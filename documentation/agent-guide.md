# Linux Agent Operations Guide

Packaged copy of the instructions we keep in the staff wiki so you can view them offline or import them into any other knowledge base.

## Purpose

The Linux agent (`ogp_agent.pl`) exposes the RPC endpoint that allows the GameServer Panel to install, start, stop, and monitor game servers on Linux hosts. Every host that runs customer games must run this service.

## Supported platforms

- Ubuntu 20.04/22.04/24.04 LTS
- Debian 11/12
- Rocky/AlmaLinux 8+
- Any modern distribution with Perl 5.30+, GNU Screen, and rsync

## Installation (Ubuntu example)

```bash
sudo apt update
sudo apt install -y git curl rsync screen perl libxml-parser-perl libpath-class-perl libarchive-zip-perl libhttp-daemon-perl
sudo git clone https://github.com/GameServerPanel/GSP_Agent_Linux.git /opt/gsp-agent
cd /opt/gsp-agent
sudo bash install.sh
sudo bash agent_conf.sh -s "root-password" -u ogp_agent
```

`agent_conf.sh` writes `/home/ogp_agent/Cfg/Config.pm`. Set:

| Key | Description |
| --- | ----------- |
| `listen_ip` | Interface to bind (use `0.0.0.0` unless you want to restrict access). |
| `listen_port` | TCP port exposed to the panel. Default is `12679`. |
| `key` | Shared secret copied from the panel → Administration → Game Servers. |
| `web_api_url` | HTTPS URL to `ogp_api.php` on the panel. |
| `stats_db_*` | Optional MySQL credentials for the resource stats cron. |

## Service management

```bash
sudo cp systemd/ogp_agent.service /etc/systemd/system/
sudo sed -i "s#{OGP_AGENT_PATH}#/opt/gsp-agent#g" /etc/systemd/system/ogp_agent.service
sudo systemctl daemon-reload
sudo systemctl enable --now ogp_agent
```

Logs live next to the binaries (`/opt/gsp-agent/ogp_agent.log`). Individual game servers stream to their own `console.log` files inside each home folder.

## Firewall checklist

1. Allow inbound TCP on the agent port.
2. Allow inbound/outbound UDP/TCP for the games you host.
3. Allow outbound HTTPS to the panel so the agent can talk to `ogp_api.php`.

## Upgrades

1. `cd /opt/gsp-agent && git pull`
2. Stop the service (`sudo systemctl stop ogp_agent`).
3. Re-run `bash install.sh` if new files were added.
4. Start the service (`sudo systemctl start ogp_agent`).
5. Verify the panel shows the agent as “online”.

## Troubleshooting

- `tail -f ogp_agent.log` – handshake failures usually mean the encryption key or port mismatches the panel entry.
- `journalctl -u ogp_agent` – capture Perl stack traces and missing dependency errors.
- `screen -ls` – confirm customer servers are running in screen sessions.
- `nc -vz panel.example.com 12679` from the panel host – ensures the agent port is reachable.

## Related docs

- [`GSP/documentation/admin-guide.md`](https://github.com/GameServerPanel/GSP/tree/main/documentation) – Panel-side instructions plus XML authoring notes.
- [`GSP-Agent-Windows/documentation/agent-guide.md`](https://github.com/GameServerPanel/GSP-Agent-Windows/tree/main/documentation/agent-guide.md) – Windows counterpart.
