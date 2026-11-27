---
name: foxslash
description: Zortrox's personal site and the home of the steamchecker extension. A cozy night den: dark, cute, orange-led with a few night colors.
colors:
  den-black: "oklch(0.13 0.028 261.692)"
  den-wall: "oklch(0.21 0.034 264.665)"
  den-shelf: "oklch(0.278 0.033 256.848)"
  den-edge: "oklch(0.373 0.034 259.733)"
  ash-quiet: "oklch(0.551 0.027 264.364)"
  ash-text: "oklch(0.707 0.022 261.325)"
  ash-bright: "oklch(0.872 0.01 258.338)"
  moon-white: "#ffffff"
  fox-orange: "oklch(0.705 0.213 47.604)"
  fox-orange-deep: "oklch(0.646 0.222 41.116)"
  fox-glow: "oklch(0.75 0.183 55.934)"
  fox-glow-soft: "oklch(0.837 0.128 66.29)"
  danger: "oklch(0.577 0.245 27.325)"
  success: "oklch(0.792 0.209 151.711)"
  sun: "oklch(0.817 0.166 76.3)"
  berry: "oklch(0.76 0.14 355)"
  mint: "oklch(0.8 0.12 170)"
  lilac: "oklch(0.74 0.12 300)"
  bluesky: "oklch(0.62 0.2 255)"
typography:
  headline:
    fontFamily: "Fredoka, Instrument Sans, ui-rounded, sans-serif"
    fontSize: "1.875rem"
    fontWeight: 600
    lineHeight: 1.33
  title:
    fontFamily: "Instrument Sans, ui-sans-serif, system-ui, sans-serif"
    fontSize: "1.125rem"
    fontWeight: 600
    lineHeight: 1.55
  body:
    fontFamily: "Instrument Sans, ui-sans-serif, system-ui, sans-serif"
    fontSize: "1rem"
    fontWeight: 400
    lineHeight: 1.625
  label:
    fontFamily: "Instrument Sans, ui-sans-serif, system-ui, sans-serif"
    fontSize: "0.875rem"
    fontWeight: 500
    lineHeight: 1.43
rounded:
  sm: "0.25rem"
  md: "0.375rem"
  lg: "0.5rem"
  full: "9999px"
spacing:
  gutter: "1.5rem"
  section: "4rem"
components:
  button-primary:
    backgroundColor: "{colors.fox-orange}"
    textColor: "{colors.moon-white}"
    rounded: "{rounded.md}"
    padding: "0.5rem 0.75rem"
  button-primary-hover:
    backgroundColor: "{colors.fox-orange-deep}"
  link-nav:
    textColor: "{colors.ash-bright}"
    rounded: "{rounded.md}"
    padding: "0.5rem 0.75rem"
  link-nav-hover:
    textColor: "{colors.fox-glow}"
  provider-button:
    backgroundColor: "{colors.den-shelf}"
    textColor: "{colors.ash-bright}"
    rounded: "{rounded.lg}"
    padding: "0.625rem 0"
  provider-button-hover:
    backgroundColor: "{colors.den-edge}"
  panel:
    backgroundColor: "{colors.den-wall}"
    rounded: "{rounded.lg}"
  input:
    backgroundColor: "{colors.den-black}"
    textColor: "{colors.ash-bright}"
    rounded: "{rounded.md}"
---

# Design System: foxslash

## Overview

**Creative North Star: "The Fox's Den"**

A cozy dark home at night. The room is near-black and cool gray; the warmth comes from the fox: the orange accent, the peeking-fox logo, and a small fox who trots across a meadow at the bottom of the home page. A few night colors (sun-yellow stars, mint grass, berry and lilac flowers) make it cute without making it loud. A visitor should read one person's voice, not an interface.

The system is minimal and dark only. There is no light mode. Surfaces are flat tonal layers that step up from the floor of the den to its walls and shelves, divided by thin borders. Depth beyond that is a faint orange glow, used for focus and for the fox, never for decoration.

Components are soft and friendly: rounded corners, pill-shaped links on personal pages, gentle hover changes, and a rounded display face, so the cute fox feels at home. App pages are the Laravel Vue starter kit (shadcn-vue) restyled to this palette.

**Key Characteristics:**

- Dark only, cool gray neutrals, orange leads; sun, berry, mint and lilac are small night accents.
- Flat tonal layers with thin borders; a subtle orange glow is the only lift.
- Soft, rounded, quiet components.
- A narrow, centered reading column for personal pages.

## Colors

Tokens map to shadcn variables in `resources/css/app.css`: Den Black is `--background`, Den Wall is `--card`/`--sidebar`, Den Shelf is `--muted`/`--border`, Den Edge is `--input`, Fox Orange is `--primary`/`--ring`, Fox Glow is `--accent-foreground`. Use the Tailwind utilities for those tokens (`bg-card`, `text-muted-foreground`, `bg-primary`), not raw gray or orange classes.

Cool blue-gray darks led by orange, with four small night accents defined as Tailwind colors in `@theme` (`text-sun`, `text-berry`, `text-mint`, `text-lilac`, `bg-bluesky`).

### Primary

- **Fox Orange** (`fox-orange`, Tailwind `orange-500`): primary buttons (Register), active borders, focus rings (`ring-orange-500`).
- **Fox Orange Deep** (`fox-orange-deep`, `orange-600`): primary button hover and pressed states.
- **Fox Glow** (`fox-glow`, `orange-400`): link and nav hover text, highlighted names in copy (the "Zortrox" handle), focused input borders.
- **Fox Glow Soft** (`fox-glow-soft`, `orange-300`): hover on text already shown in Fox Glow.

### Night accents

- **Sun** (`sun`): the "Slash" half of the wordmark, the logo's slash, stars.
- **Berry** (`berry`): blush on the fox, flowers, one highlighted phrase in copy.
- **Mint** (`mint`): meadow grass and ground line, the globe icon, one highlighted phrase.
- **Lilac** (`lilac`): flowers, alternate stars, one highlighted phrase.
- **Bluesky** (`bluesky`): hover fill of the Bluesky icon button only.

### Neutral

- **Den Black** (`den-black`, `gray-950`): page background.
- **Den Wall** (`den-wall`, `gray-900`): header bar and panels.
- **Den Shelf** (`den-shelf`, `gray-800`): borders between regions, raised controls such as the "Login with Steam" button, hover fills.
- **Den Edge** (`den-edge`, `gray-700`): stronger borders and hover fill on raised controls.
- **Ash Quiet** (`ash-quiet`, `gray-500`): de-emphasized text such as the "Or" divider prompt.
- **Ash Text** (`ash-text`, `gray-400`): body copy and secondary text. The most used text color.
- **Ash Bright** (`ash-bright`, `gray-300`): page default text and nav links.
- **Moon White** (`#ffffff`): headings and text on orange.

### Status

- **Danger** (`danger`, `red-600`): destructive buttons and error text (`red-400`/`red-500` on dark).
- **Success** (`success`, `green-400`): confirmations such as "Saved."

### Named Rules

**The Orange Leads Rule.** Orange marks what is interactive, focused, or the fox, and is the only accent allowed on buttons and focus. Night accents decorate the fox's world (logo, meadow, highlighted words) in small doses; never as large fills, button colors or status colors.

**The Dark Den Rule.** There is no light theme. `<html>` always has the `dark` class and the palette lives once on `:root` in `resources/css/app.css` as shadcn tokens (`--background`, `--card`, `--primary`, ...). Never add light surfaces or a theme toggle.

## Typography

**Display Font:** Fredoka (500/600), rounded, for headings, the wordmark and the footer. Loaded through bunny fonts in `vite.config.ts`, used with `font-display`.

**Body Font:** Instrument Sans (400/500/600, loaded through `laravel-vite-plugin` bunny fonts in `vite.config.ts`), falling back to the system sans stack.

**Character:** Fredoka carries the cuteness in headings; Instrument Sans keeps body copy and forms plain and readable.

### Hierarchy

- **Headline** (Fredoka 600, 1.875rem): page headings, in Moon White. Auth page titles use Fredoka at 1.5rem.
- **Title** (600, 1.125rem): section and card titles on settings pages.
- **Body** (400, 1rem, 1.625 via `leading-relaxed`; the home bio uses 1.125rem): descriptive copy in Ash Text, inside `max-w-xl`.
- **Label** (500, 0.875rem): nav links, buttons, form labels. `text-sm` is the dominant size across the app.

## Layout

- Personal pages use a centered column: `max-w-4xl` with `px-6` (1.5rem gutter) for the shell and a narrower `max-w-xl` reading column, with `py-16` (4rem) vertical breathing room.
- App pages (dashboard, settings) use the starter kit's sidebar shell; settings use a left nav with a `max-w-xl` content column.
- The home header is a single row with no bar: logo and Fredoka wordmark left, pill nav right. A meadow band sits between the content and the footer.
- Layouts are single column and stack naturally on narrow screens.

## Elevation & Depth

Flat by default. Depth comes from tonal steps (Den Black floor, Den Wall panels, Den Shelf controls) and one-pixel borders in Den Shelf or Den Edge. The small shadows the shadcn components ship with (`shadow-xs` on inputs and buttons, `shadow-lg` on dialogs) read as near-invisible on this background.

The chosen lift is a subtle orange glow: focus rings in Fox Orange, and a faint glow around the fox character. A glow marks attention, never hierarchy.

### Named Rules

**The Glow Means Attention Rule.** An orange glow appears only on focus, on the active item, or on the fox. Panels and cards never glow.

## Shapes

Soft and rounded. Buttons and nav links use `rounded-md` (0.375rem); panels and the provider button use `rounded-lg` (0.5rem); avatars use `rounded-full`. No sharp corners on interactive elements, and nothing larger than `rounded-xl` except circles.

## Components

- **Primary button:** Fox Orange fill, white text, `rounded-md`, `px-3 py-2 text-sm`, darkens to Fox Orange Deep on hover with a short color transition.
- **Nav link:** Ash Bright text, no fill, `rounded-md`, turns Fox Glow on hover.
- **Provider button ("Login with Steam"):** full-width, Den Shelf fill, Den Edge border, `rounded-lg`, hover lifts to Den Edge fill with an orange border. Provider icon left of the label.
- **Divider with prompt:** a thin Den Edge rule on both sides of a short Ash Quiet word ("Or").
- **Panel:** Den Wall surface, `rounded-lg`, used for dashboard cards and bordered settings rows such as Connected accounts.
- **Input:** dark fill, Ash Bright text, `rounded-md`, Fox Orange focus ring.
- **Logo (`FoxLogo.vue`):** a front-facing fox looking straight at the viewer, peeking out from behind a sun-yellow slash with its paw curled over the slash's edge; the slash clips its right cheek and ear. Ears are drawn before the head so their bases tuck into it. Inline SVG, also exported as `public/favicon.svg`, `favicon.png`, `favicon.ico` and `apple-touch-icon.png`. With `peek`, the head slides out from behind the slash once on load (the home hero only).
- **Walking fox (`WalkingFox.vue`):** a side-view fox trotting across a night meadow (stars, mint grass, flowers) above the home footer. It walks off one edge, turns and walks back; legs, tail, ears and eyes move. Decorative (`aria-hidden`), pure SVG and CSS, and still when `prefers-reduced-motion` is set.
- **Social links:** icon-only round buttons for social networks (with an `aria-label`); personal sites keep their full URL as a text pill.

## Do's and Don'ts

- **Do** keep pages to one quiet column with generous space.
- **Do** keep orange for interactive, focused, and fox elements only.
- **Do** show unavailable Steam data (private profile, Steam down) as a plain, friendly message.
- **Don't** add a light mode or light surfaces.
- **Don't** add new accent colors, gradients, or decorative illustration outside the fox and its meadow.
- **Don't** use heavy drop shadows for depth; use tonal steps and borders.
