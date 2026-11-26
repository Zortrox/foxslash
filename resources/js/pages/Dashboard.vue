<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import Heading from '@/components/Heading.vue';
import SteamIcon from '@/components/SteamIcon.vue';
import { Button } from '@/components/ui/button';
import { dashboard } from '@/routes';
import { edit as editProfile } from '@/routes/profile';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: dashboard(),
            },
        ],
    },
});

const page = usePage();
const hasSteam = computed(() =>
    (
        page.props.oauth as { connectedAccounts: { provider: string }[] }
    ).connectedAccounts.some((account) => account.provider === 'steam'),
);
</script>

<template>
    <Head title="Dashboard" />

    <div class="flex flex-1 flex-col gap-6 p-4">
        <Heading
            title="Dashboard"
            description="Your foxslash account and the steamchecker extension"
        />

        <div class="max-w-xl rounded-lg border border-border bg-card p-6">
            <div class="flex items-start gap-4">
                <SteamIcon class="size-8 shrink-0 text-accent-foreground" />
                <div class="space-y-2">
                    <h2 class="font-medium">steamchecker</h2>
                    <p class="text-sm text-muted-foreground">
                        {{
                            hasSteam
                                ? 'Your Steam account is connected. More steamchecker settings will show up here.'
                                : 'Connect your Steam account so steamchecker can use your library and wishlist.'
                        }}
                    </p>
                    <Button
                        v-if="!hasSteam"
                        variant="secondary"
                        size="sm"
                        as-child
                    >
                        <Link :href="editProfile()">Connect Steam</Link>
                    </Button>
                </div>
            </div>
        </div>
    </div>
</template>
