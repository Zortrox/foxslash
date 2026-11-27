<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import SiteLayout from '@/layouts/SiteLayout.vue';
</script>

<template>
    <Head title="Privacy policy" />

    <SiteLayout>
        <article class="policy mx-auto max-w-xl">
            <h1
                class="font-display text-3xl font-semibold text-balance text-white"
            >
                Privacy policy
            </h1>
            <p class="text-sm">Last updated November 26, 2026</p>

            <p>
                FoxSlash (foxslash.com) and the SteamChecker browser extension
                are run by one person, Zortrox. This page covers both. The short
                version: I keep only what's needed to sign you in and show your
                Steam games, I don't run ads or tracking, and I don't sell or
                share your data.
            </p>

            <h2>What's stored</h2>
            <ul>
                <li>
                    <strong>Your account:</strong> your name, email address, and
                    a hashed password if you set one. Accounts created with
                    Steam get a placeholder email instead of a real one.
                </li>
                <li>
                    <strong>Your linked Steam account:</strong> your SteamID,
                    Steam profile name and avatar link.
                </li>
                <li>
                    <strong>Security settings:</strong> two-factor
                    authentication secrets (encrypted) and passkeys, if you turn
                    them on.
                </li>
                <li>
                    <strong>Sign-in sessions:</strong> while you're signed in,
                    your session records your IP address and browser so it can
                    stay signed in. The extension gets its own access token,
                    which you can see and revoke under Settings, Security.
                </li>
            </ul>

            <h2>What isn't stored</h2>
            <ul>
                <li>
                    Your game library and wishlist are fetched from Steam when
                    the extension asks for them and kept for 10 minutes to avoid
                    asking Steam again, then discarded.
                </li>
                <li>
                    When the extension looks up alternate game names, it sends
                    the game names shown on the page. These lookups aren't tied
                    to your account.
                </li>
                <li>
                    There are no analytics, advertising or tracking cookies. The
                    site sets only the cookies it needs to sign you in and
                    protect forms.
                </li>
            </ul>

            <h2>Services involved</h2>
            <ul>
                <li>
                    <strong>Steam</strong> for signing in and reading your
                    public game library and wishlist.
                </li>
                <li>
                    <strong>Resend</strong> to send account emails such as
                    password resets and email verification.
                </li>
                <li>
                    <strong>DigitalOcean</strong> hosts the server. Like any web
                    server, it keeps short-lived request logs, and IP addresses
                    are used briefly to rate limit requests.
                </li>
            </ul>

            <h2>The extension</h2>
            <p>
                SteamChecker keeps its sign-in token and a short-lived copy of
                your library and wishlist in your browser's extension storage.
                It only reads the bundle pages it highlights and doesn't collect
                your browsing history. Signing out of the extension revokes its
                token.
            </p>

            <h2>Your choices</h2>
            <p>
                You can change your name and email, disconnect Steam, revoke
                extension tokens, or delete your account from your settings.
                Deleting your account removes it along with your linked Steam
                account, passkeys and tokens.
            </p>

            <h2>Questions</h2>
            <p>
                Reach me on
                <a
                    href="https://bsky.app/profile/zortrox.com"
                    target="_blank"
                    rel="noopener noreferrer"
                    >Bluesky</a
                >. If this policy changes, the date at the top changes with it.
            </p>
        </article>
    </SiteLayout>
</template>

<style scoped>
@reference '../../../css/app.css';

.policy > :not(h1) {
    @apply mt-4 leading-relaxed text-muted-foreground;
}
.policy h2 {
    @apply mt-10 font-display text-xl font-semibold text-white;
}
.policy ul {
    @apply list-disc space-y-2 pl-5 marker:text-primary;
}
.policy strong {
    @apply font-semibold text-foreground;
}
.policy a {
    @apply rounded-sm text-accent-foreground underline underline-offset-4 hover:text-orange-300 focus-visible:ring-2 focus-visible:ring-ring focus-visible:outline-none;
}
</style>
