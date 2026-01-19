# Changelog

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
