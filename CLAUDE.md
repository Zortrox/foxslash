# foxslash

Personal site at foxslash.com and the backend for the steamchecker browser extension (`../browser-extensions/steamchecker`). The extension highlights games a user owns or has wishlisted on bundle sites.

## Stack

- Laravel 13, PHP 8.5 (Sail runtime), Filament 5 admin at `/admin`, Scout + Typesense for game search, spatie/laravel-permission.
- Auth and the app shell follow the current Laravel Vue starter kit (`laravel/vue-starter-kit` main): Fortify (login, registration, password reset, email verification, two-factor, passkeys), Inertia v3 + Vue 3, Wayfinder for typed routes (no Ziggy), shadcn-vue components in `resources/js/components/ui`, Tailwind v4, Vite+ (`vp build`). Jetstream is gone. When upgrading, diff against the kit rather than inventing structure.
- Inertia SSR is on. `@inertiajs/vite` reuses `resources/js/app.ts` as the SSR entry (no `ssr.ts`); `bun run build:ssr` writes `bootstrap/ssr/app.js`, served in production by `artisan inertia:start-ssr` (a Forge daemon, restarted on deploy with `inertia:stop-ssr --graceful`). In dev, `bun run dev` handles SSR itself. Tests set `INERTIA_SSR_ENABLED=false`. Code that touches `window`/`document` must run in `onMounted` or `WhenMounted`.
- Pages live in lowercase `resources/js/pages`. Settings are `settings/profile` and `settings/security`; there is no Appearance page because the site is dark only.
- `resources/js/components/ui/sidebar/SidebarProvider.vue` fails `bun run types:check` with two Ref typing errors. The file is identical to the kit's, so it is an upstream issue; the build is unaffected.
- typesense/typesense-php is held on v5 because Scout's Typesense engine targets 4.9/5.x.

## Authorization

- Use spatie/laravel-permission roles and permissions for all access control, including future moderation of alias and site suggestions.
- `Super Admin` passes every gate (`Gate::before` in `AppServiceProvider`). Assign it with `artisan app:assign-super-admin-user {email}`.
- Filament (`User::canAccessPanel`) is open to Super Admins and to anyone with the `manage users` permission (created by a migration). That permission only reaches the Access group (Users, Roles, Permissions); `SteamGamePolicy` keeps Steam games Super Admin only. Policies stop non-Super Admins from granting, editing or deleting Super Admin, the `Super Admin` role or the `manage users` permission, or deleting themselves. Users created in Filament are marked verified.
- The frontend gets `auth.user.roles` and `auth.user.permissions` from `HandleInertiaRequests`; check them with `resources/js/composables/usePermission.ts`.

## Local environment

- Everything runs in Sail. Use `vendor/bin/sail artisan ...`, `vendor/bin/sail composer ...`, `vendor/bin/sail artisan test --compact ...`. The host PHP is 8.4 and cannot reach the `mysql`/`typesense` hosts.
- Host ports are remapped in `.env` (app 8003, MySQL 3307, Redis 6380, Mailpit 8026, Typesense 8109, Vite 5174) so this stack runs beside other Sail projects.
- Steam OpenID requires HTTPS, so `STEAM_REDIRECT_URI` points at foxslash.com even locally. Real Steam sign in can only be exercised on the deployed site; locally rely on the feature tests.

## Steam

- `App\Services\SteamService`: app list paging (IStoreService/GetAppList), vanity URL resolution, owned games, wishlist. Owned and wishlist results are cached for 10 minutes, vanity lookups for a day.
- `App\Jobs\SteamFetchGames` (daily at 01:00, or `artisan app:start-steam-fetch-games`) upserts `steam_games` and rebuilds `storage/app/private/steamchecker/app-list.json` through `App\Services\SteamAppListFile`.
- `steam_game_aliases` holds alternate names bundle sites use, managed in the Filament SteamGame resource.

## Steam sign in

- Plain Socialite with `socialiteproviders/steam`. Socialstream was removed because it does not support Laravel 13.
- `AuthenticatedSessionSteamController` handles `/oauth/steam` and its callback; `App\Actions\Auth\ResolveUserFromSteam` links, finds or creates users. New Steam users get a placeholder email `<steamid64>@steam.<APP_URL host>` and no password.
- Users with two-factor enabled are sent to Fortify's challenge. Connected-account and set-password routes live in `routes/oauth.php`; the frontend reads the `oauth` shared Inertia prop (`SteamSignIn.vue`, `ConnectedAccounts.vue`, `SetPassword.vue`).
- `auth.user.avatar` is the linked Steam account's avatar URL; there are no uploaded profile photos.
- Email verification is on for email signups only: Steam-created accounts are marked verified. Changing the email clears verification as usual.
- Passwordless (Steam-only) users: `RequirePasswordWhenSet` lets them into `settings/security` without password confirmation, where they can set a password. Two-factor, passkeys, disconnecting Steam and deleting the account all need a password first.

## steamchecker API

The extension contract. Change it together with the extension session/repo, never on one side only.

| New (`routes/api.php`) | Legacy (`routes/steamchecker.php`) | Legacy shape |
|---|---|---|
| `GET /api/steamchecker/profiles/{profile}/owned` | `GET /apps/steamchecker/owned?steamid=&id64=1` | `{response:{game_count,games}, aliases:{}}` |
| `GET /api/steamchecker/profiles/{profile}/wishlist` | `GET /apps/steamchecker/wishlist?id=&type=id64\|id` | `{wishlist:["appid",...]}` |
| `GET /api/steamchecker/aliases?names[]=` | `POST /apps/steamchecker/aliases` (raw JSON `{type, names}`) | `{"<appid>":["alias",...]}` |
| `GET /api/steamchecker/apps` | `GET /apps/steamchecker/appList.json` | `{applist:{apps:[{appid,name}]}}` |
| `GET /api/steamchecker/config/v2` | `GET /apps/steamchecker/config/v2/` | page config, schema_version 2 (same document) |

- `{profile}` is a SteamID64 or a custom profile URL name; the format is detected. Errors are `{message}` with 404 (unknown or private profile), 422 (missing argument) or 502 (Steam down).
- All of these are public, rate limited by the `steamchecker` limiter (120/min per IP) and send `Access-Control-Allow-Origin: *` because content scripts fetch with the page's origin.
- `config/v2` serves `resources/steamchecker/config-v2.json`, a copy of the extension's `src/config/default-config.json` (contract in the extension's `docs/remote-config.md`). Keep the two in sync.
- The legacy `/apps/steamchecker/selectors/` endpoint is not served here; its data is not in this repo.
- `/apps/steamchecker` (`apps/Steamchecker.vue`) is the extension's page: what it does, store links and a changelog from the GitHub releases of `services.steamchecker.repository`, fetched by `App\Services\SteamcheckerChangelog` (cached an hour, failures five minutes, prereleases skipped). `/apps/steamchecker/privacy-policy` is the privacy policy the store listings link to; keep it true when what the app stores changes.

### Extension sign-in

The extension signs in to a foxslash account instead of taking a pasted SteamID. Contract: the extension's `docs/sign-in.md`.

- `GET /extension/connect?redirect_uri&state&code_challenge&code_challenge_method=S256` (`ExtensionConnectController`): rejects any `redirect_uri` not exactly in `STEAMCHECKER_REDIRECT_URIS` (config `services.steamchecker.redirect_uris`) with a 400 before sending guests to log in. Users without Steam are asked to link it and come back via `url.intended`. Consent page is `auth/ExtensionConnect.vue`, a plain form POST so Allow/Cancel answer with a real 302.
- Allow stores an `ExtensionConnectCode` (SHA-256 hash only, 60 s, bound to user, redirect URI and PKCE challenge) and redirects to `<redirect_uri>?code&state`. Cancel sends `error=access_denied`.
- `POST /api/steamchecker/token` (`ExtensionTokenController`) consumes the code atomically (a wrong verifier still burns it), checks expiry, redirect URI and S256, then issues a Sanctum token with only the `steamchecker` ability. Failures are `400 {message}`.
- Bearer + `abilities:steamchecker`: `GET /api/steamchecker/me`, `/me/owned`, `/me/wishlist` (`MeController`, same shapes and errors as `/profiles/{profile}/*`, plus 409 with no linked Steam), `DELETE /api/steamchecker/token` (204). Users see and revoke these tokens under Settings > Security.
- Locally, email/password login works inside the flow; the Steam button does not, because Steam's callback goes to foxslash.com.

## Tests

- `tests/Feature/Auth/SteamAuthenticationTest.php` and `tests/Feature/SteamcheckerApiTest.php` cover sign in and the API with faked Steam responses.
- The other tests in `tests/Feature/Auth`, `tests/Feature/Settings`, `tests/Feature/DashboardTest.php` and `tests/Unit` are the starter kit's defaults (PHPUnit classes). Keep them close to upstream so they can be re-synced; the only local change is `EmailVerificationTest` using `dashboard` instead of the removed `appearance.edit` route.

## Design

Impeccable context lives in `PRODUCT.md` (product truth) and `DESIGN.md` (visual system). Read them before UI work.

===

<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

## Foundational Context

This application is a Laravel application running on PHP 8.5. Always use the APIs that match the installed major version of each package — do not assume a version.

Before relying on a package's API, confirm its installed version:
- PHP packages: run `vendor/bin/sail composer show --direct` to list direct dependencies with versions, or `vendor/bin/sail composer show <vendor/package>` for a single package.
- JS packages: check `package.json` for the installed versions.

## Skills Activation

This project has domain-specific skills available in `**/skills/**`. You MUST activate the relevant skill whenever you work in that domain—don't wait until you're stuck.

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If a frontend change doesn't show in the UI or you get a "Unable to locate file in Vite manifest" error, run `vendor/bin/sail bun run build` or ask the user to run `vendor/bin/sail bun run dev` or `vendor/bin/sail composer run dev`.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

=== boost rules ===

# Laravel Boost

## Tools

- Laravel Boost is an MCP server with tools designed specifically for this application. Prefer Boost tools over manual alternatives like shell commands or file reads.
- Use `database-query` to run read-only queries against the database instead of writing raw SQL in tinker.
- Use `database-schema` to inspect table structure before writing migrations or models.
- Use `get-absolute-url` to resolve the correct scheme, domain, and port for project URLs. Always use this before sharing a URL with the user.
- Use `browser-logs` to read browser logs, errors, and exceptions. Only recent logs are useful, ignore old entries.

## Searching Documentation (IMPORTANT)

- Use `search-docs` before changes that depend on Laravel ecosystem APIs, behavior, configuration, or version-specific syntax. Skip it for copy-only edits and other changes where package documentation is irrelevant. Reuse sufficient results already in context instead of searching again.
- Pass a `packages` array to scope results when you know which packages are relevant.
- Use multiple broad, topic-based queries: `['rate limiting', 'routing rate limiting', 'routing']`. Expect the most relevant results first.
- Do not add package names to queries because package info is already shared. Use `test resource table`, not `filament 4 test resource table`.

### Search Syntax

1. Use words for auto-stemmed AND logic: `rate limit` matches both "rate" AND "limit".
2. Use `"quoted phrases"` for exact position matching: `"infinite scroll"` requires adjacent words in order.
3. Combine words and phrases for mixed queries: `middleware "rate limit"`.
4. Use multiple queries for OR logic: `queries=["authentication", "middleware"]`.

## Project Rules

- This project contains committed, area-grouped rules in `.ai/rules` when that directory exists, including path-scoped framework guidelines under `.ai/rules/boost`. Before you enter plan mode or create/edit any file, you MUST first: open @.ai/rules/index.md (it maps file globs to rule files), read every rule file whose globs cover the path(s) in scope, and run `grep -rin 'keyword' .ai/rules` to catch what a path match alone misses. Do not write code until you have read and are following every matching rule. If `.ai/rules` does not exist, continue without it.

## Artisan

- Run Artisan commands directly via the command line (e.g., `vendor/bin/sail artisan route:list`). Use `vendor/bin/sail artisan list` to discover available commands and `vendor/bin/sail artisan [command] --help` to check parameters.
- Inspect routes with `vendor/bin/sail artisan route:list`. Filter with: `--method=GET`, `--name=users`, `--path=api`, `--except-vendor`, `--only-vendor`.
- Read configuration values using dot notation: `vendor/bin/sail artisan config:show app.name`, `vendor/bin/sail artisan config:show database.default`. Or read config files directly from the `config/` directory.

## Tinker

- Execute PHP in app context for debugging and testing code. Do not create models without user approval, prefer tests with factories instead. Prefer existing Artisan commands over custom tinker code.
- Always use single quotes to prevent shell expansion: `vendor/bin/sail artisan tinker --execute 'Your::code();'`
  - Double quotes for PHP strings inside: `vendor/bin/sail artisan tinker --execute 'User::where("active", true)->count();'`

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.
- Use PHP 8 constructor property promotion: `public function __construct(public GitHub $github) { }`. Do not leave empty zero-parameter `__construct()` methods unless the constructor is private.
- Use explicit return type declarations and type hints for all method parameters: `function isAccessible(User $user, ?string $path = null): bool`
- Use TitleCase for Enum keys: `FavoritePerson`, `BestLake`, `Monthly`.
- Prefer PHPDoc blocks over inline comments. Only add inline comments for exceptionally complex logic.
- Use array shape type definitions in PHPDoc blocks.

=== deployments rules ===

# Deployment

- Laravel can be deployed using [Laravel Cloud](https://cloud.laravel.com/), which is the fastest way to deploy and scale production Laravel applications.

=== sail rules ===

# Laravel Sail

- This project runs inside Laravel Sail's Docker containers. You MUST execute all commands through Sail.
- Start services using `vendor/bin/sail up -d` and stop them with `vendor/bin/sail stop`.
- Open the application in the browser by running `vendor/bin/sail open`.
- Always prefix PHP, Artisan, Composer, and Node commands with `vendor/bin/sail`. Examples:
    - Run Artisan Commands: `vendor/bin/sail artisan migrate`
    - Install Composer packages: `vendor/bin/sail composer install`
    - Execute Node commands: `vendor/bin/sail bun run dev`
    - Execute PHP scripts: `vendor/bin/sail php [script]`
- View all available Sail commands by running `vendor/bin/sail` without arguments.

=== tests rules ===

# Test Enforcement

- Add or update tests for behavior and logic changes when a test provides meaningful regression coverage.
- Pure copy, styling, and layout-only changes do not require new or updated tests.
- When test coverage applies, run the affected tests and ensure they pass.
- Test the changed behavior and its important failure modes, but do not add tests beyond them.
- Read the `testing-best-practices` skill before writing tests.

=== inertia-laravel/core rules ===

# Inertia

- Inertia creates fully client-side rendered SPAs without modern SPA complexity, leveraging existing server-side patterns.
- Components live in `resources/js/pages` (unless specified in `vite.config.js`). Use `Inertia::render()` for server-side routing instead of Blade views.
- ALWAYS use `search-docs` tool for version-specific Inertia documentation and updated code examples.
- IMPORTANT: Activate `inertia-vue-development` when working with Inertia Vue client-side patterns.

# Inertia v3

- Use all Inertia features from v1, v2, and v3. Check the documentation before making changes to ensure the correct approach.
- New v3 features: standalone HTTP requests (`useHttp` hook), optimistic updates with automatic rollback, layout props (`useLayoutProps` hook), instant visits, simplified SSR via `@inertiajs/vite` plugin, custom exception handling for error pages.
- Carried over from v2: deferred props, infinite scroll, merging props, polling, prefetching, once props, flash data.
- When using deferred props, add an empty state with a pulsing or animated skeleton.
- Axios has been removed. Use the built-in XHR client with interceptors, or install Axios separately if needed.
- `Inertia::lazy()` / `LazyProp` has been removed. Use `Inertia::optional()` instead.
- Prop types (`Inertia::optional()`, `Inertia::defer()`, `Inertia::merge()`) work inside nested arrays with dot-notation paths.
- SSR works automatically in Vite dev mode with `@inertiajs/vite` - no separate Node.js server needed during development.
- Event renames: `invalid` is now `httpException`, `exception` is now `networkError`.
- `router.cancel()` replaced by `router.cancelAll()`.
- The `future` configuration namespace has been removed - all v2 future options are now always enabled.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `vendor/bin/sail artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using `vendor/bin/sail artisan list` and check their parameters with `vendor/bin/sail artisan [command] --help`.
- If you're creating a generic PHP class, use `vendor/bin/sail artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `vendor/bin/sail artisan make:model --help` to check the available options.

## APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `vendor/bin/sail artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

=== wayfinder/core rules ===

# Laravel Wayfinder

Use Wayfinder to generate TypeScript functions for Laravel routes. Import from `@/actions/` (controllers) or `@/routes/` (named routes).

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `vendor/bin/sail bin pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/sail bin pint --test --format agent`, simply run `vendor/bin/sail bin pint --format agent` to fix any formatting issues.

=== pest/core rules ===

# Pest

- This project uses Pest. Create tests with `vendor/bin/sail artisan make:test --pest {name}`.
- Do not include the test suite directory in `{name}`. Use `SomeFeatureTest`, not `Feature/SomeFeatureTest`.
- Read the `testing-best-practices` skill for guidance on coverage, naming, structure, dependency isolation, and review.
- Do not delete tests or test files without approval. They are part of the application.

## Running Tests

- Run the narrowest set of tests that covers the change. Pass a file path or `--filter=testName` to `vendor/bin/sail artisan test --compact`.
- Rerun a test after each change to it.
- Run `vendor/bin/sail bin pest` to call the test runner directly. It accepts the same file path and `--filter=testName` arguments.

=== inertia-vue/core rules ===

# Inertia + Vue

Vue components must have a single root element.
- IMPORTANT: Activate `inertia-vue-development` when working with Inertia Vue client-side patterns.

</laravel-boost-guidelines>
