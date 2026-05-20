# Server Image Module Review (Phase 1)

## Scope reviewed
- `Panel/modules/lgsl_with_img_mod/`
- `Panel/modules/dsi/`
- Integration touchpoints in `Panel/modules/gamemanager/`

## Entry points

### lgsl_with_img_mod
- Module metadata: `Panel/modules/lgsl_with_img_mod/module.php`
- User/admin pages:
  - `lgsl.php`
  - `lgsl_admin.php`
- Image endpoint:
  - `image.php` (reads by `s` argument, supports `img_type`)
- Core query/cache/image logic:
  - `lgsl_files/lgsl_class.php`

### dsi
- Module metadata: `Panel/modules/dsi/module.php`
- User/admin/list pages:
  - `dsi_user.php`
  - `dsi_admin.php`
  - `dsi_list.php`
- Image endpoint:
  - `image.php` (`modules/dsi/s-IP_PORT-type.png` style)
- Helpers:
  - `includes/functions.php`
  - `includes/functions_ui.php`

## Comparison

### Which module generates images?
- **Both** generate PNG status banners.
- `lgsl_with_img_mod` is LGSL-centric and built around the `OGP_DB_PREFIXlgsl` cache model.
- `dsi` supports LGSL/GameQ/TS3 monitor includes and can render banner/code snippets directly for panel users.

### Which has better cache support?
- **lgsl_with_img_mod** has deeper cache integration:
  - DB-backed query cache (`OGP_DB_PREFIXlgsl.cache`, `cache_time`)
  - image file cache handling
  - pending/retry semantics in `lgsl_query_cached(...)`
- `dsi` has simple file cache (60s TTL) per generated image and relies on protocol monitor include side effects for query state.

### Which integrates with server monitor better?
- **dsi** is currently more user-facing for “banner + embed code” workflows:
  - `dsi_render_table(...)` outputs HTML/BBCode snippets.
  - Integrates query handlers by protocol in `dsi/image.php`.
- `lgsl_with_img_mod` is more standalone/legacy LGSL module flow.

### Which supports player info better?
- Both are focused on banner status fields (name/map/players/status).
- Neither is currently a full player-list UI provider for Game Manager.
- Query-level player data comes from monitor protocol paths, not these image modules as a first-class shared API.

### Which is easier to modernize?
- **dsi** is the better base for a future unified GSP banner module:
  - simpler structure
  - clear image endpoint + code generation UI
  - already aware of LGSL/GameQ/TS3 protocol branching
- `lgsl_with_img_mod` contains useful mature cache ideas and map/image utilities worth reusing.

## Recommended future GSP banner direction
Future module target: `Panel/modules/server_status_banner/`

### Plan
1. Keep both current modules in place during migration.
2. Use `dsi` UX flow and embed-code patterns as baseline.
3. Reuse selective `lgsl_with_img_mod` cache + map/image helper ideas.
4. Drive data from normalized query cache (planned `server_query_cache`) and wrapper output.
5. Generate GSP-owned PNG banners (small / wide / large styles).
6. Avoid external GameTracker asset dependency.
7. Provide HTML / BBCode / direct image URL output.
8. Surface banner preview and code tool in Game Manager.

## No-removal statement (Phase 1 safety)
- `lgsl_with_img_mod` was **not removed**.
- `dsi` was **not removed**.
- This phase is documentation and direction-setting only.
