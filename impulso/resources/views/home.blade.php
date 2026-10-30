@extends('layouts.app')
@section('body_class', 'home-page-fixed')
@section('content')
<div class="home-page">
	<section class="home-hero wrap">
		<div class="home-hero-copy">
			<p class="eyebrow">Herramientas para emprender</p>
			<h1>impulso<span class="home-hero-period">.</span></h1>
			<p class="home-hero-lead">Organiza tu negocio, comparte lo que haces y conecta con más personas.</p>
			<div class="actions">
				<a class="button" href="{{ route('register') }}">Crear mi espacio <span>→</span></a>
				<a class="text-link" href="{{ route('discover') }}">Explorar emprendimientos <span>↗</span></a>
			</div>
		</div>
		<div class="home-hero-board" aria-label="Resumen de actividad de un emprendimiento">
			<div class="board-top"><span>Panel de actividad</span><i></i><b>en vivo</b></div>
			<div class="board-number"><small>Movimiento del mes</small><strong>$ 184.200</strong><span>+ 12,8% <em>vs. mes anterior</em></span></div>
			<div class="board-chart" aria-hidden="true"><i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i></div>
			<div class="board-foot"><span><b class="board-dot board-dot-lime"></b>Ingresos</span><span><b class="board-dot board-dot-teal"></b>Pedidos activos</span><strong>03</strong></div>
		</div>
	</section>

	<section class="home-marquee" aria-label="Lo que puedes hacer con Impulso">
		<div class="home-marquee-track">
			<div class="home-marquee-group"><span>organiza</span><b>+</b><span>comparte</span><b>+</b><span>vende</span><b>+</b><span>conecta</span><b>+</b><span>crece</span><b>+</b></div>
			<div class="home-marquee-group" aria-hidden="true"><span>organiza</span><b>+</b><span>comparte</span><b>+</b><span>vende</span><b>+</b><span>conecta</span><b>+</b><span>crece</span><b>+</b></div>
		</div>
	</section>

	<section class="home-intro wrap">
		<div><p class="eyebrow">Todo en un mismo lugar</p><h2>Más claridad para el día a día de tu negocio.</h2></div>
		<p>Conoce cada herramienta y elige la que necesitas hoy.</p>
	</section>

	<section class="home-tools wrap">
		<details class="home-tool home-tool-wide">
			<summary><span class="tool-index">01 / VISIBILIDAD</span><strong>Muestra lo que haces.</strong><span class="home-tool-toggle">Quiero saber más <span class="tool-arrow" aria-hidden="true">⌄</span></span></summary>
			<div class="home-tool-panel"><div class="home-tool-panel-inner"><p>Crea un perfil para compartir tus productos, servicios y novedades.</p><a class="home-tool-link" href="{{ route('discover') }}">Explorar emprendimientos <span aria-hidden="true">↗</span></a></div></div>
		</details>
		<details class="home-tool home-tool-dark">
			<summary><span class="tool-index">02 / CONTROL</span><strong>Ordena tus números.</strong><span class="home-tool-toggle">Quiero saber más <span class="tool-arrow" aria-hidden="true">⌄</span></span></summary>
			<div class="home-tool-panel"><div class="home-tool-panel-inner"><p>Registra ingresos y gastos para entender mejor cómo avanza tu negocio.</p><a class="home-tool-link" href="{{ route('register') }}">Crear mi espacio <span aria-hidden="true">→</span></a></div></div>
		</details>
		<details class="home-tool home-tool-soft">
			<summary><span class="tool-index">03 / OPERACIÓN</span><strong>Gestiona tu negocio.</strong><span class="home-tool-toggle">Quiero saber más <span class="tool-arrow" aria-hidden="true">⌄</span></span></summary>
			<div class="home-tool-panel"><div class="home-tool-panel-inner"><p>Organiza productos, stock y pedidos desde un solo lugar.</p><a class="home-tool-link" href="{{ route('register') }}">Crear mi espacio <span aria-hidden="true">→</span></a></div></div>
		</details>
		<details class="home-tool home-tool-lime">
			<summary><span class="tool-index">04 / AGENDA</span><strong>Administra tus turnos.</strong><span class="home-tool-toggle">Quiero saber más <span class="tool-arrow" aria-hidden="true">⌄</span></span></summary>
			<div class="home-tool-panel"><div class="home-tool-panel-inner"><p>Define tus horarios y recibe reservas sin perderlas entre mensajes.</p><a class="home-tool-link" href="{{ route('register') }}">Crear mi espacio <span aria-hidden="true">→</span></a></div></div>
		</details>
		<details class="home-tool home-tool-outline">
			<summary><span class="tool-index">05 / CONTENIDO</span><strong>Comparte tus novedades.</strong><span class="home-tool-toggle">Quiero saber más <span class="tool-arrow" aria-hidden="true">⌄</span></span></summary>
			<div class="home-tool-panel"><div class="home-tool-panel-inner"><p>Publica proyectos y ofertas para mantener al día a tu comunidad.</p><a class="home-tool-link" href="{{ route('register') }}">Crear mi espacio <span aria-hidden="true">→</span></a></div></div>
		</details>
		<details class="home-tool home-tool-warm">
			<summary><span class="tool-index">06 / EQUIPO</span><strong>Trabaja en conjunto.</strong><span class="home-tool-toggle">Quiero saber más <span class="tool-arrow" aria-hidden="true">⌄</span></span></summary>
			<div class="home-tool-panel"><div class="home-tool-panel-inner"><p>Invita a tu equipo y mantén las tareas y conversaciones cerca.</p><a class="home-tool-link" href="{{ route('register') }}">Crear mi espacio <span aria-hidden="true">→</span></a></div></div>
		</details>
	</section>

	<section class="home-flow wrap">
		<div class="home-flow-heading"><p class="eyebrow">Para empezar</p><h2>Un paso a la vez.<br><span>A tu manera.</span></h2></div>
		<details class="home-flow-disclosure">
			<summary class="home-flow-toggle"><span>Conocer los primeros pasos</span><span class="tool-arrow" aria-hidden="true">⌄</span></summary>
			<div class="home-flow-panel"><div class="home-flow-panel-inner">
				<div class="home-flow-list"><div><b>01</b><strong>Crea tu espacio</strong><span>Presenta tu emprendimiento con tu identidad.</span></div><div><b>02</b><strong>Elige tus herramientas</strong><span>Activa solo lo que necesitas.</span></div><div><b>03</b><strong>Sigue creciendo</strong><span>Conecta con tu comunidad y avanza.</span></div></div>
			</div></div>
		</details>
	</section>

	<section class="home-cta wrap"><div><p class="eyebrow">Cuando quieras empezar</p><h2>Tu negocio tiene un lugar acá.</h2></div><a class="button" href="{{ route('register') }}">Crear mi espacio <span>→</span></a></section>
</div>
@endsection