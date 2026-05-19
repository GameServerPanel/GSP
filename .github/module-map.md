# GSP Module & Interaction Map

This file captures how the control panel, storefront, agents, and helper scripts talk to one another. Read it before diving into any subsystem—most regressions last time came from touching one module without realizing who consumed its data.

## Core runtime (shared by every module)

| Area | Key files | Responsibilities | Downstream callers |
| --- | --- | --- | --- |
| Database bootstrap | `Panel/includes/functions.php`, `Panel/includes/database_mysqli.php` | Creates the `OGPDatabase` instance and exposes helpers such as `resultQuery()`, `addGameHome()`, and logging. | Every panel page, `Website/Website/includes/panel_bridge.php`, cron jobs. |
| Session helpers | `includes/helpers.php` (`startSession()`) | Sets `session_name('opengamepanel_web')`, sanitizes request vars, loads locales. | `index.php`, `home.php`, provisioning pages, storefront session bridge. |
| Remote control | `Panel/includes/lib_remote.php` | Wraps agent RPC (install/update, FTP user management, rsync, SteamCMD). | `Panel/modules/gamemanager/*`, `Website/create_servers.php`, cron jobs. |
| XML parser | `Panel/modules/config_games/server_config_parser.php` | Converts `Panel/modules/config_games/server_configs/*.xml` into PHP arrays used for provisioning and pricing metadata. | `Panel/modules/gamemanager`, `Website/` (catalog + provisioner), cron installers. |
| API surface | `ogp_api.php`, `includes/api_functions.php` | HTTP API for third-party tooling. Exposes operations such as starting/stopping homes, querying stats. | Mobile apps, automated provisioning, selected billing workflows. |
| Cron/automation | `scripts/` (`cron-shop.php`, `status/*`, etc.) | Suspends/unsuspends services, refreshes status caches, runs backups. | Triggered via system cron or panel scheduler. |

## High-level flows

1. **Auth/session** – Driven by `index.php` (panel) and `Website/login.php` (storefront). Both set `$_SESSION['user_id']`, `users_login`, `users_group`, and `website_user_id`. The shared session cookie `opengamepanel_web` means logging into either surface immediately authenticates the other.
2. **Catalog** – `modules/config_games` hosts XML definitions. Panel modules (`gamemanager`, `config_games`) and storefront pages (`serverlist.php`, `order.php`, documentation pages, and the XML-notes mirror) parse these files for display and provisioning metadata.
3. **Provisioning** – Orders land in `gsp_billing_orders`. `Website/create_servers.php` allocates homes, assigns nodes/IPs, configures mods, kicks off SteamCMD/rsync/manual installers, and then syncs the resulting `home_id` back into `billing_orders`, `billing_invoices`, and `billing_transactions` so paid services never stay orphaned. The same provisioner is invoked by:
   - PayPal capture endpoint (`Website/api/capture_order.php`).
   - Panel module page `home.php?m=billing&p=provision_servers`.
   - Cron/repair actions in `Website/cron-shop.php`.
4. **Renewals** – `cron-shop.php` inspects `billing_orders.end_date` and toggles `status` between `installed`, `invoiced`, `suspended`, and `deleted`. PayPal renewals extend `end_date` in `capture_order.php` and immediately flip `status` back to `installed`.
5. **Documentation** – `Website/docs.php`, per-game folders under `Website/docs/`, and the XML wiki mirror (PHP port of `XML-Notes`) are used by both admins and AI helpers to craft game templates.

## Panel modules (selected)

| Module | Key files | Primary responsibilities | Upstream/Downstream dependencies |
| --- | --- | --- | --- |
| `dashboard` | `Panel/modules/dashboard/dashboard.php` | Landing page once authenticated. Pulls stats from homes, invoices, and support modules. Shows "Last updated" footer based on `Website/timestamp.txt`. | Reads `billing_orders`, `game_homes`, `tickets`. |
| `gamemanager` | `Panel/modules/gamemanager/server_monitor.php`, `Panel/modules/gamemanager/game_monitor.php` | Shows owned homes, start/stop, update, reinstall, port usage. Uses XML to know command lines. | Relies on `lib_remote`, `config_games`, `user_games` assignments. |
| `config_games` | `modules/config_games/add_mod.php`, `server_config_parser.php`, XML files under `server_configs/` | Admin UI for XML definitions. Controls what appears in storefront/service catalog. | Feeds `gamemanager`, billing catalog, cron installers. |
| `steam_workshop` | `modules/steam_workshop/admin.php`, `user.php`, `Panel/includes/functions.php`, `navigation.xml` | Admin profile defaults + per-home mod management. Profile defaults can now be refreshed from game XML and the user route is explicitly exposed via `p=user`. | Uses `config_games` XML metadata + `server_homes`/assignment tables; feeds workshop agent updater. |
| `user_games` | `modules/user_games/add_home.php`, `assign_home.php`, `edit_home.php` | Admin workflow to add homes manually or edit assignments. Shares DB tables with billing provisioner. | Uses `game_homes`, `remote_servers`, `billing_orders`. |
| `administration` / `user_admin` | CRUD around users, groups, permissions, expire dates. `modules/administration/panel_update.php` now also runs repository-layout-aware panel updates, preflight checks, updater self-refresh, backup/rollback for Panel+Website, patch execution, and Apache path scan/fix helpers. | Sets roles consumed by storefront admin guard and provisioning ACLs; writes update lifecycle traces to root `logs/update_trace.log` and patch state via `modules/update/patches` + `update_patches` tracking. |
| `server` | `modules/server/*` | Remote server management (agents, IPs, ports, reinstall keys). Billing uses these tables for available nodes/locations. |
| `modulemanager` | Manage module install/uninstall/menus. Billing module registers `navigation.xml` to surface `create_servers.php` & admin pages. |
| `tickets`, `support` | Support ticketing/email utilities. | Pulls user info and logger records. |
| `extras`, `addonsmanager` | Workshop/add-on management. Server Content workshop installs now share validation/runtime helpers across admin, user, and API flows, and sync bundled workshop scripts into each home’s `gsp_server_content/scripts/workshop/` directory before execution. | Hooks into game homes after provisioning and uses agent-side SteamCMD copy/install workflows. |
| `litefm`, `ftp`, `TS3Admin` | File managers and TeamSpeak controllers. | Depend on homes and remote server credentials set during provisioning. |
| `news`, `circular`, `faq` | Content modules for panel UI. | Use standard MVC wrappers, share session/auth. |
| `cron` | Scheduler UI feeding `scripts/` commands. | Maintains job metadata that OS cron reads, including scheduler-triggered Server Content actions via `ogp_api.php?server_content/run_scheduled_action` and `modules/addonsmanager/server_content_actions.php`. |

## Storefront (`Panel/modules/billing` runtime + `Website/` compatibility wrappers)

| Area | Key files | Notes |
| --- | --- | --- |
| Public pages | `Panel/modules/billing/index.php`, `serverlist.php`, `order.php`, `cart.php`, `payment_success.php`, `docs.php` | Runtime now lives under `Panel/modules/billing`. `Website/*.php` wrappers proxy legacy paths to these files. |
| Auth | `Panel/modules/billing/login.php`, `register.php`, `reset_password.php`, `forgot_password.php`, `includes/login_required.php`, `includes/admin_auth.php` | Share `opengamepanel_web` session, call into panel DB to validate roles. |
| Admin | `Panel/modules/billing/admin.php`, `adminserverlist.php`, `admin_orders.php`, `admin_coupons.php`, `admin_config.php`, `my_orders_panel.php` | Manage services, coupons, prices, and provisioning. `adminserverlist.php` controls service availability per node. |
| PayPal API | `Panel/modules/billing/api/create_order.php`, `api/capture_order.php`, `paypal/webhook.php`, `logs/payment_capture.log` | Implements REST checkout. Legacy `Website/api/*` and `Website/paypal/webhook.php` wrappers proxy to module runtime. |
| Provisioning bridge | `Panel/modules/billing/create_servers.php`, `includes/provisioner.php`, `includes/panel_bridge.php` | Shared between panel module and storefront backend. Encapsulates whole server creation/renewal pipeline. |
| Cron helpers | `Panel/modules/billing/cron-shop.php`, `diag_remote.php` | Automations for renewals, diagnostics, health checks. |
| Documentation | `Panel/modules/billing/docs.php`, `docs/*`, `docs/admin_xml_notes.php` (PHP mirror of XML wiki) | Provide guidance for editing XML and game configs directly inside repo. |
| Logs/data | `Panel/modules/billing/logs/`, `data/`, `timestamp.txt` | Payment JSON archives, debug traces, and runtime timestamp (synced from canonical `Website/timestamp.txt`). |

## External/agent side

| Component | Location | Purpose |
| --- | --- | --- |
| Remote agent | `Panel/modules/gamemanager` talks to standalone agent binaries configured per `remote_servers`. | Executes installs, updates, start/stop commands. Provisioner relies on it for SteamCMD and rsync workflows. |
| Apache/Nginx vhosts | `/etc/apache2/sites-available` (not in repo) | Point either the storefront domain or panel subpath at `Website/`. Required for shared session cookie scope. |

## Data touchpoints

- **Users** – `gsp_users` table is shared. Registration uses `Website/register.php`, admin pages use `Panel/modules/user_admin`. Password upgrades must not break panel logins.
- **Billing tables** – `gsp_billing_services`, `gsp_billing_orders`, `gsp_billing_invoices`, `gsp_billing_coupons`. Admin edits (pricing, enable/disable, locations) are done via `adminserverlist.php`; automation uses `cron-shop.php`.
- **Homes/Mods/IPs** – Stored in `gsp_game_homes`, `gsp_game_mods`, `gsp_remote_server_ips`. Provisioner writes to these tables; `gamemanager`, `litefm`, `ftp`, and `user_games` read them.
- **Logging** – `$db->logger()` writes to `ogp_logs`. Storefront-specific logs live in `Website/logs/` for quick inspection (payment capture, provisioning outcomes, coupon usage).

## Usage tips

1. **Need a DB object inside `Website/`?** Include `Website/includes/panel_bridge.php` and call `billing_get_panel_db()`. It sets up constants, loads helpers, and caches the `OGPDatabase` instance so multi-call flows (e.g., capture → provision → email) reuse it.
2. **Want to change provisioning?** Update `Website/includes/provisioner.php` once. `create_servers.php`, PayPal webhooks, cron jobs, and admin repair flows all use it.
3. **Working on XML or documentation?** Update the XML file under `Panel/modules/config_games/server_configs/`, regenerate docs if needed, and keep the PHP XML-notes mirror (`Website/docs/xml_notes.php`) accurate so the admin link stays trustworthy.
4. **Need to know who uses a table?** Search `.github/module-map.md` first; the table above lists the canonical readers/writers for each major schema.

_Last updated: 2025-11-20._
