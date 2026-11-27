<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import InputError from '@/components/InputError.vue';
import SteamIcon from '@/components/SteamIcon.vue';
import { Button } from '@/components/ui/button';
import { redirect } from '@/routes/oauth';

const page = usePage();
const error = computed(() => page.props.errors?.oauth as string | undefined);
</script>

<template>
    <div class="flex flex-col gap-6">
        <div class="relative flex items-center gap-4">
            <div class="grow border-t border-input" />
            <span class="text-sm text-muted-foreground">Or</span>
            <div class="grow border-t border-input" />
        </div>

        <InputError :message="error" class="text-center" />

        <Button variant="secondary" class="w-full" as-child>
            <a :href="redirect.url({ provider: 'steam' })">
                <SteamIcon class="size-4" />
                Continue with Steam
            </a>
        </Button>
    </div>
</template>
