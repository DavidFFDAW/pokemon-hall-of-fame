<?php
include dirname(__FILE__) . '/classes/PokeApi.php';
$pokemons = PokeApi::getPokemons();
?>
<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title>Slider nativo con Scroll Snap</title>

	<style>
		* {
			box-sizing: border-box;
		}

		body {
			margin: 0;
			font-family: system-ui, sans-serif;
			background: #f4f4f5;
			padding: 40px 20px;
		}

		.slider-wrapper {
			max-width: 900px;
			margin: 0 auto;
		}

		.slider {
			display: flex;
			gap: 16px;
			overflow-x: auto;
			scroll-snap-type: x mandatory;
			scroll-behavior: smooth;
			padding: 10px 0 20px;

			cursor: grab;
			user-select: none;
			-webkit-overflow-scrolling: touch;

			scrollbar-width: none;
		}

		.slider::-webkit-scrollbar {
			display: none;
		}

		.slider.dragging {
			cursor: grabbing;
			scroll-snap-type: none;
			scroll-behavior: auto;
		}

		.slide {
			flex: 0 0 80%;
			max-width: 320px;
			min-height: 220px;
			scroll-snap-align: start;
			scroll-snap-stop: always;

			background: white;
			border-radius: 20px;
			padding: 24px;
			box-shadow: 0 10px 30px rgba(0, 0, 0, .08);

			display: flex;
			flex-direction: column;
			justify-content: space-between;
		}

		.slide h3 {
			margin: 0 0 12px;
			font-size: 1.4rem;
		}

		.slide p {
			margin: 0;
			color: #555;
			line-height: 1.5;
		}

		.controls {
			display: flex;
			justify-content: center;
			gap: 12px;
			margin-top: 20px;
		}

		.controls button {
			border: 0;
			background: #111827;
			color: white;
			padding: 10px 16px;
			border-radius: 999px;
			cursor: pointer;
			font-size: 1rem;
		}

		.controls button:hover {
			background: #374151;
		}

		@media (min-width: 768px) {
			.slide {
				flex-basis: calc((100% - 32px) / 3);
			}
		}
	</style>
</head>

<body>
	<section class="slider-wrapper">
		<div class="slider" id="slider">
			<?php foreach (array_slice($pokemons, 0, 151) as $pokemon) : ?>
				<article class="slide">
					<button type="button" class="close">
						<img width="40" height="40" src="<?= $pokemon['image'] ?>" alt="<?= $pokemon['name'] ?>">
						<h3><?= $pokemon['name'] ?></h3>
					</button>
				</article>
			<?php endforeach; ?>

			<!-- <article class="slide">
				<div>
					<h3>Elemento 1</h3>
					<p>Este es un item dentro del scroll horizontal usando CSS Scroll Snap.</p>
				</div>
			</article>

			<article class="slide">
				<div>
					<h3>Elemento 2</h3>
					<p>Al soltar el scroll, el navegador ajusta automáticamente la posición.</p>
				</div>
			</article>

			<article class="slide">
				<div>
					<h3>Elemento 3</h3>
					<p>No necesitamos Swiper, Slick ni librerías externas para este efecto.</p>
				</div>
			</article>

			<article class="slide">
				<div>
					<h3>Elemento 4</h3>
					<p>Funciona especialmente bien en móvil con scroll táctil nativo.</p>
				</div>
			</article>

			<article class="slide">
				<div>
					<h3>Elemento 5</h3>
					<p>También podemos permitir arrastrar con el ratón usando un JavaScript pequeño.</p>
				</div>
			</article>
		</div> -->

		<div class="controls">
			<button type="button" id="prev">Anterior</button>
			<button type="button" id="next">Siguiente</button>
		</div>
	</section>

	<script>
		const slider = document.querySelector('#slider');
		const prev = document.querySelector('#prev');
		const next = document.querySelector('#next');

		let isDragging = false;
		let startX = 0;
		let startScrollLeft = 0;

		function getScrollAmount() {
			const slide = slider.querySelector('.slide');
			const gap = parseInt(getComputedStyle(slider).gap) || 0;

			return slide.offsetWidth + gap;
		}

		prev.addEventListener('click', () => {
			slider.scrollBy({
				left: -getScrollAmount(),
				behavior: 'smooth'
			});
		});

		next.addEventListener('click', () => {
			slider.scrollBy({
				left: getScrollAmount(),
				behavior: 'smooth'
			});
		});

		slider.addEventListener('pointerdown', (event) => {
			isDragging = true;
			startX = event.pageX;
			startScrollLeft = slider.scrollLeft;

			slider.classList.add('dragging');
			slider.setPointerCapture(event.pointerId);
		});

		slider.addEventListener('pointermove', (event) => {
			if (!isDragging) return;

			event.preventDefault();

			const distance = event.pageX - startX;
			slider.scrollLeft = startScrollLeft - distance;
		});

		slider.addEventListener('pointerup', (event) => {
			isDragging = false;
			slider.classList.remove('dragging');
			slider.releasePointerCapture(event.pointerId);
		});

		slider.addEventListener('pointercancel', () => {
			isDragging = false;
			slider.classList.remove('dragging');
		});
	</script>

</body>

</html>