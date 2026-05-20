# Server Content Manager — Roadmap & Safety Review

> **Module:** `Panel/modules/addonsmanager`  
> **Status:** Phase 1 complete — UI/language cleanup, category map, VARCHAR(32) migration, installer documentation  
> **Branch:** Panel-unstable  
> **Maintained by:** WDS (GSP is a heavily customized fork of OGP)

---

## 1. Current Behaviour Summary

The **Addons Manager** (now labelled "Server Content Manager" in the UI) lets
admins define downloadable content items that can be pushed to game server
homes by users.

### Flow

```
Admin creates Server Content item  (addons_manager.php)
  └─> stored in OGP_DB_PREFIXaddons

User visits game monitor
  └─> monitor_buttons.php checks for content items for that game type
  └─> "Server Content (N)" button appears

User clicks button
  └─> user_addons.php — shows available category links
  └─> addons_installer.php?addon_type=<type>
        └─> user picks a specific item
        └─> state=start → agent.start_file_download(url, path, filename, "uncompress")
        └─> optional post_script runs on the agent after extraction
        └─> page auto-refreshes to show download/script progress
```

---

## 2. Database Schema (db_version 4 — Phase 2)

### `OGP_DB_PREFIXaddons` (content item catalogue)

| Column                | Type            | Default       | Description |
|-----------------------|-----------------|---------------|-------------|
| addon_id              | INT UNSIGNED PK | auto          | Primary key |
| name                  | VARCHAR(80)     |               | Display name |
| url                   | VARCHAR(200)    |               | Download URL (zip / tar.gz / API endpoint) |
| path                  | VARCHAR(80)     |               | Relative target path inside server home |
| addon_type            | VARCHAR(32)     |               | Category key (plugin / mappack / config / …) |
| home_cfg_id           | VARCHAR(7)      |               | Linked game config ID |
| post_script           | LONGTEXT        |               | Bash script run by agent after install |
| group_id              | INT(11) NULL    |               | Restrict to a specific user group |
| install_method ¹      | VARCHAR(32)     | download_zip  | How the content is delivered |
| content_version ¹     | VARCHAR(64) NULL|               | Version tag shown in manifest |
| requires_stop ¹       | TINYINT(1)      | 1             | Must server be stopped before install? |
| backup_before_install¹| TINYINT(1)      | 1             | Back up target path before installing? |
| restart_after_install¹| TINYINT(1)      | 0             | Restart server after successful install? |
| is_cacheable ¹        | TINYINT(1)      | 0             | Safe for cross-server copy / shared cache? |
| description ¹         | TEXT NULL       |               | Short description shown to users |

¹ Added in db_version 4 (Phase 2).

### `OGP_DB_PREFIXserver_content_manifest` (Phase 2 — per-server installed state)

One row per (home_id, addon_id) pair.  Updated on every successful install.

| Column          | Type             | Description |
|-----------------|------------------|-------------|
| id              | INT UNSIGNED PK  | Auto primary key |
| home_id         | INT              | Server home |
| addon_id        | INT              | Installed content item |
| install_method  | VARCHAR(32)      | Method used for the install |
| content_version | VARCHAR(64) NULL | Version tag at time of install |
| install_state   | VARCHAR(32)      | installed / failed / removed |
| checksum_sha256 | VARCHAR(64) NULL | SHA-256 of the installed archive |
| source_url      | VARCHAR(255) NULL| URL used for the install |
| installed_at    | DATETIME         | Timestamp of last successful install |
| installed_by    | INT NULL         | User ID who performed the install |
| updated_at      | DATETIME NULL    | Last update time |
| notes           | TEXT NULL        | Free-text admin notes |

### `OGP_DB_PREFIXserver_content_install_history` (Phase 2 — install log)

One row per install attempt.  Never deleted (audit trail).

| Column          | Type              | Description |
|-----------------|-------------------|-------------|
| id              | INT UNSIGNED PK   | Auto primary key |
| home_id         | INT               | Server home |
| addon_id        | INT               | Content item installed |
| installed_by    | INT NULL          | User who triggered the install |
| started_at      | DATETIME          | When the install was initiated |
| completed_at    | DATETIME NULL     | When the install completed |
| install_state   | VARCHAR(32)       | started / installed / failed / cancelled |
| install_method  | VARCHAR(32) NULL  | Method used |
| content_version | VARCHAR(64) NULL  | Version tag |
| source_url      | VARCHAR(255) NULL | Source URL used |
| cache_mode_used | VARCHAR(32) NULL  | Value of server_content_cache_mode at install time |
| result_code     | INT NULL          | Script exit code (0 = success) |
| log_output      | MEDIUMTEXT NULL   | Script / download log excerpt |

---

## 3. Existing Flow: user_addons.php → addons_installer.php

1. `user_addons.php` queries all content items for the server's `home_cfg_id`.
2. It groups items by `addon_type` and renders one link per category.
3. `addons_installer.php` (page key: `addons`) receives `addon_type` and
   `home_id` in the query string.
4. On first load (no `state`), it renders a dropdown of available items.
5. On submit (`state=start`), it calls `$remote->start_file_download()` and
   begins polling.
6. Subsequent loads with `state=refresh` poll the agent for download progress
   and script log output.

---

## 4. Current post_script Replacement Variables

| Variable           | Replaced with                                                |
|--------------------|--------------------------------------------------------------|
| `%home_path%`      | Absolute filesystem path of the server home directory        |
| `%home_name%`      | Human-readable name of the server home                       |
| `%control_password%` | RCON / control password for this server instance           |
| `%max_players%`    | Maximum player count for this mod slot                       |
| `%ip%`             | IP address bound to this server                              |
| `%port%`           | Game port                                                    |
| `%query_port%`     | Query/status port (derived from game XML rules)              |
| `%incremental%`    | Internal incremental counter for this mod/home combination   |

All replacements are case-insensitive (`preg_replace … /i`).

---

## 5. Security Concerns

### Current risks

1. **No path validation in the panel** — the `path` field is passed directly
   to the agent without checking for `../`.  The agent is the last line of
   defence.  A malicious admin could craft a path that escapes the home
   directory if the agent's validation is insufficient.
   
2. **SQL injection in filter queries** — `addon_type` is interpolated into
   SQL strings in several places.  A whitelist check via `in_array()` against
   the registered category keys prevents injection, but this must remain in
   place whenever new query sites are added.

3. **post_script is admin-only but powerful** — admins write arbitrary bash.
   This is intentional; users cannot supply scripts.  However, the variable
   substitution should be audited to ensure no user-controlled value (e.g.
   a server name containing shell metacharacters) can affect the script.

### Recommended hardening (next phase)

- Add explicit `../` stripping / validation of `path` on the panel side before
  sending to the agent.
- Sanitise all `%variable%` substitution inputs (strip shell metacharacters
  from home_name, ip, port before substitution).
- Consider signing or hashing the post_script blob to detect tampering.
- Rate-limit install actions per user to prevent abuse.

---

## 6. Proposed Next Database Fields

```sql
ALTER TABLE OGP_DB_PREFIXaddons
  MODIFY addon_type VARCHAR(32) NOT NULL,          -- already applied in db_version 2
  ADD COLUMN install_method    VARCHAR(32) NOT NULL DEFAULT 'download_zip',
  ADD COLUMN content_version   VARCHAR(64) NULL,
  ADD COLUMN requires_stop     TINYINT(1)  NOT NULL DEFAULT 1,
  ADD COLUMN backup_before_install TINYINT(1) NOT NULL DEFAULT 1,
  ADD COLUMN restart_after_install TINYINT(1) NOT NULL DEFAULT 0,
  ADD COLUMN is_profile        TINYINT(1)  NOT NULL DEFAULT 0,
  ADD COLUMN description       TEXT NULL;
```

Apply this as `$install_queries[2]` (db_version 3) in `module.php` when ready.

---

## 7. Proposed Install Methods

| install_method    | Description                                                         |
|-------------------|---------------------------------------------------------------------|
| `download_zip`    | Download a .zip / .tar.gz and extract into the server path (current default) |
| `download_file`   | Download a single file (no extraction) into the server path        |
| `post_script`     | Run only the post_script — no download, no extraction              |
| `steam_workshop`  | Pass Workshop item IDs to the agent's `steamcmd +workshop_download_item` helper |
| `minecraft_jar`   | Download a server jar from Mojang / Paper / Purpur / Fabric APIs   |
| `profile_copy`    | Copy a stored profile directory tree into the server home          |

---

## 8. Proposed Categories (server_content_categories.php)

| addon_type  | Display label       | Notes                              |
|-------------|---------------------|------------------------------------|
| `plugin`    | Plugins / Mods      | Original — always present          |
| `mappack`   | Map Packs           | Original — always present          |
| `config`    | Config Packs        | Original — always present          |
| `version`   | Server Versions     | e.g. Minecraft jar switcher        |
| `modpack`   | Modpacks            | CurseForge / ATLauncher packs      |
| `workshop`  | Workshop Content    | Steam Workshop (requires VARCHAR(32)) |
| `script`    | Scripted Installer  | Admin-defined script only          |
| `profile`   | Server Profiles     | Full profile: configs + mods + scripts |

---

## 9. Recommended Phased Migration Plan

### Phase 1 (complete)
- [x] UI labels renamed to "Server Content Manager / Server Content".
- [x] Central category map created (`server_content_categories.php`).
- [x] `addon_type` column expanded to VARCHAR(32) via db_version 2 migration.
- [x] `addons_installer.php` and `user_addons.php` use category map for validation.
- [x] Full TODO/comment blocks added to installer for next phase work.
- [x] Module folder, table names, URL routes, function names unchanged.
- [x] Workshop Content Phase 1: manual Workshop ID entry, per-server manifest,
      agent script runner, install/update/remove actions (`db_version 3`).

### Phase 2 — Schema & install_method support
- [x] Apply Phase 2 schema: `install_method`, `content_version`, `requires_stop`,
      `backup_before_install`, `restart_after_install`, `is_cacheable`, `description`
      columns added to `addons` table via `$install_queries[3]` (db_version 4).
- [x] `server_content_manifest` table created – tracks one row per installed
      content item per server home (version, checksum, state, installed_by).
- [x] `server_content_install_history` table created – one row per install
      attempt with result code, log snippet, and cache_mode_used.
- [x] `install_method` dropdown added to admin create/edit form.
- [x] `content_version`, `description`, `is_cacheable` fields added to form.
- [x] `requires_stop` / `backup_before_install` / `restart_after_install`
      checkboxes added to admin form.
- [x] `requires_stop` check implemented in installer: blocks install if server
      is running (Phase 3 will add automatic stop/start).
- [x] Install history row written on `state=start`; completed on success.
- [x] Manifest row upserted on successful install.
- [x] `server_content_cache_mode` panel setting added (disabled / search_existing_servers /
      shared_cache / shared_cache_and_search; default: **disabled**).
- [x] `is_cacheable` flag stored per content item; cache mode and is_cacheable
      guard are in place — actual cross-server/cache copy logic is Phase 3.
- [ ] `backup_before_install` agent call (Phase 3 — needs agent tar/zip helper).
- [ ] `restart_after_install` agent call (Phase 3 — needs server start after install).

### Phase 3 — Steam Workshop integration
See Part 6 below.

### Phase 4 — Minecraft jar / version switcher
See Part 7 below.

### Phase 5 — DayZ / Arma profile switcher
See Part 8 below.

---

## 10. Part 6: Steam Workshop Integration

### Concept
Steam Workshop content is treated as a Server Content type (`addon_type=workshop`,
`install_method=steam_workshop`).

### Browser UI
- A "Workshop Browser" page within the module fetches the workshop item list
  from Steam's Web API and lets users select items.
- Selected item IDs are stored as server content selections linked to the home.

### Agent side
- The agent runs `steamcmd +login anonymous +workshop_download_item <appid> <item_id> +quit`
  for each selected item.
- Downloaded content is moved into the correct server mod directory.
- The agent reports progress back to the panel via the existing rsync_progress mechanism
  or a new workshop_progress RPC.

### Restart behaviour (configurable per content item)
| Mode | Description |
|------|-------------|
| 1 | Install immediately if server is stopped |
| 2 | Queue installation to run on next restart |
| 3 | Restart automatically if updates are available |
| 4 | Notify only — do not install automatically |

---

## 11. Part 7: Minecraft Example

### Base game: Minecraft

### Server Content options (addon_type=version, install_method=minecraft_jar)

| Content Item      | Source API / URL                                              |
|-------------------|---------------------------------------------------------------|
| Vanilla 1.21.x    | Mojang version manifest API                                   |
| Paper 1.21.x      | papermc.io API                                                |
| Purpur 1.21.x     | purpurmc.org API                                              |
| Forge 1.20.1      | files.minecraftforge.net                                      |
| Fabric 1.20.1     | meta.fabricmc.net                                             |
| Modpack installer | CurseForge / ATLauncher / FTB API (addon_type=modpack)        |

### Install flow
1. Admin creates a content item with `install_method=minecraft_jar` and sets
   `url` to the download endpoint (or a version ID for API-resolved URLs).
2. User selects the version from the Server Content page.
3. Installer downloads the jar to the server home path.
4. post_script patches the startup command line with the new jar filename.
5. If `restart_after_install=1`, the server restarts with the new jar.

---

## 12. Part 8: DayZ / Arma Example

### Base game: Arma 2 / DayZ-capable server

### Server Content options

| Content Item | Type    | Description                                      |
|--------------|---------|--------------------------------------------------|
| DayZ Vanilla | config  | Vanilla DayZ config + mission files              |
| DayZ Epoch   | profile | Epoch mod files + config profile                 |
| Overpoch     | profile | Combined Overwatch + Epoch profile               |
| Map Pack     | mappack | Additional map files (Chernarus, Lingor, etc.)   |
| Config Pack  | config  | Server config preset (difficulty, loot tables)   |

### Install flow
1. Admin defines each option as a Server Content item with `install_method=download_zip`
   or `install_method=profile_copy`.
2. post_script copies required files, patches `mission.sqm`, `server.cfg`, etc.
3. If `requires_stop=1`, the server is stopped before applying changes.
4. If `restart_after_install=1`, the server starts with the new profile.

---

## 13. Part 9: Security Direction

### Core principles

1. **Users must not be allowed to enter arbitrary commands.**
   Admins define Server Content items including scripts.
   Users only select from the approved list.

2. **Script execution is scoped to the assigned server.**
   The post_script runs with only the target server home path and the approved
   replacement variables.  It cannot reference paths outside the home directory.

3. **All paths must be validated against the home directory boundary.**
   - Strip or reject any `../` sequences in the `path` field.
   - Reject absolute paths unless the content item is explicitly marked
     admin-only and the admin has been warned.
   - The agent enforces path containment at the OS level; the panel should
     add a redundant check as defence-in-depth.

4. **Replacement variable values must be shell-safe.**
   - Escape shell metacharacters in `home_name`, `ip`, `port`, `home_path`,
     etc. before substitution into post_script.
   - Consider wrapping each value in single quotes in the substituted script.

5. **Workshop and external API downloads must be verified.**
   - Check Content-Type and file signature/hash where possible.
   - Reject downloads that exceed a configurable size limit.

6. **Install history must be logged.**
   - Record who installed what, when, and the script exit code.
   - This log must be readable by admins but not modifiable by users.

---

## 14. Part 10: Content Reuse / Cache Mode Security Rules

### Governing setting: `server_content_cache_mode`

| Value                    | Agent behaviour |
|--------------------------|-----------------|
| `disabled` (**default**)  | Always install from the configured source/script.  No cross-server copy, no shared cache. |
| `search_existing_servers` | Agent may scan other local game-server folders for matching **cacheable** content and copy directly if the checksum matches. |
| `shared_cache`            | Agent may store **cacheable** content in a shared cache folder and use it for future installs. |
| `shared_cache_and_search` | Both `shared_cache` and `search_existing_servers` are active. |

### Content eligibility for sharing / caching

Only content where **`is_cacheable = 1`** on the addon record may ever be
shared or placed in the shared cache.  Admins must explicitly opt in per item.

**Never mark the following as cacheable:**
- Config files, server.cfg, whitelist / banlist, RCON passwords
- User-edited files, saves, player databases
- Log files, crash dumps
- Files containing credentials, tokens, or keys
- Entire server home directories

**Safe to mark cacheable (public, non-sensitive, reproducible):**
- Publicly distributed map packs (.bsp, .nav, .vmf bundles)
- Workshop content / mod archives sourced from public URLs
- Vanilla server jars (Minecraft, etc.) from official APIs
- Stock config templates (not user-edited copies)

### Checksum enforcement

When a cached or copied file is used, the agent **must** verify the SHA-256
checksum against the value stored in `server_content_manifest.checksum_sha256`
(if present).  A checksum mismatch must abort the install and fall back to the
original source download.

### Path restrictions

- The agent must never copy files **outside** approved content paths.
- Users **cannot** specify source paths.  Only admin-defined content items
  define where content comes from and where it is placed.
- Path traversal (`../`) is forbidden in all `path` fields.  The panel
  validates this; the agent provides a second layer of defence.
- The shared cache folder must be separate from any game server home and not
  accessible directly by game server processes.

### Phase 3 implementation checklist (not yet done)

- [ ] Agent-side: implement copy-from-existing-server with checksum verification.
- [ ] Agent-side: implement shared cache store and restore.
- [ ] Panel-side: validate `is_cacheable` before passing cache-mode hint to agent.
- [ ] Panel-side: expose cache hit/miss in install history log.
- [ ] Shared cache path configurable in panel settings (`server_content_cache_path`).
