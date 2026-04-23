# Changelog

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
