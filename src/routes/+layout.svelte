<script>
	import '../app.css';
	import { page } from '$app/stores';
	import Background from '$lib/background.svelte';
	import WalkingFox from '$lib/walking-fox.svelte';
</script>

<svelte:head>
	<title>{$page.data.title} - 🦊⨏𝑜𝑥/</title>
</svelte:head>

<Background />

<!-- <WalkingFox /> -->

<div
	class="bg-neutral-100 bg-opacity-90 dark:bg-neutral-800 dark:bg-opacity-90 text-black dark:text-white px-5 py-3 sticky top-0 shadow-md"
>
	<nav class="flex gap-6 text-xl max-w-4xl mx-auto">
		<a href="/" class="no-underline hover:underline">Home</a>
		<a href="/apps/steamchecker" class="no-underline hover:underline">Steamchecker</a>
		<a href="/about" class="no-underline hover:underline">About Me</a>
		<a href="/contact" class="no-underline hover:underline">Contact</a>
	</nav>
</div>

<div class="mt-5 max-w-4xl mx-auto">
	<slot />
</div>
