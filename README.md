# GSP Windows Agent

Cygwin-based agent that lets the GameServer Panel manage Windows Server 2019/2022 hosts. It mirrors the Linux agent feature set: signed RPC transport, GNU Screen session management, and SteamCMD-aware installers.

## Highlights

- One-click installer (`Install/onceinstall_agent.bat`) that bootstraps Cygwin, required packages, and the `gameserver` service account.
- Task Scheduler entry that keeps the agent running after reboots.
- Helper scripts (`agent_conf.sh`, `rebase_post_ins.bat`, etc.) for maintaining the environment.
- Markdown documentation under [`documentation/agent-guide.md`](documentation/agent-guide.md).

## Quick start

1. Clone or download the repository to `C:\\gsp-agent`.
2. Right-click `Install\\onceinstall_agent.bat` → “Run as administrator”.
3. Open the bundled Cygwin terminal and configure the agent:
   ```bash
   cd /OGP
   bash agent_conf.sh -p "gameserverPassword"
   ```
4. Edit `C:\\OGP\\Cfg\\Config.pm` (match the settings you entered in the GSP web panel) and start the “OGP agent start on boot” scheduled task.

## Related repositories

- [GSP](https://github.com/GameServerPanel/GSP) – PHP panel that issues commands to the agents.
- [GSP_Agent_Linux](https://github.com/GameServerPanel/GSP_Agent_Linux) – Linux counterpart with systemd service files.

## Contributing

Send pull requests through GitHub. Test installer changes on a clean Windows Server VM, keep batch files in ASCII, and update `documentation/agent-guide.md` whenever you modify the workflow.
