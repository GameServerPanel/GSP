# Changelog

## 2026-05-06
- **Billing/admin provisioning hardening:** Styled the panel Migrate action like the other server action buttons, switched admin-created billing rows to the canonical monthly/31-day default, and made paid checkout fulfillment sync `billing_orders.home_id`, `billing_invoices.home_id`, and `billing_transactions.home_id` after provisioning so paid orders no longer stay at `home_id = 0`.
- **Billing cart data correctness:** `add_to_cart.php` now calculates invoice amounts from the selected slot count and duration, stores `subtotal`/`total_due` metadata, and replaces `ChangeMe` placeholders with securely generated passwords before anything is written to billing tables.
- **PayPal/coupon idempotency:** Cart checkout now stamps PayPal `custom_id` with the exact invoice IDs being purchased, capture/free/webhook handlers normalize month=31-day renewals, avoid duplicate transaction logs, and queue provisioning only for orders that still lack a home.

## 2026-05-05
- **Billing checkout — automatic server provisioning after payment:** Fixed the core provisioning gap where `capture_order.php` never populated `$newOrderIds`, so the auto-provisioner was always skipped. After a successful PayPal capture (or zero-dollar checkout), a `billing_orders` row is now created for each paid invoice and passed to `billing_invoke_provision()` so the game server is created/installed immediately without manual admin action.
- **Billing checkout — duplicate provisioning prevention:** Invoice→Order linkage is written atomically (`billing_invoices.order_id` updated after order creation). Because `getUnpaidInvoicesForUser()` filters on `payment_status NOT IN ('paid',…)`, a retried PayPal capture will find no invoices and skip all processing — preventing duplicate servers.
- **Billing checkout — paid invoices no longer reappear in cart:** `markInvoicePaid()` now sets both `payment_status='paid'` and `status='paid'`. The cart query was also tightened to exclude any invoice where `payment_status` is paid/cancelled/refunded.
- **Billing checkout — zero-dollar checkout:** New `checkout_free.php` handles orders where a coupon reduces the total to $0. The cart now shows a "Complete Free Order" button instead of the PayPal button when `$final_amount <= 0`. Free checkout marks invoices paid (method=coupon), creates orders, increments the coupon use counter, and triggers provisioning — identical flow to a PayPal capture.
- **Billing checkout — payment_success.php JOIN fix:** Fixed a broken `SELECT … s.game_name` JOIN that referenced a non-existent column; corrected to `s.service_name`.
- **Billing checkout — SQL migration:** Added `sql/002_billing_checkout_fixes.sql` — idempotent migration that adds `coupon_id`, `discount_amount`, `payment_status`, `subtotal`, and `total_due` columns to `gsp_billing_invoices`, and `coupon_id`/`discount_amount` to `gsp_billing_orders` for older installations missing these columns.
- **Billing order status standardization:** Canonical `billing_orders.status` values are now `Active`, `Invoiced`, and `Expired` only.  All old writes of `installed`, `paid` (as order status), and `suspended` have been replaced.  A SQL migration script `modules/billing/sql/normalize_billing_order_status.sql` converts any existing legacy rows.  Backward-compatibility read paths (e.g. renewable-status checks in `my_account.php`) are preserved until the migration runs.
- **Expiration display date-only:** The billing expiration shown on the game server monitor (`server_monitor.php`) now displays as `YYYY-MM-DD` only instead of `YYYY-MM-DD HH:MM`.
- **Full-day expiration grace rule:** A server whose `end_date` falls on today is treated as active for the entire calendar day.  Expiration is only processed starting the next calendar day.  This rule is applied consistently in: billing cron (`cron-shop.php` Steps B and C), the server monitor expiration helper (`home_handling_functions.php::get_server_billing_expiration_html`), and the OGP user/group assignment expiration processor (`user_games/check_expire.php`).  All comparisons now use `DATE(end_date) < CURDATE()` (SQL) or `< strtotime(date('Y-m-d'))` (PHP) — never `<= NOW()` or `<= time()`.


- **GSP 1.0 baseline:** Reset all bundled/core module versions to `1.0`. DB schema versions (`$db_version`) are unchanged.
- **FAQ module refresh:** Restored online RSS update code from upstream (opengamepanel.org), fixed `$local = false` initialization bug, switched local cache to `ogpfaq.rss`, added PHP 8.3-compatible `(array)` casts, restored upstream credits footer, and opened `navigation.xml` access to `user,admin,subuser`.
- **Config XML editor improvements:** Added schema validation before save (both structured editor and raw XML path); invalid XML is rejected with line-level error messages instead of being written to disk. Added auto-restore from backup on validation failure. Fields are now displayed in schema-defined order with required/optional badges. Added a raw XML editing panel with validation warning. Unknown/custom XML fields are preserved when only specific nodes are modified.
- **Obsidian theme:** Added `themes/Obsidian/` from `hmrserver/Obsidian`. The theme is immediately selectable in the panel theme settings.


- Removed 22 stray backup/duplicate files left by manual editing (`.bak`, `.BAK`, `.orig`, `.backup` extensions). Files inside `modules/config_games/server_configs/backup/` (intentional runtime backup folder) were left untouched.

## 2026-05-01
- Changed panel update backup location from `/var/backups/gsp-panel` to `<panel_root>/backups/` so all backups are self-contained inside the panel directory and require no external path or elevated permissions.
- Removed stale "Dev Testing" placeholder heading from the panel login page (`index.php`).

## 2026-05-01
- Added safe panel update system to `home.php?m=administration&p=main`:
  - Numbered Releases: fetches GitHub releases via API, shows newest-first dropdown, updates to selected tag.
  - Development Version: pulls from the stable branch (default `Panel-stable`).
  - Cutting Edge Version: pulls from the unstable branch (default `Panel-unstable`) with an instability warning.
  - Full pre-update backup (DB via mysqldump + panel files via PHP copy, excluding `.git/`, `logs/`, `cache/`, `tmp/`) saved to `/var/backups/gsp-panel/YYYY-mm-dd_HH-MM-SS/`.
  - `backup.json` metadata (timestamp, git commit, current version, update target) written with each backup.
  - Revert section lists available backups; restores files and database from the selected snapshot.
  - Config files (`includes/config.inc.php`, DB update-blacklist) preserved across all updates.
  - CSRF protection on every update/revert form; admin-only access enforced.
  - All update actions logged to `logs/panel_updates.log`.
  - Installed version/branch written to `includes/panel_version.php` after each update.

## 2026-04-23
- Applied a repository-wide PHP 8 compatibility sweep across PHP sources to harden array iteration/count/key checks, normalize `date()` timestamp casting, and quote bare `delete/edit/remove` string concatenations.

## 2026-01-31
- Rebuilt the Steam Workshop picker search to rely solely on Steam Community scraping (matching the working curl flow) and updated the request preview to show the actual Steam URL instead of a local panel endpoint.
- Added adapter AppID lookup for Workshop search so the picker can query Steam even when the server XML lacks a clear default installer entry.
- Switched Workshop picker results to checkbox selection, letting customers toggle multiple mods directly from the search list.
- Added a hardened Workshop scraping helper (the same HTML workflow we validated manually) and wired it into the Steam Workshop service as a fallback whenever the official API errors out or returns empty data.
- Added a native PHP HTTP scraper fallback (auto-selected when bash/proc_open is unavailable, e.g., on Windows XAMPP installs) so the Game Monitor search stops showing “Unable to contact the Steam Workshop” when the API dies but the HTML workflow still works.
- Surface scraper vs API attempts (including shell commands, exit codes, and stderr) in the JSON response so the Game Monitor can show exactly which backend produced the results.
- Bundled the reusable `workshop_scrape.sh` bash helper inside the module so future diagnostics can be run server-side without re-copying the ad-hoc script.

## 2026-01-25
- Replaced the Steam Workshop search backend with the official Steam Web API (QueryFiles) so searches are anonymous, paginated, and no longer depend on fragile HTML scraping.
- Added detailed Steam API failure logging plus structured JSON responses that expose pagination metadata to the UI.
- Introduced a reusable SteamCMD installer helper that downloads Workshop items with anonymous login, falls back to authenticated credentials, and captures all stdout/stderr in per-run log files.
- Documented the new search and install helpers to clarify expected usage from both the panel UI and CLI tooling.

## 2026-01-17
- Added per-game Steam Workshop adapter management with CRUD UI and automatic mapping helpers.
- Added workshop capability helpers and monitor button gating so only supported SteamCMD homes expose the Steam Workshop shortcut.

## 2026-01-18
- Reworked Steam Workshop support detection to read installer AppIDs directly from the canonical game XMLs, ensuring admin mappings and Game Monitor buttons only appear for true Workshop-enabled titles.

## 2026-01-19
- Hid staging directory, install strategy, and post-install script controls from non-admins to keep panel defaults enforced for customers.
- Added the Steam Workshop mod picker, including search-backed UI, JSON state handling, and refreshed styling so customers can select mods without touching raw ID lists.
- Pointed the Workshop search endpoint at whichever panel script rendered the page so AJAX searches work in both the main panel and the customer Game Monitor.
- Removed the per-server install-strategy selector so that behavior is governed solely by the admin adapter configuration.
- Removed the per-server post-install script field so hook execution stays defined at the adapter level only.
