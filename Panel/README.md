# GameServerPanel (GSP)

GSP is a modern game server hosting panel and commercial-ready hosting platform for teams that need billing, automation, and multi-node operations in one stack.

## What GSP is

GSP is the actively maintained GameServerPanel project. It is a modernized evolution of legacy Open Game Panel concepts, expanded for current hosting workflows and provider operations.

## Why GSP exists

Traditional game panel workflows often require manual setup, disconnected billing flows, and custom glue code between storefronts and provisioning.  
GSP exists to provide a stronger foundation for automated service delivery, consistent customer experience, and safer long-term operations.

## Core features

- Unified panel + storefront architecture
- Shared customer sessions between website and panel surfaces
- Billing-aware server lifecycle management
- Multi-node service placement across locations
- XML-driven game metadata and install configuration

## Automated provisioning

GSP includes work toward fully automated provisioning pipelines that connect order/payment events to server creation, home assignment, and post-provision workflows.

## Storefront, cart, billing, and PayPal support

The billing module provides the foundation for:

- Product catalog and order configuration
- Cart and invoice handling
- Coupon/discount workflows
- PayPal checkout and capture integration

## Steam Workshop management

GSP is designed to support Steam Workshop-enabled game operations, including profile-driven defaults and per-server workshop management flows.

## XML / game configuration management

Game definitions are XML-based so catalog, install metadata, and operational settings can stay centralized and extensible without hardcoded per-title logic.

## Multi-location and OS-aware deployment

GSP includes work toward OS-aware service routing and multi-location hosting so providers can target the right node/runtime combinations per game and region.

## Database migration safety

The project uses versioned module migrations and idempotent upgrade patterns, with guarded schema checks where needed, to reduce upgrade risk across diverse installs.

## Security improvements

Current code includes hardened session handling, credential verification updates, CSRF protection in key admin paths, and safer billing/provisioning validation patterns.

## Hosting provider benefits

- Faster order-to-server delivery
- Better control over node/location availability
- Reduced manual operations overhead
- Cleaner upgrade path for production environments

## Customer experience improvements

- More consistent ordering and checkout flows
- Shared login/session behavior across surfaces
- Better visibility into orders, renewals, and account actions

## Technology stack

- PHP 8.x (actively modernized for current compatibility needs)
- MySQL/MariaDB-backed data model
- XML-based game configuration system
- PayPal REST integration for storefront checkout

## Roadmap and future goals

GSP continues to focus on:

- Stronger automation and provisioning reliability
- Expanded storefront UX and mobile usability
- Broader game/workshop tooling improvements
- Operational observability and admin quality-of-life features

## Contributing

Pull requests are welcome.  
Please keep changes production-safe, follow existing GSP patterns, and avoid introducing legacy compatibility shortcuts that conflict with current architecture.

## License

GSP is distributed under the GNU General Public License v2. See [`LICENSE`](LICENSE) and [`COPYING`](COPYING).
