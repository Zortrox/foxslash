<script setup lang="ts">
/**
 * A small fox trotting back and forth across a night meadow.
 * Purely decorative: hidden from assistive tech, and it stands still
 * when the visitor prefers reduced motion.
 */
const tufts = [6, 17, 29, 41, 53, 66, 78, 90];
const flowers = [
    { left: 11, color: 'var(--color-berry)' },
    { left: 36, color: 'var(--color-lilac)' },
    { left: 61, color: 'var(--color-sun)' },
    { left: 84, color: 'var(--color-berry)' },
];
const stars = [
    { left: 8, top: 18, size: 10 },
    { left: 27, top: 52, size: 7 },
    { left: 47, top: 10, size: 12 },
    { left: 71, top: 38, size: 8 },
    { left: 91, top: 14, size: 11 },
];
</script>

<template>
    <div class="meadow" aria-hidden="true">
        <svg
            v-for="(star, index) in stars"
            :key="`star-${index}`"
            class="meadow__star"
            viewBox="0 0 10 10"
            :style="{
                left: `${star.left}%`,
                top: `${star.top}%`,
                width: `${star.size}px`,
            }"
        >
            <path
                d="M5 0 C5.4 3.4 6.6 4.6 10 5 C6.6 5.4 5.4 6.6 5 10 C4.6 6.6 3.4 5.4 0 5 C3.4 4.6 4.6 3.4 5 0 Z"
                fill="currentColor"
            />
        </svg>

        <div class="meadow__ground">
            <svg
                v-for="left in tufts"
                :key="`tuft-${left}`"
                class="meadow__tuft"
                viewBox="0 0 20 12"
                :style="{ left: `${left}%` }"
            >
                <path
                    d="M2 12 Q4 4 6 12 M8 12 Q10 1 12 12 M14 12 Q16 5 18 12"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                />
            </svg>

            <svg
                v-for="flower in flowers"
                :key="`flower-${flower.left}`"
                class="meadow__flower"
                viewBox="0 0 12 18"
                :style="{ left: `${flower.left}%`, color: flower.color }"
            >
                <path
                    d="M6 18 V9"
                    stroke="var(--color-mint)"
                    stroke-width="1.5"
                    stroke-linecap="round"
                />
                <circle cx="6" cy="3" r="2.4" fill="currentColor" />
                <circle cx="9.2" cy="5.4" r="2.4" fill="currentColor" />
                <circle cx="8" cy="9" r="2.4" fill="currentColor" />
                <circle cx="4" cy="9" r="2.4" fill="currentColor" />
                <circle cx="2.8" cy="5.4" r="2.4" fill="currentColor" />
                <circle cx="6" cy="6.2" r="1.8" fill="#fff3e4" />
            </svg>
        </div>

        <div class="meadow__track">
            <div class="meadow__walker">
                <svg class="fox" viewBox="0 0 140 90">
                    <g class="fox__tail">
                        <path
                            d="M44 50 C30 50 14 44 8 30 C4 21 10 14 16 20 C22 28 30 36 46 40 Z"
                            fill="#f97a1f"
                        />
                        <path
                            d="M14.5 18.5 C10 14.5 4.5 19 7.5 27 C9 31 11.5 33.5 14 35 C13 29 12.5 23 14.5 18.5 Z"
                            fill="#fff3e4"
                        />
                    </g>
                    <g class="fox__leg fox__leg--a">
                        <rect
                            x="49"
                            y="52"
                            width="7"
                            height="30"
                            rx="3.5"
                            fill="#c9561a"
                        />
                        <rect
                            x="49"
                            y="70"
                            width="7"
                            height="12"
                            rx="3.5"
                            fill="#2a120a"
                        />
                    </g>
                    <g class="fox__leg fox__leg--b">
                        <rect
                            x="88"
                            y="52"
                            width="7"
                            height="30"
                            rx="3.5"
                            fill="#c9561a"
                        />
                        <rect
                            x="88"
                            y="70"
                            width="7"
                            height="12"
                            rx="3.5"
                            fill="#2a120a"
                        />
                    </g>
                    <g class="fox__body">
                        <path
                            d="M40 50 C40 40 52 36 70 36 C88 36 100 40 100 50 C100 60 88 63 70 63 C52 63 40 60 40 50 Z"
                            fill="#f97a1f"
                        />
                        <path
                            d="M58 60 C66 63 84 63 92 58 C90 61 82 64 72 64 C64 64 60 62 58 60 Z"
                            fill="#fff3e4"
                        />
                        <g
                            transform="translate(100 40) scale(1.14) translate(-100 -40)"
                        >
                            <!--
                                Ears first so the head covers their bases. They sweep back as
                                it trots. The far ear is smaller and darker, seen from behind.
                                Each ear clips its tip (and the near one its inside) to its outline.
                            -->
                            <defs>
                                <clipPath id="fox-ear-far">
                                    <path
                                        d="M100.5 34 C101 24 104 15 106.4 10.2 Q107.5 8.2 108.6 10.2 C111 15 114 24 114.5 34 Z"
                                    />
                                </clipPath>
                                <clipPath id="fox-ear-near">
                                    <path
                                        d="M106.5 34 C107 24 110 15 112.4 10.2 Q113.5 8.2 114.6 10.2 C117 15 120 24 120.5 34 Z"
                                    />
                                </clipPath>
                            </defs>
                            <g
                                transform="rotate(-28 107.5 34) translate(107.5 34) scale(0.82) translate(-107.5 -34)"
                            >
                                <path
                                    d="M100.5 34 C101 24 104 15 106.4 10.2 Q107.5 8.2 108.6 10.2 C111 15 114 24 114.5 34 Z"
                                    fill="#b8501a"
                                />
                                <path
                                    d="M97.5 0 H119.5 V11.8 Q107.5 14.4 97.5 11.8 Z"
                                    fill="#4a2414"
                                    clip-path="url(#fox-ear-far)"
                                />
                            </g>
                            <g transform="rotate(-22 113.5 34)">
                                <g class="fox__ear">
                                    <path
                                        d="M106.5 34 C107 24 110 15 112.4 10.2 Q113.5 8.2 114.6 10.2 C117 15 120 24 120.5 34 Z"
                                        fill="#f97a1f"
                                    />
                                    <g clip-path="url(#fox-ear-near)">
                                        <path
                                            d="M110.1 34 C110.5 25 112 18 113.7 13.5 C115.3 18 117.1 25 117.7 34 Z"
                                            fill="#fff3e4"
                                        />
                                        <path
                                            d="M103.5 0 H125.5 V11.8 Q113.5 14.4 103.5 11.8 Z"
                                            fill="#4a2414"
                                        />
                                    </g>
                                </g>
                            </g>
                            <path
                                d="M92 44 C94 34 98 28 106 26 C114 24 122 28 124 34 L133 38.5 C134.6 39.4 134.4 41.6 132.6 42 L122 44.6 C118 50 110 52 103 50 C97 48.5 93 47 92 44 Z"
                                fill="#f97a1f"
                            />
                            <path
                                d="M132.6 42 L122 44.6 C118 50 110 52 103 50 C108 48 112 45 116 43 C120 41.4 126 40.6 133.4 40.2 C133.6 41 133.3 41.8 132.6 42 Z"
                                fill="#fff3e4"
                            />
                            <ellipse
                                cx="133"
                                cy="39.2"
                                rx="2"
                                ry="1.6"
                                fill="#2a120a"
                            />
                            <g class="fox__eye">
                                <ellipse
                                    cx="114.5"
                                    cy="34"
                                    rx="2.2"
                                    ry="2.8"
                                    fill="#2a120a"
                                />
                                <circle
                                    cx="115.3"
                                    cy="33"
                                    r="0.9"
                                    fill="#fff"
                                />
                            </g>
                            <ellipse
                                cx="112"
                                cy="41.5"
                                rx="3"
                                ry="1.7"
                                fill="#ff8fb1"
                            />
                        </g>
                    </g>
                    <g class="fox__leg fox__leg--b">
                        <rect
                            x="45"
                            y="52"
                            width="7.5"
                            height="30"
                            rx="3.75"
                            fill="#f97a1f"
                        />
                        <rect
                            x="45"
                            y="70"
                            width="7.5"
                            height="12"
                            rx="3.75"
                            fill="#3a1a10"
                        />
                    </g>
                    <g class="fox__leg fox__leg--a">
                        <rect
                            x="84"
                            y="52"
                            width="7.5"
                            height="30"
                            rx="3.75"
                            fill="#f97a1f"
                        />
                        <rect
                            x="84"
                            y="70"
                            width="7.5"
                            height="12"
                            rx="3.75"
                            fill="#3a1a10"
                        />
                    </g>
                </svg>
            </div>
        </div>
    </div>
</template>

<style scoped>
.meadow {
    position: relative;
    height: 10rem;
    overflow: hidden;
}

.meadow__star {
    position: absolute;
    color: var(--color-sun);
    opacity: 0.8;
}

.meadow__star:nth-of-type(even) {
    color: var(--color-lilac);
}

.meadow__ground {
    position: absolute;
    inset: auto 0 0;
    height: 1.75rem;
    border-top: 2px solid oklch(0.8 0.12 170 / 0.55);
    background: oklch(0.8 0.12 170 / 0.08);
}

.meadow__tuft {
    position: absolute;
    bottom: 100%;
    width: 1.25rem;
    color: var(--color-mint);
}

.meadow__flower {
    position: absolute;
    bottom: calc(100% - 2px);
    width: 0.8rem;
}

.meadow__track {
    position: absolute;
    /* Feet sit at y=82 of the 90-unit viewBox, so drop the track by that margin. */
    inset: auto 0 calc(1.75rem - 2px - 8.5rem * 8 / 140);
    container-type: inline-size;
}

.meadow__walker {
    width: 8.5rem;
    transform: translateX(62cqw);
}

.fox {
    display: block;
    width: 100%;
    overflow: visible;
}

.fox__leg,
.fox__tail,
.fox__ear {
    transform-box: fill-box;
}

.fox__leg {
    transform-origin: 50% 8%;
}

.fox__tail {
    transform-origin: 100% 80%;
}

.fox__ear {
    transform-origin: 20% 100%;
}

.fox__eye {
    transform-box: fill-box;
    transform-origin: center;
}

@media (prefers-reduced-motion: no-preference) {
    .meadow__walker {
        animation: trot 26s linear infinite;
    }

    .fox__leg {
        animation: stride 0.7s ease-in-out infinite alternate;
    }

    .fox__leg--b {
        animation-delay: -0.7s;
    }

    .fox__body {
        animation: bob 0.35s ease-in-out infinite alternate;
    }

    .fox__tail {
        animation: sway 1.4s ease-in-out infinite alternate;
    }

    .fox__ear {
        animation: twitch 5s ease-in-out infinite;
    }

    .fox__eye {
        animation: blink 4.2s ease-in-out infinite;
    }
}

/* Walk right, turn, walk back, turn. The turn is a quick flip in place. */
@keyframes trot {
    0% {
        transform: translateX(-8.5rem) scaleX(1);
    }
    48% {
        transform: translateX(calc(100cqw)) scaleX(1);
    }
    50% {
        transform: translateX(calc(100cqw)) scaleX(-1);
    }
    98% {
        transform: translateX(-8.5rem) scaleX(-1);
    }
    100% {
        transform: translateX(-8.5rem) scaleX(1);
    }
}

@keyframes stride {
    from {
        transform: rotate(24deg);
    }
    to {
        transform: rotate(-24deg);
    }
}

@keyframes bob {
    from {
        transform: translateY(0);
    }
    to {
        transform: translateY(-2px);
    }
}

@keyframes sway {
    from {
        transform: rotate(-7deg);
    }
    to {
        transform: rotate(6deg);
    }
}

@keyframes twitch {
    0%,
    90%,
    100% {
        transform: rotate(0);
    }
    93% {
        transform: rotate(-12deg);
    }
    96% {
        transform: rotate(4deg);
    }
}

@keyframes blink {
    0%,
    94%,
    100% {
        transform: scaleY(1);
    }
    97% {
        transform: scaleY(0.1);
    }
}
</style>
