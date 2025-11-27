# GSP (GameServerPanel) – Copilot Instructions

**Repo of truth:** `GameServerPanel/GSP`, branch `Panel-unstable`.  
**Prime directive:** Read this document first. Keep `.github/agent.md` identical to this file—any edit here must be mirrored there in the same commit.

## Workspace deliverables (WDS 2025 refresh)
- Use the GSP/WDS/GSW branding across UI, docs, and comments; when heritage context matters add: “GSP is a heavily customized fork of OGP maintained by WDS.”
- Keep `bootstrap/` current: Ubuntu 24.04 panel + agent installers, Windows Server 2019 (Cygwin) agent installer + service wrapper, and the optional `docker/compose.yml` dev stack. All scripts must be idempotent, echo next steps, and document verify/rollback procedures in their README files.
- Author and maintain admin-only docs in `WDS_Website/content/projects/gsp.md` and `content/docs/gsp/*` (front-matter + the **Admin Documentation** banner). Cross-link these guides from panel features that need deep dives.
- Refresh user-facing help in `modules/faq/` (RSS + UI) so the seven core topics—panel basics, file browser, Adminer/MySQL, FTP/SFTP, task scheduler, sub-users, and support—link to the latest WDS admin docs.
- Track cross-repo progress in `WDS-Team/docs/wds-gsp-migration.md` and update `.github/copilot-instructions.md` + `.github/agent.md` in lockstep whenever these guardrails change.
- Enforce the shared security settings everywhere: SSH port 12322, MySQL accounts `localuser@localhost` and `remoteuser@<reporter-ip>`, shared secret stored at `/home/gameserver/tools/.password`, and mention optional DR/monitoring tools in admin docs when relevant.

## Deployment model & paths
- `modules/billing/` houses the public storefront. Those files are always present inside the panel repo and get deployed either (a) as the root of a dedicated virtual host or (b) through the panel module loader (`home.php?m=billing`).
- Because the storefront and the control panel live in the same tree, you may include panel helpers when needed. Use the dedicated bridge include (`modules/billing/includes/panel_bridge.php`) instead of sprinkling ad-hoc `../../includes/...` calls.
- We keep Apache/Nginx vhosts pointed at `modules/billing/`, so every storefront URL must look root-relative (see critical section below). Never expose `/modules/billing` in any URL sent to a browser or external service.
- Before touching billing logic or module wiring, skim `.github/module-map.md` to remember how the panel modules depend on each other.

## CRITICAL: Website file paths and URLs (modules/billing)
- **The billing website files in `modules/billing/` are deployed at the WEBSITE ROOT when live.**
- **Never output `/modules/billing/` in any link, redirect, script tag, or webhook URL. All user-facing URLs must be root-relative**, e.g. `/payment_success.php`, `/cart.php`.
- Continue to use root-relative URLs inside HTML/JS and when building PayPal return/cancel links. The deployment tooling rewrites the document root; hardcoding `/modules/billing` breaks both standalone hosting and module embedding.

### Examples of CORRECT usage
```php
$returnUrl = $siteBase . '/payment_success.php';
header('Location: /order.php');
<form action="/add_to_cart.php" method="POST">
<a href="/my_account.php">My Account</a>
```

### Examples of WRONG usage (NEVER DO THIS)
```php
$returnUrl = $siteBase . '/modules/billing/payment_success.php';
header('Location: /modules/billing/cart.php');
<a href="/modules/billing/my_account.php">My Account</a>
```

### Exception – backend includes only
- Server-side includes may use absolute filesystem paths, but route those through the bridge helpers when panel context is required:
  - ✅ `require_once(__DIR__ . '/includes/config.inc.php');`
  - ✅ `require_once(__DIR__ . '/includes/panel_bridge.php');`
- Avoid copy/pasting panel bootstrap code; lean on the helpers already shipped inside `modules/billing/includes/`.

## 1) What to read first
- `.github/module-map.md` – living diagram of how the panel, billing site, daemons, and cron jobs talk to each other.
- `modules/billing/` – storefront runtime, payment handlers, provisioning bridge.
- `modules/config_games/server_configs/` – authoritative XML metadata for every supported game.
- `modules/` – control-panel modules (billing runs here too when embedded).
- `includes/` & `ogp_api.php` – database layer, shared helpers, remote agent operations.

## 2) Planning mode (default)
While scoping multi-file work, do **not** emit PHP/SQL/XML or run shell commands unless a maintainer explicitly says “Generate code now.” Plans should cover:
- Impacted files and rationale.
- Data mappings (tables/fields) you will touch.
- Risks, rollback notes, validation/tests.

## 3) Scope & principles
- **Single session across panel + storefront.** Every billing page must call `session_name('opengamepanel_web')` before `session_start()`. Always keep `$_SESSION['user_id']`, `$_SESSION['users_login']`, `$_SESSION['users_group']`, and `$_SESSION['website_user_id']` in sync so that logging into either surface signs the visitor into both.
- **Auth reuse.** Preferred order when verifying credentials: `users_pass_hash` (modern hash) → legacy `users_passwd` (MD5). Upgrading to a modern hash is allowed so long as panel logins keep working.
- **Bridge for panel helpers.** Use `modules/billing/includes/panel_bridge.php` to load panel classes (`OGPDatabase`, `OGPRemoteLibrary`, XML parsers) when the storefront needs to provision servers or read panel-only metadata. Do not reinvent ad-hoc copies of panel logic.
- **Storefront runtime.** Public pages continue to use mysqli with credentials from `modules/billing/includes/config.inc.php`. Provisioning steps may request an `OGPDatabase` handle from the bridge.
- **Provisioning pipeline.** Always funnel server creation or renewals through the shared provisioner (`modules/billing/includes/provisioner.php`). This helper wraps the old `create_servers.php` logic and ensures PayPal captures, cron jobs, and panel clicks all follow the same code path.
- **Catalog = XML.** Never hardcode game metadata. Parse `modules/config_games/server_configs/*.xml` at runtime; new XMLs must show up automatically.
- **Regions/Nodes = live DB.** Pull nodes/locations from the panel DB (`gsp_remote_servers`, etc.). Respect admin enable/disable flags and never mirror node lists into flat files.
- **Game XML wiki parity.** We ship a PHP-rendered version of https://github.com/OpenGamePanel/OGP-Website/wiki/XML-Notes inside `modules/billing/` (linked from the storefront admin area). Keep it updated so maintainers can edit XMLs without leaving the repo.

## 4) Functional requirements
### 4.1 Catalog (from XML)
- Parse every XML under `modules/config_games/server_configs/`.
- Normalize: game key, display name, install/update commands, default ports, mod metadata.
- XML pages (`modules/billing/docs.php` and the new XML-notes mirror) must stay in sync so AI-powered edits can cross-reference expectations.

### 4.2 Authentication & sessions
- Website registration must create/maintain panel users. Set both the legacy `users_passwd` and the modern hash column.
- Login flow must hydrate the shared session variables so `home.php` immediately recognizes the visitor.
- All storefront guards should treat `$_SESSION['user_id']` as the source of truth, falling back to `website_user_id` only for older sessions.

### 4.3 Checkout → PayPal → Provisioning
- Flow: add to cart → invoices (`billing_invoices`) → PayPal order (`api/create_order.php`) → capture (`api/capture_order.php`) → immediately hand off to `BillingProvisioner`.
- Mark invoices paid **only** after verifying PayPal response/webhook. Support multiple servers per payment: loop through every paid invoice and either create a new order or extend an existing service.
- For renewals, extend `end_date` from its current value and keep status at `installed`. For new services set status `installing`, invoke the provisioner, then switch to `installed` on success.
- Provisioner is responsible for calling `modules/billing/create_servers.php` logic, adding homes, assigning ports, enabling FTP, and logging/notifications. Never bypass it.

### 4.4 Regions/Nodes (multi-remote)
- `remote_servers` and `remote_server_ips` tables remain the source for available locations. Admin tooling (`adminserverlist.php`) must let staff toggle availability and restrict services per location.
- When a node is globally disabled it must disappear (or show as unavailable) in ordering and admin tools.

### 4.5 Billing automation (website-side)
- Cron/workers under `modules/billing/cron-shop.php` still suspend/delete expired services. Renewals triggered via PayPal must update `billing_orders.status` and `end_date` consistently so cron jobs can pick up where they expect.
- Keep audit logs in `modules/billing/logs/` whenever automatic provisioning, renewals, refunds, or coupon adjustments happen.

## 5) Data model alignment (no DDL during planning)
- Use panel tables as the source of truth (`gsp_billing_orders`, `gsp_billing_services`, `gsp_billing_invoices`, `gsp_game_mods`, etc.).
- Multi-remote fields (`remote_server_id`, IP IDs) already exist—never introduce duplicates in the storefront DB.
- When you truly need schema changes, follow the naming conventions, provide migrations under `modules/billing/*.sql`, and describe the plan first.

## 6) Coding standards & security
- Parameterize SQL or escape inputs with mysqli real_escape-string helpers.
- Harden sessions (regenerate IDs on login, honor `modules/billing/timestamp.txt` for public timestamps).
- CSRF-protect every POST/DELETE-like operation in the storefront admin.
- Verify PayPal signatures, never trust client-side status.
- XML parsing: disable external entities, enforce file size limits.
- Observability: keep per-request IDs in `logs/` to trace provisioning attempts.
- Licensing: leave upstream license headers intact.

## 7) Validation checklist
- Read `.github/module-map.md`, `modules/billing/`, panel helpers, and XMLs before proposing architecture changes.
- Confirm catalog pages only use XML metadata.
- Confirm node selectors reflect current DB state (respect enabled flags).
- Test that logging into either the panel (`index.php`) or storefront (`modules/billing/login.php`) logs you into both.
- PayPal capture should mark invoices paid, create/extend orders, and schedule provisioning instantly. Verify multi-item carts create all services.
- `BillingProvisioner` must be exercised via PayPal capture, panel module (`create_servers.php`), and any admin “retry” buttons.
- Documentation admin links must expose the XML-notes PHP mirror and the game docs browsers.
- Timestamp footer requirement (see below) satisfied whenever site content changes.

## 8) Deliverables for Copilot
- Concise change plan with:
  - Files to touch and why.
  - Data tables/fields involved.
  - UX notes (new buttons, admin affordances, etc.).
  - Risks, rollback, and testing.
- Update `CHANGELOG.md` with a short, high-signal entry.
- Append one actionable line to `docs/COPILOT_TODO.md` if UI follow-ups remain.
- Keep `.github/module-map.md` current whenever inter-module behavior changes.

## 9) Prohibited while in planning mode
- No PHP/SQL/XML snippets.
- No shell commands or tooling setup instructions.
- No auto-generated diff dumps.

---

## Additional UI requirement: "Last updated" footer on key pages

When making small content or page edits to the website, ensure the following pages display a human-friendly "Last updated" timestamp at the very bottom of the page (visible to site visitors):

- `modules/billing/index.php`
- `index.php` (site root)
- `modules/dashboard/dashboard.php`

Requirements:
- The text must read exactly: "Last updated at YYYY-MM-DD HH:MM:SS" (24-hour time) where the timestamp reflects the deliberate edit time of the page (see acceptance criteria below).
- Place the timestamp in the page footer area so it does not break layout on mobile or desktop. Keep styling minimal and consistent with the existing footer typography.
- Use the server/local timezone for the timestamp and include the date and time in the format above. Do not include timezone abbreviations in the UI; internal logs may record timezone if needed.

Acceptance criteria:
- Visiting each page shows the "Last updated at" line at the very bottom of the rendered HTML.
- The timestamp matches the time the page's source was last edited (file modification time) or the annotated edit time used by the deployment process. The project maintainer must decide which of these sources is canonical; document the choice in the change plan.
- The line is visible and readable on small screens and does not overlap other UI elements.

Testing checklist:
- Manually open each page and confirm the timestamp is present.
- After making a small edit and deploying, confirm the timestamp updates to the new edit time.
- If using automated deploys, ensure the deploy process preserves or updates the canonical timestamp source (e.g., touch file, update metadata) so the displayed value is accurate.

Maintainer update requirement:
- The canonical human-friendly timestamp is stored in `modules/billing/timestamp.txt` and MUST be updated whenever site files or content are edited and deployed.
- Format and wording: use a single-line plain-text entry such as: "Last Updated at 7:25am on 2025-15-11". This exact text (including capitalization) is what appears in theme footers.
- Update process: include the `timestamp.txt` change in the same commit/PR as any content change that should alter the "Last Updated" time, or ensure your deployment process updates the file automatically (for example, a post-deploy hook that writes the current deploy time in the agreed format).
- Rationale: themes are non-PHP files and may not support SSI on all servers; keeping a single canonical plain-text file reduces duplication and avoids server-side includes.


**End of Copilot Instructions.**
