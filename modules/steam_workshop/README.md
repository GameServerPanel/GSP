# Steam Workshop Automation (WIP)

This folder now hosts the rewritten Steam Workshop tooling for the GSP panel. The previous DayZ-only batch scripts are left untouched under `DayZ Workshop Mod Auto Update/` for historical reference, but the new MVC layer introduces adapters, XML-backed configuration, and an eventual agent scheduler.

## Milestone 1 summary

- **Controllers** – `controllers/SteamWorkshopController.php` routes the module entrypoint through a thin MVC wrapper.
- **Service layer** – `lib/SteamWorkshopService.php` loads/saves per-home XML configs under `data/configs/<home_id>.xml`, parses Modlist-style imports, and exposes adapter metadata.
- **Adapters** – `lib/GameAdapters/*.xml` define canonical behaviors for DayZ, Arma 3, ARK, Garry's Mod, and CS2. They are validated against `schema.xsd`.
- **Views** – `views/*` render the server list, edit form, and parsed mod table using localized strings from `lang/en_US.php`.
- **Data directory** – `data/configs/` stores the serialized workshop configuration for each game home.

## Editing workflow

1. Visit `home.php?m=steam_workshop&p=main` to see the list of homes you can access. Click **Configure** on any home to edit its Workshop setup.
2. Paste a Modlist.txt style payload (e.g., `1565508334,@MyMod`) into the Workshop IDs textarea.
3. Choose the adapter, interval, install strategy, and on-update action, then click **Save settings**. The controller serializes this into XML so the agent can consume it later.
4. Config files live under `modules/steam_workshop/data/configs/`. Delete a file to reset a home to defaults.

## Roadmap

- **Milestone 2** will flesh out the adapter runtime helpers and validation against the schema.
- **Milestone 3** wires the Linux/Windows agents via a new `workshop_update` RPC and scheduler, using the serialized XML from this module.
- Later milestones add dry-run/apply actions, activation writers, and safe apply hooks.

> GSP is a heavily customized fork of OGP maintained by WDS. Keep all Steam Workshop code inside this module tree so storefront, agents, and future docs stay decoupled.
