# Product

<!-- impeccable:product-schema 1 -->

## Platform

web

## Users

- **Primary: visitors to Zortrox's personal site.** People who know Zortrox from forums, gaming sites or Bluesky and want to see who he is and what he makes (games, projects like steamchecker).
- **Secondary: steamchecker users.** People who installed the steamchecker browser extension, which grays out games they already own and highlights wishlisted games on bundle sites (currently Humble Bundle). They sign in with Steam.
- **Admin: Zortrox.** Manages Steam game data and aliases through the Filament admin at `/admin`.

## Product Purpose

foxslash.com is Zortrox's personal site first. It also hosts the steamchecker extension's backend (owned games, wishlist, game aliases, app list and page config APIs) and, over time, a signed-in area for steamchecker users.

Success for the personal side: a visitor quickly gets who Zortrox is and finds his projects and links. Success for steamchecker: users can link Steam and trust the extension's owned and wishlist results.

## Positioning

A one-person site with a personal voice, not a company or product marketing site. Steamchecker is one of the author's projects, presented as such.

## Operating Context

- Visitors arrive from Zortrox's profiles and links; steamchecker users arrive from the extension (popup, store listing) and sign in with Steam OpenID.
- Steam profiles can be private, in which case owned games or wishlists cannot be read. That is a normal state the UI must explain, not an error to hide.
- The extension calls the API from bundle-site pages; the website and the extension are developed side by side (`../browser-extensions/steamchecker`).

## Capabilities and Constraints

Confirmed now:
- Steam sign in (no email from Steam; new users get a placeholder address and no password), optional password, two-factor and passkeys through Fortify. Email signups must verify their email; Steam accounts are marked verified.
- Public steamchecker API with legacy `/apps/steamchecker/*` URLs kept for installed extension versions.
- Filament admin for Steam games and aliases.

Planned for signed-in users (not built yet):
- Manage steamchecker: linked Steam account, owned and wishlist status, and extension settings tied to the account.
- Suggest game aliases (alternate names bundle sites use for Steam games).
- Later: suggest new bundle sites. Each site needs its own page template config (path pattern, CSS selectors, style variant), matching the extension's schema_version 2 page config.

Access control uses roles and permissions (spatie/laravel-permission). `Super Admin` can do everything; other roles, such as moderators for suggestions, can be added with their own permissions.

Undecided: how alias and site suggestions are reviewed and published, and which roles do the reviewing.

## Brand Commitments

- Name: foxslash. Author handle: Zortrox.
- The fox logo and the orange accent are the brand and stay.
- Dark theme only. No light mode.
- Minimal, but cute and a bit colorful.
- The logo is a fox peeking out from behind a slash, cut by it.
- A cute animated walking fox lives on the site (authored in-house, no licensed animation).
- Social networks are shown as icons; personal sites keep their full URL.
- Beyond the fox logo, orange accent, dark theme and minimalism, the visual design is open to redesign.

## Evidence on Hand

- Logo and images: `public/` and `_old_files/images/`.
- Real copy: the bio on the welcome page (`resources/js/Pages/Welcome.vue`) and the old steamchecker page copy and changelog in `_old_files/site.php`.
- No testimonials, user counts or press exist. Do not invent them.

## Product Principles

1. Personal before product: the site reads as one person's home, with steamchecker as a project inside it.
2. Honest about Steam: show clearly when data is unavailable (private profile, Steam down) rather than showing empty results as if they were real.
3. The extension contract is shared: anything the site changes in the steamchecker API or page config is changed together with the extension.
4. Small and cozy: few pages, few controls, nothing added for decoration except the fox and its world.
