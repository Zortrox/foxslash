<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Globe } from '@lucide/vue';
import FoxLogo from '@/components/FoxLogo.vue';
import SiteLayout from '@/layouts/SiteLayout.vue';
</script>

<template>
    <Head title="FoxSlash Games" />

    <SiteLayout>
        <div class="flex justify-center">
            <FoxLogo peek class="size-44" />
        </div>

        <section class="mx-auto mt-10 max-w-xl">
            <h1
                class="font-display text-3xl font-semibold text-balance text-white"
            >
                Who am I?
            </h1>

            <p class="mt-4 text-lg leading-relaxed text-muted-foreground">
                I go by
                <span class="font-semibold text-primary">Zortrox</span>
                on most online forums and gaming sites. When I'm not developing
                games, you can usually find me playing
                <span class="text-mint">Steam games</span> or reading
                <span class="text-lilac">science fiction</span> and
                <span class="text-berry">fantasy books</span>.
            </p>

            <ul class="mt-8 flex flex-wrap items-center gap-3">
                <li>
                    <a
                        href="https://bsky.app/profile/zortrox.com"
                        target="_blank"
                        rel="noopener noreferrer"
                        aria-label="Zortrox on Bluesky"
                        title="Bluesky"
                        class="flex size-11 items-center justify-center rounded-full bg-card text-foreground transition-colors hover:bg-bluesky hover:text-white focus-visible:ring-2 focus-visible:ring-ring focus-visible:outline-none"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="size-5"
                            fill="currentColor"
                            viewBox="0 0 16 16"
                            aria-hidden="true"
                        >
                            <path
                                d="M3.468 1.948C5.303 3.325 7.276 6.118 8 7.616c.725-1.498 2.698-4.29 4.532-5.668C13.855.955 16 .186 16 2.632c0 .489-.28 4.105-.444 4.692-.572 2.04-2.653 2.561-4.504 2.246 3.236.551 4.06 2.375 2.281 4.2-3.376 3.464-4.852-.87-5.23-1.98-.07-.204-.103-.3-.103-.218 0-.081-.033.014-.102.218-.379 1.11-1.855 5.444-5.231 1.98-1.778-1.825-.955-3.65 2.28-4.2-1.85.315-3.932-.205-4.503-2.246C.28 6.737 0 3.12 0 2.632 0 .186 2.145.955 3.468 1.948"
                            />
                        </svg>
                    </a>
                </li>
                <li>
                    <a
                        href="https://matthew-clark.xyz/"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="flex h-11 items-center gap-2 rounded-full bg-card px-4 text-sm font-medium transition-colors hover:text-accent-foreground focus-visible:ring-2 focus-visible:ring-ring focus-visible:outline-none"
                    >
                        <Globe class="size-4 text-mint" aria-hidden="true" />
                        matthew-clark.xyz
                    </a>
                </li>
            </ul>
        </section>
    </SiteLayout>
</template>
