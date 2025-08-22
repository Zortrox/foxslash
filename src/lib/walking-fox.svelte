<script lang="ts">
	import { onMount } from 'svelte';

	let fox: HTMLElement;
	let previousTimestamp: number;
	let isGoingRight = true;

	function animateFoxWalk(timestamp: number) {
		if (!previousTimestamp) {
			previousTimestamp = timestamp;
		}

		const currentLeft = parseFloat(getComputedStyle(fox).left);

		if (currentLeft > window.innerWidth + 400) {
			isGoingRight = false;
		} else if (currentLeft < -400 - fox.clientWidth) {
			isGoingRight = true;
		}

		const deltaTime = timestamp - previousTimestamp;
		const deltaLeft = deltaTime * 0.001 * (isGoingRight ? 1 : -1);
		fox.style.left = `${currentLeft + deltaLeft}px`;

		window.requestAnimationFrame(animateFoxWalk);
	}

	onMount(() => {
		const width = document.body.clientWidth;
		const speed = 65;
		const time = (((width + 400) * 2) / speed).toFixed(2);

		window.requestAnimationFrame(animateFoxWalk);
	});
</script>

<div class="fixed bottom-0 overflow-hidden w-full h-[335px] z-[-1]">
	<div bind:this={fox} class="fox">
		<div class="leg-outer">
			<div class="leg">
				<div class="paw">
					<div class="log">
						<div class="log-inner" />
					</div>
				</div>
			</div>
		</div>
		<div class="leg-outer">
			<div class="leg">
				<div class="paw">
					<div class="log">
						<div class="log-inner" />
					</div>
				</div>
			</div>
		</div>
		<div class="hind-leg-outer">
			<div class="hind-leg-outer2">
				<div class="hind-paw">
					<div class="hind-log">
						<div class="hind-log-inner" />
					</div>
				</div>
			</div>
		</div>
		<div class="hind-leg-outer">
			<div class="hind-leg-outer2">
				<div class="hind-paw">
					<div class="hind-log">
						<div class="hind-log-inner" />
					</div>
				</div>
			</div>
		</div>
		<div class="body">
			<div class="head">
				<div class="ears">
					<div class="ear" />
					<div class="ear" />
				</div>
				<div class="face" />
				<div class="snout" />
			</div>
			<div class="tail">
				<div class="tail">
					<div class="tail">
						<div class="tail">
							<div class="tail">
								<div class="tail" />
							</div>
						</div>
					</div>
				</div>
				<div class="tail2">
					<div class="tail">
						<div class="tail">
							<div class="tail">
								<div class="tail">
									<div class="tail" />
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<style>
	@media (prefers-reduced-motion) {
		.fox {
			display: none;
		}
	}
	.fox {
		width: 470px;
		height: 335px;
		min-width: 470px;
		min-height: 335px;
		position: absolute;
		bottom: 0;
		--foxColor: #dd4019;
		--foxColorDark: #a72a14;
		left: -470px;
		transform: scale(0.5);
		transform-origin: center left;
		transition: filter 1s ease-in-out;
	}
	@media (prefers-color-scheme: dark) {
		.fox {
			filter: hue-rotate(20deg) saturate(0.4) brightness(0.5);
		}
	}

	.fox * {
		position: absolute;
	}
	.fox:after {
		content: '';
		display: block;
		position: absolute;
		width: 80%;
		height: 20px;
		border-radius: 100%;
		background: rgba(0, 0, 0, 0.2);
		bottom: -10px;
		left: 10%;
		z-index: -1;
	}

	@keyframes log {
		from,
		to {
			transform: rotate(-30deg);
		}
		50% {
			transform: rotate(25deg);
		}
	}
	@keyframes log-inner {
		from,
		to {
			transform: rotate(15deg);
		}
		50% {
			transform: rotate(-15deg);
		}
	}
	@keyframes hind-log {
		from,
		to {
			transform: rotate(-20deg);
		}
		50% {
			transform: rotate(15deg);
		}
	}
	@keyframes hind-log-inner {
		from,
		to {
			transform: rotate(50deg);
		}
		50% {
			transform: rotate(30deg);
		}
	}
	.leg-outer {
		bottom: 0;
		right: 127px;

		animation: leg-outer 0.9s cubic-bezier(0.445, 0, 0.55, 1) both infinite;
		z-index: 1;
	}
	@keyframes leg-outer {
		0%,
		100% {
			transform: translateX(0);
		}
		50% {
			transform: translateX(-76px);
		}
	}
	.hind-leg-outer {
		bottom: 0;
		right: 263px;
		animation: leg-outer 0.9s cubic-bezier(0.445, 0, 0.55, 1) both infinite;
		animation-delay: -0.225s;
		z-index: 1;
	}

	.leg {
		bottom: 0;

		animation: leg 0.9s cubic-bezier(0.445, 0, 0.55, 1) both infinite;
	}
	@keyframes leg {
		75% {
			transform: translateY(-20px);
		}
		from,
		40%,
		to {
			transform: translateY(0);
		}
	}
	.hind-leg-outer2 {
		bottom: 0;
		animation: leg 0.9s cubic-bezier(0.445, 0, 0.55, 1) both infinite;
		animation-delay: -0.225s;
	}

	.paw,
	.hind-paw {
		bottom: 100%;
		width: 10px;
		height: 12px;
		transform-origin: bottom center;
		border-radius: 5px;
		background: #000;
	}
	.paw:after,
	.hind-paw:after {
		animation: paw 0.9s cubic-bezier(0.445, 0, 0.55, 1) both infinite;
		content: '';
		position: absolute;
		display: block;
		left: 0;
		bottom: 0;
		height: 100%;
		width: 200%;
		background: #000;
		border-radius: 100px;
		transform-origin: 5px center;
	}
	@keyframes paw {
		10%,
		to {
			transform: rotate(10deg);
		}
		20%,
		40% {
			transform: rotate(10deg);
		}
		70% {
			transform: rotate(120deg);
		}
	}
	.hind-paw {
		animation-delay: -0.225s;
	}

	.log,
	.hind-log {
		bottom: calc(100% - 5px);
		width: 10px;
		height: 44px;
		transform-origin: bottom center;
		transform: rotate(5deg);
		background: black;
		border-bottom-right-radius: 50% 100%;
		border-top-right-radius: 5px;
		border-top-left-radius: 5px;
	}
	.log:after,
	.hind-log:after {
		content: '';
		display: block;
		position: absolute;
		height: 100%;
		width: 75%;
		right: 0;
		background: black;
		transform-origin: right bottom;
		transform: rotate(9deg);
		border-top-right-radius: 8px;
	}

	.log {
		animation: log 0.9s cubic-bezier(0.445, 0, 0.55, 1) both infinite;
	}

	.hind-log {
		animation: hind-log 0.9s cubic-bezier(0.445, 0, 0.55, 1) both infinite;

		animation-delay: -0.225s;
	}

	.log-inner,
	.hind-log-inner {
		z-index: 1;
	}

	.log-inner {
		bottom: calc(100% - 8px);
		width: 15px;
		height: 65px;
		transform-origin: bottom center;
		animation: log-inner 0.9s cubic-bezier(0.445, 0, 0.55, 1) both infinite;
		background-color: var(--foxColor);
		transition: background-color var(--foxColorTransitionSpeed);
		background-image: linear-gradient(
			to top,
			#000000,
			#000000 10px,
			rgba(0, 0, 0, 0) 11px,
			rgba(0, 0, 0, 0)
		);
		border-bottom-right-radius: 5px;
		border-bottom-left-radius: 5px;
	}
	.leg-outer + .leg-outer .log-inner,
	.leg-outer + .leg-outer .log-inner:after {
		background-color: var(--foxColorDark);
	}
	.leg-outer + .leg-outer .log-inner:before {
		background: radial-gradient(
			farthest-side at bottom left,
			rgba(0, 0, 0, 0),
			rgba(0, 0, 0, 0) 99%,
			var(--foxColorDark)
		);
	}
	.log-inner:before,
	.log-inner:after {
		content: '';
		display: block;
		position: absolute;
	}
	.log-inner:before {
		right: 100%;
		background: radial-gradient(
			farthest-side at bottom left,
			rgba(0, 0, 0, 0),
			rgba(0, 0, 0, 0) 99%,
			var(--foxColor)
		);
		height: 90%;
		bottom: 0;
		width: 240%;
		border-top-left-radius: 100%;
	}
	.log-inner:after {
		right: 0;
		bottom: 0;
		height: 100%;
		width: 100%;
		background-color: var(--foxColor);
		transform-origin: bottom right;
		transform: rotate(15deg);
		border-bottom-left-radius: 50% 100%;
		border-bottom-right-radius: 5px;
		background-image: linear-gradient(
			-15deg,
			#000000,
			#000000 9.5px,
			rgba(0, 0, 0, 0) 9.5px,
			rgba(0, 0, 0, 0)
		);
	}

	.hind-log-inner {
		bottom: calc(100% - 7px);
		width: 15px;
		left: -1px;
		height: 85px;
		transform-origin: bottom center;
		animation: hind-log-inner 0.9s cubic-bezier(0.445, 0, 0.55, 1) both infinite;
		animation-delay: -0.225s;
		background-color: var(--foxColor);
		background-image: linear-gradient(
			to top,
			#000000,
			#000000 12px,
			rgba(0, 0, 0, 0) 12px,
			rgba(0, 0, 0, 0)
		);
	}
	.hind-log-inner:before,
	.hind-log-inner:after {
		content: '';
		display: block;
		position: absolute;
	}
	.hind-log-inner:before {
		right: 100%;
		background: radial-gradient(
			farthest-side at bottom left,
			rgba(0, 0, 0, 0),
			rgba(0, 0, 0, 0) 99%,
			var(--foxColor)
		);
		height: 90%;
		bottom: 0;
		width: 240%;
		border-top-left-radius: 100%;
	}
	.hind-log-inner:after {
		left: calc(100% - 1px);
		height: 100%;
		width: 34px;
		background-color: var(--foxColor);
		border-bottom-right-radius: 100% 100%;
		transform-origin: bottom left;
		transform: skewY(-38deg);
		background-image: linear-gradient(
			38deg,
			#000000,
			#000000 10px,
			rgba(0, 0, 0, 0) 10px,
			rgba(0, 0, 0, 0)
		);
	}
	.hind-leg-outer + .hind-leg-outer .hind-log-inner:before {
		background: radial-gradient(
			farthest-side at bottom left,
			rgba(0, 0, 0, 0),
			rgba(0, 0, 0, 0) 99%,
			var(--foxColorDark)
		);
	}

	.leg-outer + .leg-outer {
		z-index: 0;
	}
	.leg-outer + .leg-outer,
	.leg-outer + .leg-outer *,
	.leg-outer + .leg-outer *:after {
		animation-delay: -0.45s !important;
	}

	.hind-paw:after {
		animation-delay: -0.225s;
	}

	.hind-leg-outer + .hind-leg-outer {
		z-index: 0;
	}
	.hind-leg-outer + .hind-leg-outer,
	.hind-leg-outer + .hind-leg-outer *,
	.hind-leg-outer + .hind-leg-outer *:after {
		animation-delay: -0.675s;
	}
	.hind-leg-outer + .hind-leg-outer .hind-log-inner,
	.hind-leg-outer + .hind-leg-outer .hind-log-inner:after {
		background-color: var(--foxColorDark);
	}

	.body {
		top: 50%;
		left: 170px;
		height: 90px;
		width: 173px;
		background: var(--foxColor);
		border-top-left-radius: 25% 50%;
		border-bottom-left-radius: 25% 50%;
		border-bottom-right-radius: 25% 50%;
		transform-origin: right center;
		animation: body 0.9s cubic-bezier(0.445, 0, 0.55, 1) both infinite;
		animation-duration: 0.45s;
		animation-delay: -0.225s;
	}
	@keyframes body {
		from,
		to {
			transform: rotate(-1deg);
		}
		50% {
			transform: rotate(1deg);
		}
	}
	.body:before {
		content: '';
		display: block;
		position: absolute;
		height: 50px;
		width: 50px;
		background: var(--foxColor);
		z-index: 1;
		border-top-right-radius: 22px;
		right: 3px;
		top: -3px;
		transform: rotate(15deg);
	}

	.head {
		position: absolute;
		height: 87px;
		width: 112px;
		bottom: 89%;
		left: 65%;
		border-top-left-radius: 40% 60%;
		border-top-right-radius: 60% 20%;
		background: var(--foxColor);
		animation: head 0.9s cubic-bezier(0.445, 0, 0.55, 1) both infinite;
		animation-duration: 0.45s;
		animation-delay: -0.225s;
		transform-origin: center bottom;
		z-index: 0;
		transform-style: preserve-3d;
	}
	@keyframes head {
		from,
		to {
			transform: rotate(0);
		}
		50% {
			transform: translateY(2px) rotate(-2deg);
		}
	}
	.head:before,
	.head:after {
		content: '';
		display: block;
		position: absolute;
	}
	.head:before {
		width: 15px;
		height: 15px;
		right: 100%;
		bottom: 9px;
		background-image: radial-gradient(
			ellipse farthest-side at top left,
			rgba(0, 0, 0, 0),
			rgba(0, 0, 0, 0) 99%,
			var(--foxColor) 99.1%
		);
	}
	.head:after {
		width: 50px;
		height: 40px;
		top: calc(100% - 1px);
		left: 50%;
		background-image: radial-gradient(
			ellipse farthest-side at bottom right,
			rgba(0, 0, 0, 0),
			rgba(0, 0, 0, 0) 99%,
			#f0e9ec 99.1%
		);
	}

	.face {
		width: 84px;
		height: 72px;
		bottom: 0;
		right: 4px;
		background: #f0e9ec;
		border-top-left-radius: 60% 100%;
		border-top-right-radius: 40% 10%;
		z-index: 2;
		animation: face 0.9s cubic-bezier(0.445, 0, 0.55, 1) both infinite;
	}
	@keyframes face {
		from,
		to {
			transform: translateX(0);
		}
		50% {
			transform: translateX(-2px);
		}
	}
	.face:before,
	.face:after {
		content: '';
		display: block;
		position: absolute;
		background: #000;
		animation: eye 0.9s cubic-bezier(0.445, 0, 0.55, 1) both infinite;
	}
	@keyframes eye {
		from,
		to {
			transform: rotate(-4deg);
		}
		50% {
			transform: translateX(-2px) rotate(-4deg);
		}
	}
	.face:before {
		height: 4px;
		width: 25px;
		border-radius: 2px;
		top: 10px;
		right: 8px;
	}
	.face:after {
		height: 10px;
		width: 10px;
		border-radius: 50%;
		top: 23px;
		right: 15px;
	}

	.snout {
		width: 36px;
		height: 24px;
		left: calc(100% - 5px);
		bottom: 0;
		border-bottom-right-radius: 100%;
		background-color: #f0e9ec;
		background-image: radial-gradient(
			circle 6px at top right,
			#000000,
			#000000 99%,
			rgba(0, 0, 0, 0) 99%
		);
		transform-origin: right;
		animation: snout 0.9s cubic-bezier(0.445, 0, 0.55, 1) both infinite;
	}
	@keyframes snout {
		from,
		to {
			transform: scale(1);
		}
		50% {
			transform: scaleX(1.05);
		}
	}
	.snout:before,
	.snout:after {
		content: '';
		display: block;
		position: absolute;
		bottom: 100%;
		left: 5px;
		width: calc(100% - 5px);
		height: 40px;
		background-image: radial-gradient(
			ellipse farthest-side at top right,
			rgba(0, 0, 0, 0),
			rgba(0, 0, 0, 0) 99%,
			var(--foxColor) 99.1%
		);
	}
	.snout:before {
		animation: snout-outer 0.9s cubic-bezier(0.445, 0, 0.55, 1) both infinite;
	}
	@keyframes snout-outer {
		50% {
			transform: scaleX(0.93);
		}
	}
	.snout:after {
		background-image: radial-gradient(
			ellipse farthest-side at top right,
			rgba(0, 0, 0, 0),
			rgba(0, 0, 0, 0) 99%,
			#f0e9ec 99.1%
		);
		left: 0;
	}

	.tail {
		border-radius: 50%;
		background: var(--foxColor);
		width: 1rem;
		height: 1rem;
	}

	.body > .tail {
		height: 20;
		width: 20;
		left: 10px;
		transform: rotate(58deg);
	}
	.body > .tail .tail {
		right: 100%;
		border-color: green;
		z-index: -1;
	}
	.body > .tail > .tail {
		height: 36px;
		width: 36px;
		top: -8px;
		animation: tail-2 0.9s cubic-bezier(0.445, 0, 0.55, 1) both infinite;
		animation-delay: -0.225s;
		transform-origin: calc(100% + 10px);
		transform: rotate(0);
	}
	@keyframes tail-2 {
		from,
		to {
			transform: rotate(0);
		}
		50% {
			transform: rotate(0);
		}
	}
	.body > .tail > .tail:before,
	.body > .tail > .tail:after {
		content: '';
		left: 50%;
		position: absolute;
		height: 50%;
		width: 26.83282px;
		display: block;
		background: var(--foxColor);
	}
	.body > .tail > .tail:after {
		bottom: 0;
		transform-origin: top left;
		transform: rotate(-16.60156deg);
	}
	.body > .tail > .tail:before {
		transform-origin: bottom left;
		transform: rotate(16.60156deg);
	}
	.body > .tail > .tail2 > .tail {
		height: 70px;
		width: 70px;
		top: -25px;

		animation: tail2-2 0.9s cubic-bezier(0.445, 0, 0.55, 1) both infinite;

		animation-delay: -0.225s;
		transform-origin: calc(100% + 10px);
		transform: rotate(-30deg);
	}
	@keyframes tail2-2 {
		from,
		to {
			transform: rotate(-33deg);
		}
		50% {
			transform: rotate(-30deg);
		}
	}
	.body > .tail > .tail2 > .tail:before,
	.body > .tail > .tail2 > .tail:after {
		content: '';
		left: 50%;
		position: absolute;
		height: 50%;
		width: 37.41657px;
		display: block;
		background: var(--foxColor);
	}
	.body > .tail > .tail2 > .tail:after {
		bottom: 0;
		transform-origin: top left;
		transform: rotate(-33.74902deg);
	}
	.body > .tail > .tail2 > .tail:before {
		transform-origin: bottom left;
		transform: rotate(33.74902deg);
	}
	.body > .tail > .tail > .tail {
		height: 93px;
		width: 93px;
		top: -28.5px;
		animation: tail-3 0.9s cubic-bezier(0.445, 0, 0.55, 1) both infinite;
		animation-delay: -0.225s;
		transform-origin: calc(100% + 18px);
		transform: rotate(-24deg);
	}
	@keyframes tail-3 {
		from,
		to {
			transform: rotate(-30deg);
		}
		50% {
			transform: rotate(-24deg);
		}
	}
	.body > .tail > .tail > .tail:before,
	.body > .tail > .tail > .tail:after {
		content: '';
		left: 50%;
		position: absolute;
		height: 50%;
		width: 57.8619px;
		display: block;
		background: var(--foxColor);
	}
	.body > .tail > .tail > .tail:after {
		bottom: 0;
		transform-origin: top left;
		transform: rotate(-26.22267deg);
	}
	.body > .tail > .tail > .tail:before {
		transform-origin: bottom left;
		transform: rotate(26.22267deg);
	}
	.body > .tail > .tail2 > .tail > .tail {
		height: 120px;
		width: 120px;
		top: -25px;
		animation: tail2-3 0.9s cubic-bezier(0.445, 0, 0.55, 1) both infinite;
		animation-delay: -425ms;
		background: linear-gradient(45deg, #f0e9ec 25%, rgba(0, 0, 0, 0) 25%),
			linear-gradient(135deg, #f0e9ec 25%, rgba(0, 0, 0, 0) 25%) 0 0,
			linear-gradient(to left, rgba(0, 0, 0, 0), rgba(0, 0, 0, 0) 66%, #f0e9ec 66%);
		background-size: 40px 40px, 40px 40px, 100%;
		background-position: center;
		background-color: var(--foxColor);
		background-repeat: repeat-y;
		transform-origin: calc(100% + 35px);
		transform: rotate(-30deg);
	}
	@keyframes tail2-3 {
		from,
		to {
			transform: rotate(-35deg);
		}
		50% {
			transform: rotate(-30deg);
		}
	}
	.body > .tail > .tail2 > .tail > .tail:before,
	.body > .tail > .tail2 > .tail > .tail:after {
		content: '';
		left: 50%;
		position: absolute;
		height: 50%;
		width: 91.65151px;
		display: block;
		background: var(--foxColor);
	}
	.body > .tail > .tail2 > .tail > .tail:after {
		bottom: 0;
		transform-origin: top left;
		transform: rotate(-15.25754deg);
	}
	.body > .tail > .tail2 > .tail > .tail:before {
		transform-origin: bottom left;
		transform: rotate(15.25754deg);
	}
	.body > .tail > .tail > .tail > .tail {
		height: 98px;
		width: 98px;
		top: -2.5px;
		animation: tail-4 0.9s cubic-bezier(0.445, 0, 0.55, 1) both infinite;
		animation-delay: -425ms;
		background: #f0e9ec !important;
		transform-origin: calc(100% + 46.5px);
		transform: rotate(-17deg);
	}
	.body > .tail > .tail > .tail > .tail:before {
		background: linear-gradient(to right, #f0e9ec, var(--foxColor)) !important;
	}
	@keyframes tail-4 {
		from,
		to {
			transform: rotate(-24deg);
		}
		50% {
			transform: rotate(-17deg);
		}
	}
	.body > .tail > .tail > .tail > .tail:before,
	.body > .tail > .tail > .tail > .tail:after {
		content: '';
		left: 50%;
		position: absolute;
		height: 50%;
		width: 95.46727px;
		display: block;
		background: var(--foxColor);
	}
	.body > .tail > .tail > .tail > .tail:after {
		bottom: 0;
		transform-origin: top left;
		transform: rotate(-1.50006deg);
	}
	.body > .tail > .tail > .tail > .tail:before {
		transform-origin: bottom left;
		transform: rotate(1.50006deg);
	}
	.body > .tail > .tail2 > .tail > .tail > .tail {
		height: 50px;
		width: 50px;
		top: 35px;
		animation: tail2-4 0.9s cubic-bezier(0.445, 0, 0.55, 1) both infinite;
		animation-delay: -0.225s;
		transform-origin: calc(100% + 60px);
		transform: rotate(27deg);
	}
	.body > .tail > .tail2 > .tail > .tail > .tail:before {
		display: none !important;
	}
	.body > .tail > .tail2 > .tail > .tail > .tail,
	.body > .tail > .tail2 > .tail > .tail > .tail:before,
	.body > .tail > .tail2 > .tail > .tail > .tail:after {
		background: #f0e9ec !important;
	}
	@keyframes tail2-4 {
		from,
		to {
			transform: rotate(5deg);
		}
		50% {
			transform: rotate(27deg);
		}
	}
	.body > .tail > .tail2 > .tail > .tail > .tail:before,
	.body > .tail > .tail2 > .tail > .tail > .tail:after {
		content: '';
		left: 50%;
		position: absolute;
		height: 50%;
		width: 77.45967px;
		display: block;
		background: var(--foxColor);
	}
	.body > .tail > .tail2 > .tail > .tail > .tail:after {
		bottom: 0;
		transform-origin: top left;
		transform: rotate(24.31576deg);
	}
	.body > .tail > .tail2 > .tail > .tail > .tail:before {
		transform-origin: bottom left;
		transform: rotate(-24.31576deg);
	}
	.body > .tail > .tail > .tail > .tail > .tail {
		height: 36px;
		width: 36px;
		top: 31px;
		animation: tail-5 0.9s cubic-bezier(0.445, 0, 0.55, 1) both infinite;
		animation-delay: -0.225s;
		transform-origin: calc(100% + 49px);
		transform: rotate(41deg);
	}
	.body > .tail > .tail > .tail > .tail > .tail,
	.body > .tail > .tail > .tail > .tail > .tail:before,
	.body > .tail > .tail > .tail > .tail > .tail:after {
		background: #f0e9ec !important;
	}
	@keyframes tail-5 {
		from,
		to {
			transform: rotate(10deg);
		}
		50% {
			transform: rotate(41deg);
		}
	}
	.body > .tail > .tail > .tail > .tail > .tail:before,
	.body > .tail > .tail > .tail > .tail > .tail:after {
		content: '';
		left: 50%;
		position: absolute;
		height: 50%;
		width: 59.39697px;
		display: block;
		background: var(--foxColor);
	}
	.body > .tail > .tail > .tail > .tail > .tail:after {
		bottom: 0;
		transform-origin: top left;
		transform: rotate(27.56063deg);
	}
	.body > .tail > .tail > .tail > .tail > .tail:before {
		transform-origin: bottom left;
		transform: rotate(-27.56063deg);
	}
	.body > .tail > .tail2 > .tail > .tail > .tail > .tail {
		height: 20px;
		width: 20px;
		top: 15px;
		animation: tail2-5 0.9s cubic-bezier(0.445, 0, 0.55, 1) both infinite;
		animation-delay: -0.225s;
		transform-origin: calc(100% + 25px);
		transform: rotate(23deg);
	}
	.body > .tail > .tail2 > .tail > .tail > .tail > .tail,
	.body > .tail > .tail2 > .tail > .tail > .tail > .tail:before,
	.body > .tail > .tail2 > .tail > .tail > .tail > .tail:after {
		background: #f0e9ec !important;
	}
	@keyframes tail2-5 {
		from,
		to {
			transform: rotate(0deg);
		}
		50% {
			transform: rotate(23deg);
		}
	}
	.body > .tail > .tail2 > .tail > .tail > .tail > .tail:before,
	.body > .tail > .tail2 > .tail > .tail > .tail > .tail:after {
		content: '';
		left: 50%;
		position: absolute;
		height: 50%;
		width: 31.62278px;
		display: block;
		background: var(--foxColor);
	}
	.body > .tail > .tail2 > .tail > .tail > .tail > .tail:after {
		bottom: 0;
		transform-origin: top left;
		transform: rotate(25.37695deg);
	}
	.body > .tail > .tail2 > .tail > .tail > .tail > .tail:before {
		transform-origin: bottom left;
		transform: rotate(-25.37695deg);
	}
	.body > .tail > .tail > .tail > .tail > .tail > .tail {
		height: 15px;
		width: 15px;
		top: 10.5px;
		animation: tail-6 0.9s cubic-bezier(0.445, 0, 0.55, 1) both infinite;
		animation-delay: -0.225s;
		transform-origin: calc(100% + 18px);
		transform: rotate(10deg);
	}
	.body > .tail > .tail > .tail > .tail > .tail > .tail,
	.body > .tail > .tail > .tail > .tail > .tail > .tail:before,
	.body > .tail > .tail > .tail > .tail > .tail > .tail:after {
		background: #f0e9ec !important;
	}
	@keyframes tail-6 {
		from,
		to {
			transform: rotate(0deg);
		}
		50% {
			transform: rotate(10deg);
		}
	}
	.body > .tail > .tail > .tail > .tail > .tail > .tail:before,
	.body > .tail > .tail > .tail > .tail > .tail > .tail:after {
		content: '';
		left: 50%;
		position: absolute;
		height: 50%;
		width: 23.2379px;
		display: block;
		background: var(--foxColor);
	}
	.body > .tail > .tail > .tail > .tail > .tail > .tail:after {
		bottom: 0;
		transform-origin: top left;
		transform: rotate(24.31576deg);
	}
	.body > .tail > .tail > .tail > .tail > .tail > .tail:before {
		transform-origin: bottom left;
		transform: rotate(-24.31576deg);
	}
	.body > .tail > .tail2 > .tail > .tail > .tail > .tail > .tail {
		height: 10px;
		width: 10px;
		top: 5px;
		animation: tail2-6 0.9s cubic-bezier(0.445, 0, 0.55, 1) both infinite;
		animation-delay: -0.225s;
		transform-origin: calc(100% + 10px);
		transform: rotate(10deg);
	}
	.body > .tail > .tail2 > .tail > .tail > .tail > .tail > .tail,
	.body > .tail > .tail2 > .tail > .tail > .tail > .tail > .tail:before,
	.body > .tail > .tail2 > .tail > .tail > .tail > .tail > .tail:after {
		background: #f0e9ec !important;
	}
	@keyframes tail2-6 {
		from,
		to {
			transform: rotate(3deg);
		}
		50% {
			transform: rotate(10deg);
		}
	}
	.body > .tail > .tail2 > .tail > .tail > .tail > .tail > .tail:before,
	.body > .tail > .tail2 > .tail > .tail > .tail > .tail > .tail:after {
		content: '';
		left: 50%;
		position: absolute;
		height: 50%;
		width: 14.14214px;
		display: block;
		background: var(--foxColor);
	}
	.body > .tail > .tail2 > .tail > .tail > .tail > .tail > .tail:after {
		bottom: 0;
		transform-origin: top left;
		transform: rotate(19.47124deg);
	}
	.body > .tail > .tail2 > .tail > .tail > .tail > .tail > .tail:before {
		transform-origin: bottom left;
		transform: rotate(-19.47124deg);
	}
	.body > .tail > .tail2 {
		height: 20px;
		width: 20px;
		border-radius: 50%;
		position: absolute;
		right: 50%;
		transform-origin: right center;
		transform: rotate(18deg);
	}
	.body > .tail > .tail2 .tail {
		border-color: blue;
		z-index: -1;
	}

	.ears {
		top: 10px;
		left: 35px;
		width: 60px;
		transform: rotateY(20deg);
		transform-origin: bottom left;
	}
	.ears:before {
		content: '';
		display: block;
		position: absolute;
		width: 60px;
		height: 40px;
		background: var(--foxColor);
		border-top-left-radius: 100%;
		left: -36px;
		top: -16px;
		transform: rotate(-18deg) skewX(-20deg);
	}

	.ear {
		width: 40px;
		height: 46px;
		bottom: 0;
		transform: skewY(-10deg);
		transform-origin: right bottom;
	}
	.ear:before,
	.ear:after {
		content: '';
		display: block;
		position: absolute;
		height: 100%;
		width: 100%;
		border-top-left-radius: 100%;
	}
	.ear:before {
		background: var(--foxColor);
	}
	.ear:after {
		background: #d5d1dc;
		width: 60%;
		right: 0;

		animation: ear-inner 0.9s cubic-bezier(0.445, 0, 0.55, 1) both infinite;

		animation-delay: -0.45s;
		transform-origin: right;
	}
	@keyframes ear-inner {
		50% {
			transform: scaleX(0.9);
		}
	}
	.ear:nth-child(1) {
		right: 0;
		animation: ear-back 0.9s cubic-bezier(0.445, 0, 0.55, 1) both infinite;
	}
	@keyframes ear-back {
		from,
		to {
			transform: rotate(5deg) skewY(-10deg);
		}
		50% {
			transform: translateX(-1px) rotate(5deg) skewY(-10deg);
		}
	}
	.ear:nth-child(2) {
		left: 0;
		transform: skewY(-10deg);
	}
</style>
