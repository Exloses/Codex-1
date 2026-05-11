# PROJECT_MEMORY.md
> Persistent implementation notes for Codex. Keep this file updated at task boundaries and context checkpoints.

---

## Current Task

- Active task: Task 10 — Vue Frontend
- Branch: `codex/task-10-vue-frontend`
- Status: Completed locally, pending PR creation/review.
- Scope: Vue/Inertia storefront pages, components, layout, and Breeze route/test synchronization.
- Do not start Task 11 until Task 10 is merged.

---

## Project Rules

- Never commit `.env` or real secrets.
- Never push directly to `main`.
- Never merge pull requests.
- Storefront must never expose `vendor_price`.
- Prices are stored in USD; frontend can display converted/selected currency labels.
- Windows localhost is the target environment.

---

## Recent Baseline

- Task 1-9 are merged into `main`.
- Task 9 added Filament admin resources and dashboard widgets.
- Existing issue entering Task 10: Breeze feature tests fail because default Breeze routes/pages are not synchronized with the Task 7/8 route map.
- Task 10 fixed the Breeze route/test drift by restoring compatible dashboard, profile, verify-email, confirm-password, and password update routes.

---

## Task 10 Targets

- `StorefrontLayout.vue` with navbar, social login buttons, currency selector, language selector, cart icon, notification bell, wishlist icon, footer, newsletter form, payment/carrier logos, Tawk.to widget hook, and PWA install prompt.
- `Home.vue` with hero carousel, trust badges, category grid, flash sale, featured products, and newsletter section.
- `ProductShow.vue` with gallery, zoom, variant selector, size guide modal, stock state, cart/wishlist actions, Q&A, reviews, shipping estimator, share buttons, stock/price alerts.
- `Cart.vue` with item list, quantity updates, custom notes, coupon input, loyalty redemption, and order summary.
- `Checkout.vue` with address/guest step, shipping step, and payment step placeholders for Stripe/PayPal.
- `TrackOrder.vue` with guest tracking form and timeline.

---

## Task 10 Completed Work

- Added `StorefrontLayout.vue` with nav, social login links, currency/language selectors, cart/wishlist/notification/account links, newsletter signup, payment/carrier badges, Tawk.to script hook, and PWA install prompt.
- Added storefront pages: Home, ProductIndex, ProductShow, Cart, Checkout, TrackOrder, CheckoutSuccess, and FAQ.
- Added account, vendor, and affiliate pages for every current Inertia render target.
- Updated auth pages with social login links and route-name fixes.
- Validation passed:
  - `npm run build`
  - `php artisan about`
  - `php artisan route:list`
  - `php artisan test` with 25 tests and 61 assertions
  - Browser render check for `http://127.0.0.1:8000` and `/track-order`
