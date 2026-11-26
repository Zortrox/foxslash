<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import SteamIcon from '@/components/SteamIcon.vue';
import { Button } from '@/components/ui/button';
import { redirect } from '@/routes/oauth';

defineOptions({
    layout: {
        title: 'Connect SteamChecker',
        description: 'Let the extension check bundle pages against your Steam library and wishlist',
    },
});

defineProps<{
    redirect_uri: string;
    state: string;
    code_challenge: string;
    code_challenge_method: string;
    csrfToken: string;
    steamLinked: boolean;
    steamName: string | null;
    steamAvatar: string | null;
    accountName: string;
}>();
</script>

<template>
    <Head title="Connect SteamChecker" />

    <div v-if="!steamLinked" class="flex flex-col gap-6 text-center">
        <p class="text-sm text-muted-foreground">
            SteamChecker reads your games and wishlist from Steam. Link your
            Steam account to
            <span class="font-medium text-foreground">{{ accountName }}</span>
            first, and you'll come straight back here.
        </p>

        <Button class="w-full" as-child>
            <a :href="redirect.url({ provider: 'steam' })">
                <SteamIcon class="size-4" />
                Link Steam account
            </a>
        </Button>
    </div>

    <form
        v-else
        method="post"
        action="/extension/connect"
        class="flex flex-col gap-6"
    >
        <input type="hidden" name="_token" :value="csrfToken" />
        <input type="hidden" name="redirect_uri" :value="redirect_uri" />
        <input type="hidden" name="state" :value="state" />
        <input type="hidden" name="code_challenge" :value="code_challenge" />
        <input
            type="hidden"
            name="code_challenge_method"
            :value="code_challenge_method"
        />

        <div
            class="flex items-center gap-4 rounded-lg border border-border bg-card p-4"
        >
            <img
                v-if="steamAvatar"
                :src="steamAvatar"
                alt=""
                class="size-12 rounded-full"
            />
            <SteamIcon v-else class="size-12 p-2 text-muted-foreground" />
            <div class="min-w-0">
                <p class="truncate font-medium">{{ steamName ?? 'Steam' }}</p>
                <p class="truncate text-sm text-muted-foreground">
                    foxslash account: {{ accountName }}
                </p>
            </div>
        </div>

        <div class="space-y-2 text-sm text-muted-foreground">
            <p>SteamChecker will be able to read:</p>
            <ul class="list-disc space-y-1 pl-5 text-foreground marker:text-primary">
                <li>the games you own on Steam</li>
                <li>your Steam wishlist</li>
            </ul>
            <p>You can disconnect it any time in Security settings.</p>
        </div>

        <div class="grid grid-cols-2 gap-3">
            <Button
                type="submit"
                name="decision"
                value="cancel"
                variant="secondary"
            >
                Cancel
            </Button>
            <Button type="submit" name="decision" value="allow">
                Allow
            </Button>
        </div>
    </form>
</template>
