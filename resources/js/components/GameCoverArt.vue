<script setup lang="ts">
/**
 * A tiny low-poly scene that stands in for a game's cover art on the
 * SteamChecker page's example tiles. Decorative only.
 */
defineProps<{
    scene: 'peaks' | 'space' | 'castle';
}>();
</script>

<template>
    <svg
        viewBox="0 0 40 30"
        preserveAspectRatio="xMidYMid slice"
        aria-hidden="true"
        class="block"
        shape-rendering="geometricPrecision"
    >
        <template v-if="scene === 'peaks'">
            <rect width="40" height="30" fill="oklch(0.3 0.08 300)" />
            <polygon points="0,0 40,0 40,12 0,16" fill="oklch(0.36 0.1 300)" />
            <polygon
                points="27,5 31,4 33,7 31,10 27,10 25,7"
                fill="var(--color-sun)"
            />
            <polygon points="0,24 9,9 17,20" fill="var(--color-lilac)" />
            <polygon points="9,9 17,20 12,22" fill="oklch(0.6 0.12 300)" />
            <polygon points="11,24 22,6 34,24" fill="oklch(0.66 0.12 300)" />
            <polygon points="22,6 34,24 26,24" fill="oklch(0.54 0.12 300)" />
            <polygon points="19,10 22,6 25,11 22,10" fill="white" />
            <polygon
                points="28,24 37,13 40,16 40,24"
                fill="var(--color-lilac)"
            />
            <polygon
                points="0,22 14,21 26,23 40,21 40,30 0,30"
                fill="var(--color-mint)"
            />
            <polygon
                points="0,26 18,24 40,27 40,30 0,30"
                fill="oklch(0.62 0.12 170)"
            />
        </template>

        <template v-else-if="scene === 'space'">
            <rect width="40" height="30" fill="oklch(0.2 0.05 260)" />
            <polygon points="4,4 5,5 4,6 3,5" fill="var(--color-sun)" />
            <polygon points="16,3 17,4 16,5 15,4" fill="white" />
            <polygon points="35,6 36,7 35,8 34,7" fill="var(--color-lilac)" />
            <polygon points="9,24 10,25 9,26 8,25" fill="white" />
            <polygon
                points="22,12 30,8 37,12 38,20 32,26 24,25 20,19"
                fill="var(--color-mint)"
            />
            <polygon
                points="22,12 30,8 29,16 20,19"
                fill="oklch(0.88 0.09 170)"
            />
            <polygon points="29,16 38,20 32,26" fill="oklch(0.62 0.12 170)" />
            <polygon
                points="16,18 40,13 40,15 18,19"
                fill="var(--color-berry)"
            />
            <polygon points="5,17 14,13 11,18" fill="var(--color-primary)" />
            <polygon points="5,17 11,18 7,20" fill="oklch(0.6 0.2 45)" />
            <polygon points="2,19 5,17 7,20" fill="var(--color-sun)" />
        </template>

        <template v-else>
            <rect width="40" height="30" fill="oklch(0.34 0.1 355)" />
            <polygon points="0,0 40,0 40,10 0,14" fill="oklch(0.27 0.08 300)" />
            <polygon points="6,4 9,3 10,6 8,8 5,7" fill="var(--color-sun)" />
            <polygon
                points="0,30 0,20 12,17 26,19 40,16 40,30"
                fill="oklch(0.42 0.1 160)"
            />
            <polygon
                points="13,22 13,12 16,12 16,9 18,12 18,22"
                fill="oklch(0.45 0.03 260)"
            />
            <polygon
                points="18,22 18,14 26,14 26,22"
                fill="oklch(0.55 0.03 260)"
            />
            <polygon
                points="26,22 26,11 29,11 29,8 31,11 31,22"
                fill="oklch(0.45 0.03 260)"
            />
            <polygon
                points="12.5,12 14.5,8 16.5,12"
                fill="var(--color-berry)"
            />
            <polygon
                points="25.5,11 28,6.5 30.5,11"
                fill="var(--color-berry)"
            />
            <polygon
                points="20.5,22 20.5,18 22,16.5 23.5,18 23.5,22"
                fill="oklch(0.25 0.04 260)"
            />
            <polygon
                points="14.2,15 15.2,15 15.2,17 14.2,17"
                fill="var(--color-sun)"
            />
            <polygon
                points="27.8,14 28.8,14 28.8,16 27.8,16"
                fill="var(--color-sun)"
            />
            <polygon
                points="0,30 0,25 20,23 40,26 40,30"
                fill="oklch(0.36 0.09 160)"
            />
        </template>
    </svg>
</template>
