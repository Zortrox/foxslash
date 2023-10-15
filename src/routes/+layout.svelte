<script>
	import '../app.css';
</script>

<div class="bg-white dark:bg-neutral-700 text-black dark:text-white px-5 py-3">
	<nav class="flex gap-6 text-xl max-w-4xl mx-auto">
		<a href="/">Home</a>
		<a href="/apps/steamchecker">Steamchecker</a>
		<a href="/about">About Me</a>
		<a href="/contact">Contact</a>
	</nav>
</div>

<div class="mt-5 max-w-4xl mx-auto">
	<slot />
</div>
