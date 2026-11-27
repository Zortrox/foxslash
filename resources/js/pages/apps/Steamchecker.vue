<script setup lang="ts">
import { Deferred, Head, Link } from '@inertiajs/vue3';
import { ExternalLink } from '@lucide/vue';
import SiteLayout from '@/layouts/SiteLayout.vue';
import { privacy } from '@/routes/steamchecker';

type Release = {
    version: string;
    published_at: string;
    url: string;
    notes: string;
};

defineProps<{
    repositoryUrl: string;
    releases?: Release[] | null;
}>();

const stores = [
    {
        name: 'Chrome Web Store',
        note: 'Chrome, Brave, Vivaldi',
        href: 'https://chromewebstore.google.com/detail/steamchecker/eopegaefgepfdedhecfbclehhffiebpk',
    },
    {
        name: 'Firefox Add-ons',
        note: 'Firefox',
        href: 'https://addons.mozilla.org/firefox/addon/steamchecker/',
    },
];

const formatDate = (date: string): string =>
    new Date(`${date}T00:00:00Z`).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        timeZone: 'UTC',
    });
</script>

<template>
    <Head title="SteamChecker">
        <meta
            name="description"
            content="SteamChecker is a browser extension that grays out the Steam games you own and highlights your wishlisted games on Humble Bundle."
        />
    </Head>

    <SiteLayout>
        <article class="mx-auto max-w-xl">
            <h1
                class="font-display text-3xl font-semibold text-balance text-white"
            >
                SteamChecker
            </h1>

            <p class="mt-4 text-lg leading-relaxed text-muted-foreground">
                A browser extension for Humble Bundle. It grays out the games
                already in your
                <span class="text-mint">Steam library</span> and highlights the
                ones on your <span class="text-sun">wishlist</span>, so you can
                tell at a glance whether a bundle is worth it.
            </p>

            <div class="mt-8 grid grid-cols-3 gap-3" aria-hidden="true">
                <div class="tile">
                    <div class="tile__art bg-lilac/30" />
                    <span class="tile__name">New to you</span>
                </div>
                <div class="tile tile--owned">
                    <span class="tile__badge">Owned</span>
                    <div class="tile__art bg-mint/30" />
                    <span class="tile__name">Already owned</span>
                </div>
                <div class="tile tile--wished">
                    <div class="tile__art bg-berry/30" />
                    <span class="tile__name">Wishlisted</span>
                </div>
            </div>

            <section class="mt-12">
                <h2 class="font-display text-xl font-semibold text-white">
                    Get it
                </h2>
                <ul class="mt-4 flex flex-wrap gap-3">
                    <li v-for="store in stores" :key="store.name">
                        <a
                            :href="store.href"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="flex h-11 items-center gap-2 rounded-full bg-card px-4 text-sm font-medium transition-colors hover:text-accent-foreground focus-visible:ring-2 focus-visible:ring-ring focus-visible:outline-none"
                        >
                            {{ store.name }}
                            <span class="text-muted-foreground">
                                {{ store.note }}
                            </span>
                        </a>
                    </li>
                </ul>
                <p class="mt-4 text-sm text-muted-foreground">
                    The source is on
                    <a
                        :href="repositoryUrl"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="text-link"
                        >GitHub</a
                    >.
                </p>
            </section>

            <section class="mt-12">
                <h2 class="font-display text-xl font-semibold text-white">
                    How to use it
                </h2>
                <ol class="steps mt-4 space-y-3 leading-relaxed">
                    <li>Install it from your browser's store.</li>
                    <li>
                        Open the extension and sign in with your FoxSlash
                        account, or create one with Steam. That links your Steam
                        profile.
                    </li>
                    <li>
                        Browse Humble Bundle. Games you own fade to gray, and
                        wishlisted games glow gold.
                    </li>
                </ol>
                <p class="mt-4 leading-relaxed text-muted-foreground">
                    Your Steam profile's game details need to be public for your
                    library and wishlist to load. Humble sometimes names a game
                    differently than Steam does; those names are matched by
                    hand, so if a game you own isn't grayed out, let me know on
                    <a
                        href="https://bsky.app/profile/zortrox.com"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="text-link"
                        >Bluesky</a
                    >.
                </p>
                <p class="mt-4 text-sm text-muted-foreground">
                    <Link :href="privacy()" class="text-link"
                        >Privacy policy</Link
                    >
                </p>
            </section>

            <section class="mt-12">
                <h2 class="font-display text-xl font-semibold text-white">
                    Changelog
                </h2>

                <Deferred data="releases">
                    <template #fallback>
                        <div class="mt-4 space-y-3" aria-busy="true">
                            <div
                                class="h-5 w-32 animate-pulse rounded-md bg-card"
                            />
                            <div
                                class="h-4 w-full animate-pulse rounded-md bg-card"
                            />
                            <div
                                class="h-4 w-2/3 animate-pulse rounded-md bg-card"
                            />
                        </div>
                    </template>

                    <p
                        v-if="!releases"
                        class="mt-4 leading-relaxed text-muted-foreground"
                    >
                        The changelog can't be loaded from GitHub right now. See
                        the
                        <a
                            :href="`${repositoryUrl}/releases`"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="text-link"
                            >releases on GitHub</a
                        >
                        instead.
                    </p>

                    <p
                        v-else-if="releases.length === 0"
                        class="mt-4 text-muted-foreground"
                    >
                        No releases yet.
                    </p>

                    <ol v-else class="mt-4 space-y-8">
                        <li v-for="release in releases" :key="release.version">
                            <div class="flex items-baseline gap-3">
                                <h3
                                    class="font-display text-lg font-semibold text-primary"
                                >
                                    {{ release.version }}
                                </h3>
                                <time
                                    :datetime="release.published_at"
                                    class="text-sm text-muted-foreground"
                                >
                                    {{ formatDate(release.published_at) }}
                                </time>
                                <a
                                    :href="release.url"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="ml-auto rounded-md p-1 text-muted-foreground transition-colors hover:text-accent-foreground focus-visible:ring-2 focus-visible:ring-ring focus-visible:outline-none"
                                    :aria-label="`Version ${release.version} on GitHub`"
                                >
                                    <ExternalLink class="size-4" />
                                </a>
                            </div>
                            <!-- Rendered from GitHub markdown on the server with raw HTML stripped. -->
                            <div class="notes mt-2" v-html="release.notes" />
                        </li>
                    </ol>
                </Deferred>
            </section>
        </article>
    </SiteLayout>
</template>

<style scoped>
@reference '../../../css/app.css';

.text-link {
    @apply rounded-sm text-accent-foreground underline decoration-1 underline-offset-4 hover:text-orange-300 focus-visible:ring-2 focus-visible:ring-ring focus-visible:outline-none;
}

.steps {
    counter-reset: step;
}
.steps li {
    @apply relative pl-10;
    counter-increment: step;
}
.steps li::before {
    @apply absolute top-0 left-0 flex size-7 items-center justify-center rounded-full bg-card font-display text-sm font-semibold text-primary;
    content: counter(step);
}

.tile {
    @apply relative flex flex-col gap-2 rounded-lg bg-card p-2 transition-[opacity,filter] duration-300;
}
.tile__art {
    @apply aspect-[4/3] rounded-md;
}
.tile__name {
    @apply px-1 pb-1 text-xs font-medium sm:text-sm;
}
.tile--owned {
    filter: grayscale(1);
    opacity: 0.45;
}
.tile--owned:hover {
    opacity: 0.85;
}
.tile__badge {
    @apply absolute top-3 left-3 rounded-md bg-black/80 px-2 text-xs leading-5 font-bold text-white;
}
.tile--wished {
    outline: 3px solid var(--color-sun);
    outline-offset: -3px;
    box-shadow: 0 0 14px color-mix(in oklch, var(--color-sun) 60%, transparent);
}

.notes :deep(h3) {
    @apply mt-3 text-sm font-semibold text-white;
}
.notes :deep(ul) {
    @apply mt-1 list-disc space-y-1 pl-5 text-muted-foreground marker:text-primary;
}
.notes :deep(p) {
    @apply mt-1 text-muted-foreground;
}
.notes :deep(a) {
    @apply text-accent-foreground underline underline-offset-4 hover:text-orange-300;
}
</style>
