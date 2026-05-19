# Server Content Workshop – Phase 1

Phase 1 adds manual Workshop ID support inside the existing `addonsmanager` module (user-facing label: **Server Content**).

## Scope (Phase 1)

- No Steam Workshop browser/search UI yet.
- No Steam scraping.
- User enters comma-separated numeric Workshop IDs.
- Panel validates IDs, removes duplicates, and stores them per server home.
- Panel lists saved IDs and supports:
  - Install New
  - Update Selected
  - Remove Selected
  - Update All
- Panel generates a per-server manifest at:
  - `%home_path%/gsp_server_content/workshop_manifest.json`
- Panel runs an approved script path (safe default or game-specific config), never user-supplied command/path.

## Security model

- Ownership check: non-admin users can only access homes assigned to them; admins can access any home.
- Actions are scoped to one `home_id`.
- IDs must be numeric only.
- Script path is not user-editable.
- Manifest path is validated to remain under server home.
- Remove is non-destructive in the generic scripts (preserve/move behavior for Phase 1).
- All actions are logged through panel logging.

## Database

Phase 1 introduces:

- `OGP_DB_PREFIXserver_content_workshop`

and keeps `OGP_DB_PREFIXaddons.addon_type` at `VARCHAR(32)` so `workshop` is valid.

## Game/admin config TODO (next phase hardening)

Each game should define and document:

- `workshop_app_id`
- Linux workshop script path
- Windows/Cygwin workshop script path
- target install location
- restart/update behavior

## Phase 2 (not included here)

- Workshop browsing/search/select UI
- richer metadata/title lookups
- per-game install adapters and deeper status reporting
