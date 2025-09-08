# Gameservers World – Development & Operations TODO

This document tracks development tasks, infrastructure setup, and business steps for building our commercial game server community around the **GS Panel** (fork of OGP), aka **GSP**.  
The software is developed and maintained by **World Domination Software (WDS)**. 

---

## BASE TODO
- [ ] Repo for GSW website
- [ ] Repo for WDS website
- [ ] Create organization for WDS
- [ ] Create OGP-like section and forum/docs on WDS website
- [ ] Repo for game post-install config scripts and files
- [ ] Compare agent files — can we use the same files?
- [ ] Add scripts for setting up servers with required files, DLL, settings
- [ ] DR server

---

## 1. Core Panel (OGP → GS Fork)
- [ ] GSW is a host provider. GSP is a product of WDS. Brand as such. The WDS site needs to keep the forum and documentation.
- [ ] Replace all references from **OGP** → **GS / Gameservers World**
- [ ] Update UI text, branding, and logos
- [ ] Create new default theme (dark/light mode support)
- [ ] Review and clean up legacy OGP code no longer needed
- [ ] Update README.md with GS branding + installation instructions
- [ ] Ensure GPL license text is preserved and updated with attribution
- [ ] Remove `print` language as there is probably a better option now — which one?
- [ ] GSP GitHub needs Wiki
- [ ] Create releases for GSP and the AGENT
- [ ] Agent assistant on sites

---

## 2. Configuration & Secrets
- [ ] Add `.gitignore` for secrets (`.env`, `config/*.local.*`, `*.secret.*`)
- [ ] Create `.env.example` with all required environment variables
- [ ] Add `config/*.dist` template files for MySQL, Hive.ini, Steam API keys
- [ ] Write `scripts/load_env.sh` to load secrets into runtime
- [ ] Decide secrets storage strategy
- [ ] Private `gs-secrets` repo
- [ ] Security: strong password for user, firewall rules to block DDoS and hack attempts, ability to ban IPs across all machines

---

## 3. Database
- [ ] Export schema from phpMyAdmin (`db/schema.sql`)
- [ ] Add to repo under `/db/`
- [ ] Keep migrations in `/db/migrations/`
- [ ] Automate schema export with GitHub Actions (optional)
- [ ] Document how to apply schema & migrations in README

---

## 4. Backup & DR (Disaster Recovery)
- [ ] Script to replicate MySQL (`mysqldump` or replication) to DR site
- [ ] Script to mirror website files from GitHub into DR
- [ ] Script to copy changed files from user servers → DR backup
- [ ] Test recovery process from DR → production

---

## 5. Game Server Management
- [ ] Maintain list of all supported games (Linux, Windows, Unknown)
- [ ] Include common mods per game (e.g., AMX for CS 1.6, DayZ DB)
- [ ] Add database configuration docs for DB-based games
- [ ] Implement server resource tiers (Low / Mid / High hardware)
- [ ] Implement location toggles (ATL, LA, KC, etc.) and the ability to move server
- [ ] Add slot-based pricing calculator to panel

---

## 6. Automation & Scripts
- [ ] Script to auto-pull latest GS panel updates to all servers
- [ ] Script to create manifest of installed game files (with timestamps)
- [ ] Watcher for Steam Workshop items (sync commonly used mods). Scan workshops or keep in SQL and update continually. Update the working install when needed, but we have the files cached.
- [ ] Multi-server RCON tool for posting messages from the panel (like BEC/B3 built into the panel)
- [ ] Git push/pull sync scripts for private repos (`git_force.sh`)
- [ ] Git push/pull sync for a setup script for each gameserver: includes Steam install, workshop installs, our post-install configs, then from backup. Recreate a server from script for DR or migration.
- [ ] Script to analyze game server resource usage and track over time. Display on core server webpage to admins. Alert on issues.

---

## 7. Hosting Infrastructure
- [ ] Finalize hostname naming scheme (gs-*, lab-*, mysql-*, files-*)
- [ ] Evaluate VPS requirements (RAM/CPU) for panel + MySQL
- [ ] Tier hosting hardware (low/mid/high specs)
- [ ] Identify best global server locations (based on player/server data)
- [ ] Implement DNS failover for panel & DR site

---

## 8. GitHub & Copilot
- [ ] Create public GS repo (code only, no secrets)
- [ ] Add `TODO.md` and keep updated
- [ ] Enable **GitHub Projects** for Kanban-style task tracking
- [ ] Add **Copilot** context files (`schema.sql`, config templates, migrations)
- [ ] Enable **secret scanning** and pre-commit hooks
- [ ] Document workflow for devs using Copilot with repo context

---

## 9. Website & Community
- [ ] Update **gameservers.world** with hosting pricing & signup flow
- [ ] Add "Why Rent From Us?" page with marketing copy
- [ ] Integrate with billing/payment (PayPal, Stripe)
- [ ] Add support documentation & FAQs
- [ ] Create Discord community for support + marketing
- [ ] Add landing page for **World Domination Software** studio (cross-promo)

---

## 10. Marketing & Business
- [ ] Define pricing model (per-slot vs flat rate vs tiered)
- [ ] Research competitor prices (low / high per 16 slots)
- [ ] Create promotional blurbs for each popular game (ARK, Urban Terror, etc.)
- [ ] Build outreach list of gaming communities & contacts
- [ ] Prepare pitch materials (PDF/Word) for associates
- [ ] Track server/player stats to refine target locations

---

## 11. Nice-to-Haves / Future
- [ ] Web-based chat assistant trained on GS docs
- [ ] User dashboard for self-service backups/restores
- [ ] Integration with external monitoring (Prometheus/Grafana)
- [ ] Automated failover of game servers between locations
- [ ] Plugin system for community developers

---

# ✅ Notes
- **GPL Compliance**: Keep OGP attribution in LICENSE, but rebrand UI and docs to GS.  
- **Secrets**: Never commit live passwords or IPs to public repos — use `.env` or secrets repo.  
- **Testing**: Always stage DB changes, backup scripts, and automation before production.