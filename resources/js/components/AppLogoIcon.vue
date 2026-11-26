<script setup lang="ts">
import type { HTMLAttributes } from 'vue';
import FoxLogo from '@/components/FoxLogo.vue';

defineOptions({
    inheritAttrs: false,
});

type Props = {
    className?: HTMLAttributes['class'];
};

defineProps<Props>();
</script>

<template>
    <FoxLogo :class="className" v-bind="$attrs" />
</template>
