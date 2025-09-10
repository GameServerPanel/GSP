#!/usr/bin/env python3
# collector.py  (place in gameserver root; run via cron)

import os, socket, time, subprocess
from datetime import datetime, timezone
from pathlib import Path
import psutil
import mysql.connector

# ======== CONFIG (edit for YOUR PANEL DB) =========
DB_HOST = "127.0.0.1"
DB_USER = "panel_user"       # your panel DB user
DB_PASS = "REPLACE_ME"
DB_NAME = "panel_database"   # your panel DB name (not a new DB)
TABLE_PREFIX = "gsp_"        # don't change unless you want a different prefix
# ================================================

BASE_DIR = Path(__file__).resolve().parent
DISK_PATH = str(BASE_DIR)
NET_IFACE = None
MACHINE_ID = os.environ.get("GS_MACHINE_ID") or socket.gethostname()
CPU_SAMPLE_DELAY = 0.5

def utc_now():
    return datetime.now(timezone.utc).replace(tzinfo=None)

def get_default_iface():
    if NET_IFACE:
        return NET_IFACE
    try:
        with open("/proc/net/route") as f:
            for line in f.readlines()[1:]:
                parts = line.strip().split()
                if len(parts) >= 11 and parts[1] == '00000000' and int(parts[3], 16) & 2:
                    return parts[0]
    except Exception:
        pass
    stats = psutil.net_if_stats()
    for name, st in stats.items():
        if st.isup:
            return name
    return None

def get_folder_size_bytes(path: Path) -> int:
    try:
        res = subprocess.run(["du", "-sb", str(path)], capture_output=True, text=True, check=True)
        return int(res.stdout.split()[0])
    except Exception:
        total = 0
        for root, dirs, files in os.walk(path, followlinks=False):
            for fn in files:
                fp = os.path.join(root, fn)
                try: total += os.path.getsize(fp)
                except Exception: pass
        return total

def connect_db():
    return mysql.connector.connect(
        host=DB_HOST, user=DB_USER, password=DB_PASS, database=DB_NAME, autocommit=True
    )

def ensure_machine(db, hostname):
    cur = db.cursor()
    cur.execute(
        f"INSERT IGNORE INTO {TABLE_PREFIX}machines (machine_id, hostname) VALUES (%s, %s)",
        (MACHINE_ID, hostname),
    )
    cur.close()

def insert_machine_sample(db, ts, load1, load5, load15, cpu_pct, vm, swap, du, iface, rx, tx, speed):
    cur = db.cursor()
    cur.execute(f"""
        INSERT INTO {TABLE_PREFIX}machine_samples
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
        mi = proc.memory_info(); mem_pct = proc.memory_percent()
    except Exception:
        mi = None; mem_pct = None
    rss = mi.rss if mi else None; vms = mi.vms if mi else None
    try:
        io = proc.io_counters(); rd, wr = io.read_bytes, io.write_bytes
    except Exception:
        rd = wr = None
    try:
        fds = proc.num_fds() if hasattr(proc, "num_fds") else None
    except Exception:
        fds = None
    ports = set()
    try:
        for c in proc.connections(kind="inet"):
            try:
                if c.status == psutil.CONN_LISTEN or c.type == psutil.SOCK_DGRAM:
                    if c.laddr and c.laddr.port: ports.add(str(c.laddr.port))
            except Exception: pass
    except Exception: pass
    ports_str = ",".join(sorted(ports)) if ports else None
    cmd_str = None
    try:
        cmd = proc.cmdline()
        if cmd: cmd_str = " ".join(cmd)[:1024]
    except Exception: pass

    cur = db.cursor()
    cur.execute(f"""
        INSERT INTO {TABLE_PREFIX}process_samples
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
    try:
        load1, load5, load15 = os.getloadavg()
    except Exception:
        load1=load5=load15=0.0

    # discover servers
    server_dirs = [p for p in BASE_DIR.iterdir() if p.is_dir() and not p.name.startswith('.')]
    server_procs = {str(d): [] for d in server_dirs}

    plist = []
    for p in psutil.process_iter(attrs=["pid","name","cwd","exe","cmdline"]):
        try:
            _ = p.status()
            p.cpu_percent(interval=None)
            plist.append(p)
        except Exception:
            pass

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

    time.sleep(CPU_SAMPLE_DELAY)
    proc_cpu = {}
    for p in plist:
        try: proc_cpu[p.pid] = p.cpu_percent(interval=None)
        except Exception: proc_cpu[p.pid] = None

    vm = psutil.virtual_memory()
    swap = psutil.swap_memory()
    du = psutil.disk_usage(DISK_PATH)
    cpu_pct = psutil.cpu_percent(interval=0.0)

    db = connect_db()
    ensure_machine(db, hostname)
    insert_machine_sample(db, ts, load1, load5, load15, cpu_pct, vm, swap, du, iface, rx, tx, iface_speed)

    for sdir, procs in server_procs.items():
        server_name = Path(sdir).name
        folder_size = get_folder_size_bytes(Path(sdir))
        for p in procs:
            insert_process_sample(db, ts, server_name, sdir, p, proc_cpu.get(p.pid), folder_size)

    db.close()

if __name__ == "__main__":
    main()
