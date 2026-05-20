# GSP Linux Agent

Perl-based agent that receives signed RPC calls from the GameServer Panel (GSP) and launches customer servers on Linux hosts. It replaces the upstream OGP agent with our service wrappers, stats hooks, and documentation.

## Features

- TLS-ready RPC listener (default port 12679/TCP)
- GNU Screen process management + PID tracking
- SteamCMD helpers for installing/updating games
- Optional resource stats reporting to MySQL
- Systemd service definitions and bootstrap scripts

## Install (Ubuntu example)

```bash
sudo apt update
sudo apt install -y git curl rsync screen perl libxml-parser-perl libpath-class-perl
sudo git clone https://github.com/GameServerPanel/GSP-Agent-Linux.git /opt/gsp-agent
cd /opt/gsp-agent
sudo bash install.sh
sudo bash agent_conf.sh -s "root-password" -u ogp_agent
```

After running `agent_conf.sh`, edit `/home/ogp_agent/Cfg/Config.pm` so `listen_ip`, `listen_port`, `key`, and `web_api_url` match the server entry you created inside the GSP web panel.

## Documentation

Offline instructions, upgrade notes, and troubleshooting tips live under [`documentation/agent-guide.md`](documentation/agent-guide.md). Import that file into your wiki if you need a browsable version.

## Related projects

- [GSP](https://github.com/GameServerPanel/GSP) – The web panel that issues commands to this agent.
- [GSP-Agent-Windows](https://github.com/GameServerPanel/GSP-Agent-Windows) – Windows counterpart with Task Scheduler wrappers.

## Contributing

Pull requests are welcome. Please keep Perl code formatted with `perltidy`, validate new service files on a staging host, and document behavior changes in `documentation/agent-guide.md`.
