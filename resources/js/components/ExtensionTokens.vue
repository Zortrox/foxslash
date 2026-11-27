<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import SecurityController from '@/actions/App/Http/Controllers/Settings/SecurityController';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';

export type ExtensionToken = {
    id: number;
    name: string;
    created_at_diff: string | null;
    last_used_at_diff: string | null;
};

defineProps<{
    tokens: ExtensionToken[];
}>();
</script>

<template>
    <div class="space-y-6">
        <Heading
            variant="small"
            title="SteamChecker"
            description="Browsers where the SteamChecker extension is signed in to your account"
        />

        <p v-if="tokens.length === 0" class="text-sm text-muted-foreground">
            SteamChecker isn't connected anywhere. Sign in from the extension's
            popup to connect it.
        </p>

        <ul v-else class="space-y-3">
            <li
                v-for="token in tokens"
                :key="token.id"
                class="flex items-center justify-between gap-4 rounded-lg border border-border p-4"
            >
                <div class="min-w-0">
                    <p class="truncate font-medium">{{ token.name }}</p>
                    <p class="text-sm text-muted-foreground">
                        Connected {{ token.created_at_diff }}
                        <template v-if="token.last_used_at_diff">
                            · last used {{ token.last_used_at_diff }}
                        </template>
                    </p>
                </div>

                <Form
                    v-bind="
                        SecurityController.destroyExtensionToken.form({
                            token: token.id,
                        })
                    "
                    :options="{ preserveScroll: true }"
                    v-slot="{ processing }"
                >
                    <Button
                        type="submit"
                        variant="secondary"
                        :disabled="processing"
                    >
                        Disconnect
                    </Button>
                </Form>
            </li>
        </ul>
    </div>
</template>
