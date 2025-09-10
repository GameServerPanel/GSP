#!/usr/bin/env python3
# collector.py
#
# Runs on each machine via cron (e.g., every minute).
# Scans subfolders (each is a gameserver), finds running PIDs rooted there,
# samples process + machine stats, and writes to MySQL.

import os, socket, time, subprocess, json
from datetime import datetime, timezone
from pathlib import Path

import psutil
import mysql.connector

# ======== CONFIG (edit these) =========
DB_HOST = "127.0.0.1"
DB_USER = "gs_metrics"
DB_PASS = "REPLACE_ME"
DB_NAME = "gs_metrics"

# The folder this script lives in is the gameserver root containing subfolders.
BASE_DIR = Path(__file__).resolve().parent

# Optional: set the disk path you care about (use BASE_DIR’s filesystem)
DISK_PATH = str(BASE_DIR)

# Optional: force a specific interface name (else autodetect default route iface)
NET_IFACE = None  # e.g. "eth0"

# Identify the machine (hostname fallback if env not set)
MACHINE_ID = os.environ.get("GS_MACHINE_ID") or socket.gethostname()

# How long to wait to get stable CPU% readings (seconds)
CPU_SAMPLE_DELAY = 0.5
# =======================================

def utc_now():
    return datetime.now(timezone.utc).replace(tzinfo=None)  # naive UTC for MySQL DATETIME

def get_default_iface():
    if NET_IFACE:
        return NET_IFACE
    # Parse /proc/net/route for default route (Destination == 00000000)
    try:
        with open("/proc/net/route") as f:
            for line in f.readlines()[1:]:
                parts = line.strip().split()
                if len(parts) >= 11 and parts[1] == '00000000' and int(parts[3], 16) & 2:
                    return parts[0]
    except Exception:
        pass
    # Fallback: first "up" interface with stats
    stats = psutil.net_if_stats()
    for name, st in stats.items():
        if st.isup:
            return name
    return None

def get_folder_size_bytes(path: Path) -> int:
    # Fast and robust: use `du -sb` if available
    try:
        res = subprocess.run(["du", "-sb", str(path)], capture_output=True, text=True, check=True)
        return int(res.stdout.split()[0])
    except Exception:
        # Fallback: Python walk (slower on huge trees)
        total = 0
        for root, dirs, files in os.walk(path, followlinks=False):
            for fn in files:
                fp = os.path.join(root, fn)
                try:
                    total += os.path.getsize(fp)
                except Exception:
                    pass
        return total

def connect_db():
    return mysql.connector.connect(
        host=DB_HOST, user=DB_USER, password=DB_PASS, database=DB_NAME, autocommit=True
    )

def ensure_machine(db, hostname):
    cur = db.cursor()
    cur.execute(
        "INSERT IGNORE INTO machines (machine_id, hostname) VALUES (%s, %s)",
        (MACHINE_ID, hostname),
    )
    cur.close()

def insert_machine_sample(db, ts, load1, load5, load15, cpu_pct, vm, swap, du, iface, rx, tx, speed):
    cur = db.cursor()
    cur.execute("""
        INSERT INTO machine_samples
          (machine_id, ts, load1, load5, load15, cpu_pct,
           mem_used_bytes, mem_total_bytes, mem_used_pct,
           swap_used_bytes, swap_total_bytes,
           disk_path, disk_total_bytes, disk_used_bytes, disk_used_pct,
           net_iface, rx_bytes, tx_bytes, iface_speed_mbps)
        VALUES (%s,%s,%s,%s,%s,%s,
                %s,%s,%s,
                %s,%s,
                %s,%s,%s,%s,
                %s,%s,%s,%s)
    """, (MACHINE_ID, ts, load1, load5, load15, round(cpu_pct,2),
          vm.used, vm.total, round(vm.percent,2),
          swap.used, swap.total,
          DISK_PATH, du.total, du.used, round(du.percent,2),
          iface, rx, tx, speed))
    cur.close()

def insert_process_sample(db, ts, server_name, server_path, proc, cpu_pct, folder_size):
    try:
        mi = proc.memory_info()
        mem_pct = proc.memory_percent()  # % of system RAM
    except Exception:
        mi = None
        mem_pct = None

    rss = mi.rss if mi else None
    vms = mi.vms if mi else None

    try:
        io = proc.io_counters()
        rd, wr = io.read_bytes, io.write_bytes
    except Exception:
        rd = wr = None

    try:
        fds = proc.num_fds() if hasattr(proc, "num_fds") else None
    except Exception:
        fds = None

    # Listening ports (TCP LISTEN, UDP sockets present)
    ports = set()
    try:
        for c in proc.connections(kind="inet"):
            try:
                if c.status == psutil.CONN_LISTEN or c.type == psutil.SOCK_DGRAM:
                    if c.laddr and c.laddr.port:
                        ports.add(str(c.laddr.port))
            except Exception:
                pass
    except Exception:
        pass
    ports_str = ",".join(sorted(ports)) if ports else None

    cmd_str = None
    try:
        cmd = proc.cmdline()
        if cmd:
            cmd_str = " ".join(cmd)[:1024]
    except Exception:
        pass

    cur = db.cursor()
    cur.execute("""
        INSERT INTO process_samples
          (machine_id, ts, server_name, server_path,
           pid, proc_name, cmd, cpu_pct,
           rss_bytes, vms_bytes, mem_pct,
           io_read_bytes, io_write_bytes, open_fds,
           listening_ports, folder_size_bytes)
        VALUES (%s,%s,%s,%s,
                %s,%s,%s,%s,
                %s,%s,%s,
                %s,%s,%s,
                %s,%s)
    """, (MACHINE_ID, ts, server_name, str(server_path),
          proc.pid, proc.name()[:255] if proc.name() else None,
          cmd_str, round(cpu_pct,2) if cpu_pct is not None else None,
          rss, vms, round(mem_pct,2) if mem_pct is not None else None,
          rd, wr, fds, ports_str, folder_size))
    cur.close()

def main():
    ts = utc_now()
    hostname = socket.gethostname()
    iface = get_default_iface()
    ifaces_stats = psutil.net_if_stats()
    iface_speed = ifaces_stats.get(iface).speed if iface and iface in ifaces_stats else None
    net_counters = psutil.net_io_counters(pernic=True)
    rx = tx = None
    if iface and iface in net_counters:
        rx = net_counters[iface].bytes_recv
        tx = net_counters[iface].bytes_sent

    load1=load5=load15 = (0.0,0.0,0.0)
    try:
        load1, load5, load15 = os.getloadavg()
    except Exception:
        pass

    # Prime process CPU% and collect candidate PIDs
    # Discover servers = immediate child dirs of BASE_DIR (ignore dot dirs)
    server_dirs = [p for p in BASE_DIR.iterdir() if p.is_dir() and not p.name.startswith('.')]
    # map server -> processes
    server_procs = {str(d): [] for d in server_dirs}

    # Build a fast list of all processes once
    plist = []
    for p in psutil.process_iter(attrs=["pid","name","cwd","exe","cmdline"]):
        try:
            _ = p.status()  # touch to ensure alive
            p.cpu_percent(interval=None)  # prime
            plist.append(p)
        except Exception:
            pass

    # Associate processes to server dirs by cwd/exe/cmdline path prefix
    for d in server_dirs:
        dstr = str(d)
        for p in plist:
            try:
                cwd = p.info.get("cwd") or ""
                exe = p.info.get("exe") or ""
                cmd = " ".join(p.info.get("cmdline") or [])
                if cwd.startswith(dstr) or exe.startswith(dstr) or dstr in cmd:
                    server_procs[str(d)].append(p)
            except Exception:
                continue

    # Wait briefly so second CPU% read is meaningful
    time.sleep(CPU_SAMPLE_DELAY)

    # Re-read CPU% for each proc
    proc_cpu = {}
    for p in plist:
        try:
            proc_cpu[p.pid] = p.cpu_percent(interval=None)  # % of one CPU * cores
        except Exception:
            proc_cpu[p.pid] = None

    # Machine-wide stats
    vm = psutil.virtual_memory()
    swap = psutil.swap_memory()
    du = psutil.disk_usage(DISK_PATH)
    cpu_pct = psutil.cpu_percent(interval=0.0)

    # Insert into DB
    db = connect_db()
    ensure_machine(db, hostname)
    insert_machine_sample(db, ts, load1, load5, load15, cpu_pct, vm, swap, du,
                          iface, rx, tx, iface_speed)

    # For each server dir, capture folder size once and record each process row
    for sdir, procs in server_procs.items():
        server_name = Path(sdir).name
        folder_size = get_folder_size_bytes(Path(sdir))
        for p in procs:
            insert_process_sample(
                db, ts, server_name, sdir, p, proc_cpu.get(p.pid), folder_size
            )

    db.close()

if __name__ == "__main__":
    main()
