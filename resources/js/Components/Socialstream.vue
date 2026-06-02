<script setup>
import InputError from '@/Components/InputError.vue';
import ProviderIcon from '@/Components/SocialstreamIcons/ProviderIcon.vue';

defineProps({
    prompt: {
        type: String,
        default: 'Or Login Via',
    },
    error: {
        type: String,
        default: null,
    },
    providers: {
        type: Array,
    },
    labels: {
        type: Object,
    }
});
</script>

<template>
    <div v-if="providers.length" class="space-y-6 mt-6 mb-2">
        <div class="relative flex items-center">
            <div class="grow border-t border-gray-700"></div>
            <span class="shrink text-gray-500 px-6">
                {{ prompt }}
             </span>
            <div class="grow border-t border-gray-700"></div>
        </div>

        <InputError v-if="error" :message="error" class="text-center"/>

        <div class="grid gap-4">
            <a v-for="provider in providers" :key="provider.id"
               class="flex gap-2 items-center justify-center transition duration-200 border border-gray-700 bg-gray-800 hover:bg-gray-700 hover:border-orange-500 w-full py-2.5 rounded-lg text-sm font-normal text-center"
               :href="route('oauth.redirect', provider.id)">
                <ProviderIcon :provider="provider" classes="h-6 w-6"/>
                <span class="block font-medium text-sm text-gray-200">Login with {{ provider.buttonLabel || provider.name }}</span>
            </a>
        </div>
    </div>
</template>
