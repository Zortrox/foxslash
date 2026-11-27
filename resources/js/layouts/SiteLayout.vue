<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import FoxLogo from '@/components/FoxLogo.vue';
import WalkingFox from '@/components/WalkingFox.vue';
import { dashboard, login, register, steamchecker } from '@/routes';

/**
 * The shell for public personal pages: wordmark header, the meadow with the
 * walking fox, and the footer.
 */
</script>

<template>
    <div class="flex min-h-svh flex-col">
        <header>
            <div
                class="mx-auto flex w-full max-w-4xl items-center justify-between gap-4 px-6 py-5"
            >
                <Link
                    href="/"
                    class="flex items-center gap-2 rounded-lg focus-visible:ring-2 focus-visible:ring-ring focus-visible:outline-none"
                >
                    <FoxLogo class="size-10" />
                    <span class="font-display text-xl font-semibold">
                        <span class="text-primary">Fox</span
                        ><span class="text-sun">Slash</span>
                    </span>
                </Link>

                <nav class="flex items-center gap-1 text-sm font-medium">
                    <Link
                        :href="steamchecker()"
                        class="rounded-full px-4 py-2 transition-colors hover:bg-card hover:text-accent-foreground max-sm:hidden"
                    >
                        SteamChecker
                    </Link>
                    <Link
                        v-if="$page.props.auth.user"
                        :href="dashboard()"
                        class="rounded-full px-4 py-2 transition-colors hover:bg-card hover:text-accent-foreground"
                    >
                        Dashboard
                    </Link>
                    <template v-else>
                        <Link
                            :href="login()"
                            class="rounded-full px-4 py-2 transition-colors hover:bg-card hover:text-accent-foreground"
                        >
                            Log in
                        </Link>
                        <Link
                            :href="register()"
                            class="rounded-full bg-primary px-4 py-2 text-primary-foreground transition-colors hover:bg-primary/90"
                        >
                            Register
                        </Link>
                    </template>
                </nav>
            </div>
        </header>

        <main class="mx-auto w-full max-w-4xl flex-1 px-6 pt-10 pb-6">
            <slot />
        </main>

        <WalkingFox />

        <footer
            class="bg-card py-4 text-center font-display text-sm text-muted-foreground"
        >
            FoxSlash Games
        </footer>
    </div>
</template>
