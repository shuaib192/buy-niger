# BuyNiger - Project Rules & Progress

## Project Overview
Multi-vendor e-commerce platform with AI (AICOS). Laravel 10 + Blade + MySQL.

## Session Progress

### Session 1: Dashboard UI Overhaul (June 8, 2026)

#### Issues Fixed
1. **File Permissions** - `storage/` and `bootstrap/cache/` now world-writable (777) for XAMPP
2. **404 for `/buy-niger/`** - .htaccess redirects to `/buy-niger/public/` (mod_rewrite + external redirect)
3. **`.env`** - Updated for local dev: `APP_URL=http://localhost/buy-niger/public`, DB = root@localhost/buyniger
4. **Dashboard CSS Consolidated** — ~2700 lines added to `public/css/dashboard.css` (stat cards, icon-box, premium buttons, badges, admin page tables, modals, settings toggles, review modals, vendor shared patterns, border-left helpers, etc.)
5. **Layouts Unified** — All dashboards now use `layouts.app`; `layouts.vendor` deprecated/unused
6. **Vendor Dashboard Inline Styles Removed** — dashboard, orders, products/index clean; 10 sub-views marked as page-specific
7. **Superadmin All Views Cleaned** — dashboard, vendors, users, roles, messages, settings, AI, disputes, analytics, payouts, products — zero `<style>` blocks, inline styles minimized
8. **Customer Views Cleaned** — dashboard, profile, addresses, reviews — zero `<style>` blocks, inline styles minimized
9. **Message Modal** — Converted to `.admin-modal` CSS classes
10. **Review Modal** — Converted to `.review-modal` CSS classes
11. **Settings Toggle** — Converted from `.switch`/`.slider` to `.toggle-switch`/`.toggle-slider`
12. **Vendor sub-views** — 10 `<style>` blocks reduced to page-specific only (image upload, chat, finances, coupons, order detail stepper, inventory table, analytics charts). Shared patterns extracted to `dashboard.css` (empty-state-premium, btn-primary-premium, form-group-premium, bg-primary-subtle)

### Session 2: Shop Pages CSS Consolidation (June 8, 2026)

#### Done
1. **`public/css/shop.css` expanded from ~1263 to ~1820 lines** — added:
   - Policy page styles (`.policy-container`, `.policy-header`, `.policy-content`)
   - Status badges (`.status-badge` with pending/paid/processing/shipped/delivered/cancelled/rejected)
   - Back link, empty state, btn-full, btn-outline, summary-row, form-row
   - Alert/alert-danger, secure-text, detail-card, success-icon
   - Page hero (`.page-hero`, `.page-hero-badge`)
   - Store card grid (`.stores-grid`, `.store-card`, `.store-card-banner`, `.store-avatar`, etc.)
   - Image placeholder, map container, WhatsApp button
   - Price label/sale badge/on-sale marker, save-chip, price-col, price-row
   - Vendor placeholder, wishlist button
   - ~40+ utility classes (d-flex, flex-wrap, gap-*, align-items-*, justify-content-*, text-center, w-100, etc.)

2. **4 policy pages fully cleaned** — privacy.blade.php, refund-policy.blade.php, terms.blade.php, vendor-policy.blade.php — `<style>` blocks removed entirely (CSS moved to shop.css)

3. **Extracted shared patterns from 10+ shop views** — cart, checkout, order-detail, order-confirmation, payment, vendor-apply, stores, catalog, product — removed duplicated `.status-badge`, `.back-link`, `.summary-row`, `.btn-full`, `.btn-outline`, `.form-row`, `.empty-state`, `.text-warning/.text-gray`, `.secure-text`, `.alert`, `.btn-outline`, `.detail-card`, `.success-icon`, `.stores-grid`, `.store-card*`, `.price-label` from inline `<style>` blocks

4. **All 18 remaining `<style>` blocks marked as page-specific** — catalog, product, cart, checkout, payment, order-confirmation, order-detail, track-order, about, contact, store, stores, index, vendor-apply, plus the 4 policy pages cleaned

5. **Inline `style=""` cleanup**:
   - Product page: price block (`.price-label`, `.price-label.sale`, utility classes), vendor placeholder (`.vendor-placeholder`), WhatsApp button (`.btn-whatsapp`)
   - Cart page: image placeholder (`.img-placeholder`), sale prices (`.on-sale-marker`, `.sale-badge`)
   - Contact page: form labels (`.text-danger` for asterisks), submit button (`.btn-full`), success alert, map (`.map-container`)
   - Product card partial: image placeholder (`.img-placeholder`), sale price (`.save-chip`, `.price-col`, `.price-row`), wishlist button (`.wishlist-btn`)

#### Remaining
1. **Visual verification** — Check all dashboard + shop pages render correctly after CSS consolidation
2. **Responsive testing** — Test all pages on mobile viewports
3. **Remaining inline styles** (~30+ one-off inline `style=""` attributes in checkout, order-detail, about, stores, contact info icons — mostly dynamically-generated gradients or element-specific tweaks)
4. **Orders/wishlist views** (use `layouts.app`/dashboard.css — could benefit from status-badge standardization)

#### To Access Site
- URL: `http://localhost/buy-niger/` (redirects to `http://localhost/buy-niger/public/`)
- DB: MySQL root@localhost, database `buyniger`
- XAMPP Apache runs as `daemon` user

#### Color Scheme
- Primary: Blue (#2563eb/#0066FF)
- Keep existing blue-based branding

## Session Strategy

### What Was Done (Full Scope)
- **Dashboards**: CSS-first approach — all reusable dashboard CSS moved to `dashboard.css` (~2700 lines), all 26+ superadmin + customer views have zero `<style>` blocks, vendor views marked page-specific
- **Shop pages**: Same approach — shared patterns extracted to `shop.css` (~1820 lines), policy pages fully cleaned, all remaining style blocks marked page-specific, key inline styles replaced with utility/component classes
- **Pattern**: Add CSS class to `shop.css` → remove `<style>` block from view → replace inline `style=""` with CSS classes

### Key CSS Sections Added (Session 2)
`.policy-container`, `.policy-header`, `.policy-content`, `.back-link`, `.status-badge` (+7 variants), `.empty-state`, `.btn-full`, `.btn-outline`, `.summary-row`, `.summary-row.total`, `.form-row`, `.text-warning`, `.text-gray`, `.text-danger`, `.alert`, `.alert-danger`, `.secure-text`, `.detail-card`, `.detail-card h3`, `.success-icon`, `.page-hero`, `.page-hero-badge`, `.stores-grid`, `.store-card`, `.store-card-banner`, `.store-avatar`, `.store-card-body`, `.store-location`, `.store-desc`, `.store-meta`, `.store-visit`, `.img-placeholder`, `.map-container`, `.btn-whatsapp`, `.price-label`, `.price-label.sale`, `.sale-badge`, `.on-sale-marker`, `.save-chip`, `.price-col`, `.price-row`, `.vendor-placeholder`, `.wishlist-btn`, `.d-flex`, `.d-inline-flex`, `.flex-wrap`, `.align-items-*`, `.justify-content-*`, `.text-center`, `.text-uppercase`, `.gap-*`, `.m-0`, `.mb-2/3/4`, `.mt-2/3`, `.p-0/3/4/5`, `.px-3/4`, `.py-2/3/4`, `.pb-0/5`, `.w-100`, `.d-block`, `.font-weight-bold/600`

### Key Files Modified
| File | Change |
|------|--------|
| `public/css/dashboard.css` | ~2700 lines (all shared dashboard CSS) |
| `public/css/shop.css` | ~1820 lines (shared shop CSS, expanded ~+560 lines in Session 2) |
| `resources/views/layouts/app.blade.php` | Premium sidebar, topbar, flash messages |
| `resources/views/layouts/shop.blade.php` | No changes needed (already clean) |
| `resources/views/shop/privacy.blade.php` | `<style>` block removed (→ shop.css) |
| `resources/views/shop/refund-policy.blade.php` | `<style>` block removed (→ shop.css) |
| `resources/views/shop/terms.blade.php` | `<style>` block removed (→ shop.css) |
| `resources/views/shop/vendor-policy.blade.php` | `<style>` block removed (→ shop.css) |
| `resources/views/shop/cart.blade.php` | Extracted summary-row, btn-full, btn-outline, empty-cart → shop.css; cleaned inline styles |
| `resources/views/shop/checkout.blade.php` | Extracted form-row, alert, secure-text, btn-full, btn-outline, summary-row → shop.css |
| `resources/views/shop/order-detail.blade.php` | Extracted back-link, detail-card, summary-rows, status-badge → shop.css |
| `resources/views/shop/order-confirmation.blade.php` | Extracted success-icon, status-badge, btn-outline → shop.css |
| `resources/views/shop/payment.blade.php` | Extracted back-link → shop.css |
| `resources/views/shop/vendor-apply.blade.php` | Extracted status-badge, btn-full, form-row → shop.css |
| `resources/views/shop/stores.blade.php` | Extracted stores-grid, store-card* → shop.css |
| `resources/views/shop/product.blade.php` | Extracted text-warning/gray → shop.css; cleaned price block, vendor placeholder, WhatsApp button |
| `resources/views/shop/catalog.blade.php` | Extracted text-warning/gray → shop.css |
| `resources/views/shop/contact.blade.php` | Cleaned form labels, success alert, submit button, map container |
| `resources/views/shop/partials/product-card.blade.php` | Cleaned image placeholder, sale price display, wishlist button |
| `resources/views/vendor/*` | All 10 sub-views — `<style>` blocks marked page-specific, shared CSS extracted |
| `resources/views/superadmin/*` | Zero `<style>` blocks, inline styles minimized |
| `resources/views/customer/*` | Zero `<style>` blocks |

### Critical Context
- XAMPP Apache runs as `daemon` user — all storage/ and bootstrap/cache/ must be world-writable (777)
- Site accessible at `http://localhost/buy-niger/` (redirects to `/public/`)
- APP_URL must include `/public` suffix for route generation
- Bootstrap 5.3 loaded from CDN only in `layouts.app` (dashboards) — shop pages use pure CSS from `shop.css`
- All dashboards use `layouts.app` — `layouts.vendor` safe to delete later
- Charts use Chart.js loaded via `@push('scripts')`
