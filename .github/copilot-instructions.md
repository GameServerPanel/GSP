# GSP (GameServerPanel) — Copilot Instructions (No-Code)

**Repo of truth:** `GameServerPanel/GSP`, branch `Panel-unstable`.  
**Prime directive:** Read this document first. Propose changes that align with our repo and specs. Only search for external info if something contradicts this file.

## Standalone website mode
- When working on website features, treat the `_website/` folder as a standalone website root. All website-focused changes (pages, runtime, data persistence, webhooks, and admin UI for the storefront) should live inside `_website/` and be referenced relative to that folder.
- Do NOT modify files outside `_website/` (the panel codebase) unless a maintainer explicitly asks for cross-repo or panel-side changes. If a change necessarily touches panel files, call it out clearly in the plan and get maintainer approval first.
- All redirects, data directories, and public-facing endpoints implemented for the storefront must be scoped under `_website/` (absolute or root-relative to the `_website` site root), not the panel root or external panel dashboard pages.

## CRITICAL: Website file paths and URLs (modules/billing)
- **The billing website files in `modules/billing/` will be deployed at the WEBSITE ROOT when live.**
- **NEVER EVER use `/modules/billing/` in any URL, link, redirect, or file path within the billing website code.**
- **All URLs must be root-relative (starting with `/` but NOT including `/modules/billing/`):**
  - ✅ CORRECT: `/payment_success.php`, `/cart.php`, `/order.php`
  - ❌ WRONG: `/modules/billing/payment_success.php`, `modules/billing/cart.php`
- **This is a CRITICAL requirement that has been violated multiple times. Read this section carefully before making ANY changes to billing website files.**

### Examples of CORRECT usage:
```php
// PayPal return URLs
$returnUrl = $siteBase . '/payment_success.php';
$cancelUrl = $siteBase . '/payment_cancel.php';

// Header redirects
header('Location: /cart.php');
header('Location: /order.php');

// Links
<a href="/my_account.php">My Account</a>
<a href="/serverlist.php">Browse Servers</a>

// Form actions
<form action="/add_to_cart.php" method="POST">
```

### Examples of WRONG usage (NEVER DO THIS):
```php
// ❌ WRONG - includes modules/billing path
$returnUrl = $siteBase . '/modules/billing/payment_success.php';
header('Location: /modules/billing/cart.php');
<a href="/modules/billing/my_account.php">My Account</a>
```

### Exception - Backend includes only:
- Backend PHP includes CAN use `__DIR__` or relative paths for file inclusion:
  - ✅ `require_once(__DIR__ . '/includes/config.inc.php')`
  - ✅ `require_once(__DIR__ . '/../../includes/database_mysqli.php')`
- But these are for SERVER-SIDE file inclusion, NOT for user-facing URLs/redirects/links.

## 1) What to read first (paths & context)
- `_website/` — canonical website storefront and Checkout/Webhooks flow.
- `modules/config_games/server_configs/` — authoritative game catalog XMLs (all supported games live here).
- `modules/` — panel modules (legacy `billing/` exists; its **schema** is authoritative for multi-remote, but the **pages** are deprecated).
- `modules/billing/` — frontend website for selling gameservers to customers. Can interface with panel from same machine or external web host via MySQL tables. Uses `gameservers_website` session namespace (separate from panel sessions).
- `includes/` — panel configuration and DB connectors.
- `ogp_api.php` — internal API entry point for panel-side actions.
- `api/` — Payment-related API code if present in this branch (previously under `paypal/` or `payments/`).

## 2) No-Code Planning Mode (default)
- Do **not** emit PHP, SQL, XML, or shell commands unless a maintainer explicitly asks: **“Generate code now.”**
- While in planning mode, produce only:
  - Impacted paths and files,
  - Step-by-step plans with acceptance criteria,
  - Risks, rollbacks, and test/validation checklists,
  - Data mappings that reference existing tables/fields.

## 3) Scope & principles
- **Website ↔ Panel on the same host.** Website uses the **panel DB for authentication** and the **panel’s internal APIs** for provisioning. **Sessions remain separate** (website session ≠ panel session).
- **Billing module flexibility.** The `modules/billing/` frontend can run on the **same machine as the panel** or on an **external web host**, interfacing primarily via MySQL table edits. All interaction with panel DB happens through direct MySQL queries using credentials in `modules/billing/includes/config.inc.php`.
- **Billing module flexibility.** The `modules/billing/` frontend can run on the **same machine as the panel** or on an **external web host**, interfacing primarily via MySQL table edits. All interaction with panel DB happens through direct MySQL queries using credentials in `modules/billing/includes/config.inc.php`.
- **Catalog = XML.** Enable **every game** present under `modules/config_games/server_configs/`. The website reads those XMLs for ports, params, install/update metadata. New XMLs should become available without code changes.
- **Regions/Nodes = panel DB.** Regions and nodes are configured in the panel and must be **queried live** from the panel DB. Never hardcode or mirror region lists on the website.
- **Slotless model.** Pricing/UX must not enforce slot caps. If an engine requires a player count parameter, set a safe high default and surface engine limits transparently if they exist.
- **Auth compatibility.** Panel users use legacy MD5 in `ogp_users`. The website should prefer a modern hashing shadow and upgrade transparently on successful login, **without breaking panel login**.
- **Checkout/Webhooks.** Follow the working **PayPal Checkout** flow in `_website/`. Use **REST Webhooks** only. Mark orders paid **only** after webhook verification.
- **Legacy billing module.** Treat `modules/billing/` **pages** as deprecated. Reuse the **existing tables/fields** introduced there for **multi-remote** support. Do not invent parallel schema.

## 4) Functional requirements (design-level only)
### 4.1 Catalog (from XML)
- Parse all XMLs under `modules/config_games/server_configs/`.
- Normalize game key, display name, required ports, startup parameters, install/update routines, and any engine constraints.
- Support hot-add: new XMLs become available to the storefront after a repo update.

### 4.2 Authentication & sessions
- Website registration creates a panel user (legacy-compatible) and stores a **modern hash shadow** linked 1:1 to that user.
- Login prefers the modern hash; on MD5 success, upgrade silently to the modern hash.
- Maintain **separate sessions** for website and panel.

### 4.3 Checkout → Webhooks → Provisioning
- Mirror `_website/` structure and flows for Checkout.
- On verified webhook events: transition order state to paid, create service records, and **provision** a panel Home using internal panel APIs.
- Derive ports and startup parameters **from the XML metadata**.

### 4.4 Regions/Nodes (multi-remote)
- At checkout or during provisioning, present or auto-select regions/nodes by reading **the panel DB**.  
- If a node is hidden/disabled in the panel, it must not appear in the website UI.

### 4.5 Billing automation (website-side)
- Reconcile renewals and invoke panel APIs to suspend/reactivate/terminate services.
- Operations must be idempotent and observable (logs/metrics defined at design time).

## 5) Data model alignment (no DDL)
- Use the **panel DB as the source of truth**.  
- **Multi-remote** tables and fields already exist (introduced by the legacy billing work). Reuse them.  
- Only propose new fields/tables if strictly necessary; when doing so, reference existing naming conventions and provide a migration plan (still no SQL while in planning mode).

## 6) Coding standards & security (what to enforce when code is requested)
- **Repository-first:** Before proposing file names, endpoints, or structures, search `Panel-unstable` to reuse existing helpers, patterns, and locations.
- **Strictness:** Prefer strict comparisons; parameterized DB access; centralized input validation and output escaping.
- **Session & CSRF:** Harden website sessions and require CSRF tokens on state-changing requests.
- **Webhooks:** Verify signatures and event types server-side; never trust client redirects for payment state.
- **XML:** Harden parsing (no external entities; size/complexity limits). Treat XML as untrusted input even though it’s in-repo.
- **Observability:** Define success/failure metrics, audit logs for state changes, and trace IDs for provisioning flows.
- **Licensing:** Preserve upstream notices and ensure our additions stay license-compatible.

## 7) Validation checklist (pre-PR / pre-merge)
- Read `_website/`, `modules/config_games/server_configs/`, `modules/`, `includes/`, `api/` (if present), and `ogp_api.php` to anchor proposals to actual code.
- Catalog uses only the XML metadata; no hardcoded ports/params.
- Regions/nodes are read live from the panel DB; no duplicates on the website.
- Auth plan preserves panel compatibility and modernizes website hashing; **sessions remain separate**.
- Checkout mirrors `_website/`; uses **REST Webhooks**; paid state changes occur only after verification.
- Provisioning calls panel internals (e.g., `ogp_api.php`), respects selected/auto node, and records mappings consistently.
- Legacy billing module pages are not extended; its schema is reused for multi-remote.
- Security items from §6 are addressed in the plan: CSRF, webhook verification, strict comparisons, hardened XML.

## 8) Deliverables for Copilot (when planning)
- A concise change plan that lists:
  - Files to create/modify/remove and their locations,
  - Data sources and mappings to existing tables/fields,
  - UX notes (e.g., region selector vs auto-placement),
  - Risks, rollback approach, and test coverage,
  - Acceptance criteria aligned to these instructions.
- Update `CHANGELOG.md` with a brief, high-signal entry (date, scope, rationale).
- Append a single line item to `docs/COPILOT_TODO.md` for any UI follow-ups or next steps.

## 9) Prohibited while in planning
- No PHP/SQL/XML.
- No shell commands or system setup steps.
- No scaffolding diffs or auto-generated file dumps.

---

**End of Copilot Instructions (No-Code).**
