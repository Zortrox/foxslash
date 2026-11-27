<script setup lang="ts">
import { Deferred, Head, Link } from '@inertiajs/vue3';
import { ExternalLink, Gift } from '@lucide/vue';
import GameCoverArt from '@/components/GameCoverArt.vue';
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

/** Browser logos from Simple Icons (CC0), 24x24 paths. */
const browsers = {
    chrome: {
        name: 'Chrome',
        color: 'text-sun',
        path: 'M12 0C8.21 0 4.831 1.757 2.632 4.501l3.953 6.848A5.454 5.454 0 0 1 12 6.545h10.691A12 12 0 0 0 12 0zM1.931 5.47A11.943 11.943 0 0 0 0 12c0 6.012 4.42 10.991 10.189 11.864l3.953-6.847a5.45 5.45 0 0 1-6.865-2.29zm13.342 2.166a5.446 5.446 0 0 1 1.45 7.09l.002.001h-.002l-5.344 9.257c.206.01.413.016.621.016 6.627 0 12-5.373 12-12 0-1.54-.29-3.011-.818-4.364zM12 16.364a4.364 4.364 0 1 1 0-8.728 4.364 4.364 0 0 1 0 8.728Z',
    },
    brave: {
        name: 'Brave',
        color: 'text-primary',
        path: 'M15.68 0l2.096 2.38s1.84-.512 2.709.358c.868.87 1.584 1.638 1.584 1.638l-.562 1.381.715 2.047s-2.104 7.98-2.35 8.955c-.486 1.919-.818 2.66-2.198 3.633-1.38.972-3.884 2.66-4.293 2.916-.409.256-.92.692-1.38.692-.46 0-.97-.436-1.38-.692a185.796 185.796 0 01-4.293-2.916c-1.38-.973-1.712-1.714-2.197-3.633-.247-.975-2.351-8.955-2.351-8.955l.715-2.047-.562-1.381s.716-.768 1.585-1.638c.868-.87 2.708-.358 2.708-.358L8.321 0h7.36zm-3.679 14.936c-.14 0-1.038.317-1.758.69-.72.373-1.242.637-1.409.742-.167.104-.065.301.087.409.152.107 2.194 1.69 2.393 1.866.198.175.489.464.687.464.198 0 .49-.29.688-.464.198-.175 2.24-1.759 2.392-1.866.152-.108.254-.305.087-.41-.167-.104-.689-.368-1.41-.741-.72-.373-1.617-.69-1.757-.69zm0-11.278s-.409.001-1.022.206-1.278.46-1.584.46c-.307 0-2.581-.434-2.581-.434S4.119 7.152 4.119 7.849c0 .697.339.881.68 1.243l2.02 2.149c.192.203.59.511.356 1.066-.235.555-.58 1.26-.196 1.977.384.716 1.042 1.194 1.464 1.115.421-.08 1.412-.598 1.776-.834.364-.237 1.518-1.19 1.518-1.554 0-.365-1.193-1.02-1.413-1.168-.22-.15-1.226-.725-1.247-.95-.02-.227-.012-.293.284-.851.297-.559.831-1.304.742-1.8-.089-.495-.95-.753-1.565-.986-.615-.232-1.799-.671-1.947-.74-.148-.068-.11-.133.339-.175.448-.043 1.719-.212 2.292-.052.573.16 1.552.403 1.632.532.079.13.149.134.067.579-.081.445-.5 2.581-.541 2.96-.04.38-.12.63.288.724.409.094 1.097.256 1.333.256s.924-.162 1.333-.256c.408-.093.329-.344.288-.723-.04-.38-.46-2.516-.541-2.961-.082-.445-.012-.45.067-.579.08-.129 1.059-.372 1.632-.532.573-.16 1.845.009 2.292.052.449.042.487.107.339.175-.148.069-1.332.508-1.947.74-.615.233-1.476.49-1.565.986-.09.496.445 1.241.742 1.8.297.558.304.624.284.85-.02.226-1.026.802-1.247.95-.22.15-1.413.804-1.413 1.169 0 .364 1.154 1.317 1.518 1.554.364.236 1.355.755 1.776.834.422.079 1.08-.4 1.464-1.115.384-.716.039-1.422-.195-1.977-.235-.555.163-.863.355-1.066l2.02-2.149c.341-.362.68-.546.68-1.243 0-.697-2.695-3.96-2.695-3.96s-2.274.436-2.58.436c-.307 0-.972-.256-1.585-.461-.613-.205-1.022-.206-1.022-.206z',
    },
    vivaldi: {
        name: 'Vivaldi',
        color: 'text-berry',
        path: 'M12 0C6.75 0 3.817 0 1.912 1.904.007 3.81 0 6.75 0 12s0 8.175 1.912 10.08C3.825 23.985 6.75 24 12 24c5.25 0 8.183 0 10.088-1.904C23.993 20.19 24 17.25 24 12s0-8.175-1.912-10.08C20.175.015 17.25 0 12 0zm-.168 3a9 9 0 016.49 2.648 9 9 0 010 12.704A9 9 0 1111.832 3zM7.568 7.496a1.433 1.433 0 00-.142.004A1.5 1.5 0 006.21 9.75l1.701 3c.93 1.582 1.839 3.202 2.791 4.822a1.417 1.417 0 001.41.75 1.5 1.5 0 001.223-.81l4.447-7.762A1.56 1.56 0 0018 8.768a1.5 1.5 0 10-2.828.914 2.513 2.513 0 01.256 1.119v.246a2.393 2.393 0 01-2.52 2.13 2.348 2.348 0 01-1.965-1.214c-.307-.51-.6-1.035-.9-1.553-.42-.72-.826-1.41-1.246-2.16a1.433 1.433 0 00-1.229-.754Z',
    },
    firefox: {
        name: 'Firefox',
        color: 'text-primary',
        path: 'M8.824 7.287c.008 0 .004 0 0 0zm-2.8-1.4c.006 0 .003 0 0 0zm16.754 2.161c-.505-1.215-1.53-2.528-2.333-2.943.654 1.283 1.033 2.57 1.177 3.53l.002.02c-1.314-3.278-3.544-4.6-5.366-7.477-.091-.147-.184-.292-.273-.446a3.545 3.545 0 01-.13-.24 2.118 2.118 0 01-.172-.46.03.03 0 00-.027-.03.038.038 0 00-.021 0l-.006.001a.037.037 0 00-.01.005L15.624 0c-2.585 1.515-3.657 4.168-3.932 5.856a6.197 6.197 0 00-2.305.587.297.297 0 00-.147.37c.057.162.24.24.396.17a5.622 5.622 0 012.008-.523l.067-.005a5.847 5.847 0 011.957.222l.095.03a5.816 5.816 0 01.616.228c.08.036.16.073.238.112l.107.055a5.835 5.835 0 01.368.211 5.953 5.953 0 012.034 2.104c-.62-.437-1.733-.868-2.803-.681 4.183 2.09 3.06 9.292-2.737 9.02a5.164 5.164 0 01-1.513-.292 4.42 4.42 0 01-.538-.232c-1.42-.735-2.593-2.121-2.74-3.806 0 0 .537-2 3.845-2 .357 0 1.38-.998 1.398-1.287-.005-.095-2.029-.9-2.817-1.677-.422-.416-.622-.616-.8-.767a3.47 3.47 0 00-.301-.227 5.388 5.388 0 01-.032-2.842c-1.195.544-2.124 1.403-2.8 2.163h-.006c-.46-.584-.428-2.51-.402-2.913-.006-.025-.343.176-.389.206-.406.29-.787.616-1.136.974-.397.403-.76.839-1.085 1.303a9.816 9.816 0 00-1.562 3.52c-.003.013-.11.487-.19 1.073-.013.09-.026.181-.037.272a7.8 7.8 0 00-.069.667l-.002.034-.023.387-.001.06C.386 18.795 5.593 24 12.016 24c5.752 0 10.527-4.176 11.463-9.661.02-.149.035-.298.052-.448.232-1.994-.025-4.09-.753-5.844z',
    },
};

const stores = [
    {
        name: 'Chrome Web Store',
        browsers: [browsers.chrome, browsers.brave, browsers.vivaldi],
        href: 'https://chromewebstore.google.com/detail/steamchecker/eopegaefgepfdedhecfbclehhffiebpk',
    },
    {
        name: 'Firefox Add-ons',
        browsers: [browsers.firefox],
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
                    <GameCoverArt scene="peaks" class="tile__art" />
                    <span class="tile__name">New to you</span>
                </div>
                <div class="tile tile--owned">
                    <span class="tile__badge">Owned</span>
                    <GameCoverArt scene="space" class="tile__art" />
                    <span class="tile__name">Already owned</span>
                </div>
                <div class="tile tile--wished">
                    <span class="tile__gift">
                        <Gift class="size-4" />
                    </span>
                    <GameCoverArt scene="castle" class="tile__art" />
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
                            class="flex flex-col gap-1.5 rounded-xl bg-card px-4 py-2.5 text-sm font-medium transition-colors hover:text-accent-foreground focus-visible:ring-2 focus-visible:ring-ring focus-visible:outline-none"
                        >
                            {{ store.name }}
                            <span
                                class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs font-normal text-muted-foreground"
                            >
                                <span
                                    v-for="browser in store.browsers"
                                    :key="browser.name"
                                    class="flex items-center gap-1"
                                >
                                    <svg
                                        viewBox="0 0 24 24"
                                        fill="currentColor"
                                        class="size-3.5"
                                        :class="browser.color"
                                        aria-hidden="true"
                                    >
                                        <path :d="browser.path" />
                                    </svg>
                                    {{ browser.name }}
                                </span>
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
    @apply aspect-[4/3] w-full overflow-hidden rounded-md;
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
/* the light gold wash the extension lays over wishlisted cards */
.tile--wished::after {
    @apply pointer-events-none absolute inset-0 z-10 rounded-[inherit];
    content: '';
    background: color-mix(in oklch, var(--color-sun) 28%, transparent);
}
.tile__gift {
    @apply absolute top-3 right-3 z-20 flex items-center justify-center rounded-lg border-2 p-1;
    border-color: oklch(0.2 0.04 85);
    background: var(--color-sun);
    color: oklch(0.2 0.04 85);
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
