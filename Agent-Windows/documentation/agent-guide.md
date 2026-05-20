# Windows Agent Operations Guide

This Markdown file mirrors the internal WDS wiki instructions so you can ship it with release archives or import it into any other knowledge base.

## Purpose

The Windows agent bundles Cygwin, Perl, GNU Screen, and helper scripts so the GameServer Panel can manage Windows Server 2019/2022 game hosts. It exposes the same RPC surface as the Linux agent and expects the same shared key.

## Requirements

- Windows Server 2019 or 2022 (English locale recommended)
- Administrator privileges
- Reliable network access to the panel on TCP 12679 (or whatever port you configure)
- Outbound HTTPS so the agent can talk to `ogp_api.php`

## Installation workflow

1. **Clone or download** the repository to `C:\\gsp-agent`.
2. **Run** `Install\\onceinstall_agent.bat` as Administrator. The script:
   - Installs Cygwin with Perl, rsync, wget, screen, zip/unzip, git, etc.
   - Creates the `gameserver` Windows account and grants “Log on as a service”.
   - Unpacks the latest agent files into `C:\\OGP`.
   - Registers the Windows Task Scheduler entry that boots the agent at startup.
3. **Open the bundled Cygwin terminal** and configure the agent:
   ```bash
   cd /OGP
   bash agent_conf.sh -p "gameserverPassword"
   ```
4. **Edit configuration** – `/OGP/Cfg/Config.pm` mirrors the Linux agent. Set `listen_ip`, `listen_port`, `key`, `web_api_url`, and (optionally) the stats database credentials.
5. **Start the service** – The installer already created a scheduled task (“OGP agent start on boot”). Run it immediately from Task Scheduler or execute `schtasks /Run /tn "OGP agent start on boot"`.

## Updating the agent

1. Stop the scheduled task or kill any running `ogp_agent.pl` processes.
2. Pull the latest files (`git pull` inside `C:\\gsp-agent` or download the release ZIP again).
3. Copy updated files into `C:\\OGP`.
4. Re-run `rebase_post_ins.bat` if new Cygwin DLLs were added.
5. Start the agent task again.

## Logging & troubleshooting

- Main log: `C:\\OGP\\ogp_agent.log`
- PID files: `ogp_agent_run.pid` (wrapper) and `ogp_agent.pid` (Perl daemon)
- Customer servers run inside GNU Screen sessions—attach via `C:\\OGP\\bin\\screen -r ogp_agent`
- Firewall: open TCP 12679 (or your configured port) and any game-specific ports before provisioning.
- Authentication errors almost always mean the `key` in `Cfg/Config.pm` does not match the value stored in the panel → Administration → Servers.

## Usage tips

- Always run the installer as Administrator so it can write to `Program Files`, register services, and manage the `gameserver` account.
- Keep the `team@worlddomination.dev` mailbox handy for provider login challenges when managing Windows hosts.
- The Linux agent docs live in `GSP-Agent-Linux/documentation/agent-guide.md` and the panel XML reference is in `GSP/documentation/`.
