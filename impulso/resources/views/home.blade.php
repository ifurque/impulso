@extends('layouts.app')
@section('body_class', 'home-page-fixed')
@section('content')
<div class="home-page">
	<section class="home-hero wrap">
		<div class="home-hero-copy">
			<p class="eyebrow">La plataforma para hacer que pase</p>
			<h1>impulso<span class="home-hero-period">.</span></h1>
			<p class="home-hero-lead">Todo lo que tu emprendimiento necesita para verse, organizarse y crecer en un mismo lugar.</p>
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

	<section class="home-marquee" aria-label="Funcionalidades de Impulso">
		<div class="home-marquee-track"><span>ordenar</span><b>+</b><span>mostrar</span><b>+</b><span>vender</span><b>+</b><span>conectar</span><b>+</b><span>crecer</span><b>+</b><span>ordenar</span></div>
	</section>

	<section class="home-intro wrap">
		<div><p class="eyebrow">Un solo lugar, muchas posibilidades</p><h2>Tu negocio tiene varias caras. Impulso las pone a trabajar juntas.</h2></div>
		<p>Desde la primera publicación hasta el próximo pedido, cada herramienta está pensada para quitarte ruido y devolverte tiempo para lo importante.</p>
	</section>

	<section class="home-tools wrap">
		<a class="home-tool home-tool-wide" href="{{ route('discover') }}"><span class="tool-index">01 / VISIBILIDAD</span><strong>Haz que te encuentren.</strong><p>Un perfil público para mostrar lo que haces, tus productos, servicios, reseñas y novedades.</p><span class="tool-arrow">↗</span></a>
		<a class="home-tool home-tool-dark" href="{{ route('register') }}"><span class="tool-index">02 / CONTROL</span><strong>Entiende tus números.</strong><p>Ingresos, gastos, movimientos y métricas claras para decidir con más confianza.</p><span class="tool-arrow">↗</span></a>
		<a class="home-tool home-tool-soft" href="{{ route('register') }}"><span class="tool-index">03 / OPERACIÓN</span><strong>Ordena el día a día.</strong><p>Productos, stock, pedidos y una base de datos que acompaña tu forma de trabajar.</p><span class="tool-arrow">↗</span></a>
		<a class="home-tool home-tool-lime" href="{{ route('register') }}"><span class="tool-index">04 / AGENDA</span><strong>Llena tu agenda.</strong><p>Configura horarios, recibe turnos y gestiona cada reserva sin mensajes perdidos.</p><span class="tool-arrow">↗</span></a>
		<a class="home-tool home-tool-outline" href="{{ route('register') }}"><span class="tool-index">05 / CONTENIDO</span><strong>Cuenta lo que haces.</strong><p>Publica novedades, ofertas y proyectos para mantener activa tu comunidad.</p><span class="tool-arrow">↗</span></a>
		<a class="home-tool home-tool-warm" href="{{ route('register') }}"><span class="tool-index">06 / EQUIPO</span><strong>Crezcan en conjunto.</strong><p>Invita miembros, reparte responsabilidades y mantén las conversaciones cerca.</p><span class="tool-arrow">↗</span></a>
	</section>

	<section class="home-flow wrap">
		<div class="home-flow-heading"><p class="eyebrow">De la idea al movimiento</p><h2>Menos pestañas abiertas.<br><span>Más cosas pasando.</span></h2></div>
		<div class="home-flow-list"><div><b>01</b><strong>Presenta tu emprendimiento</strong><span>Un espacio propio, con tu identidad y tus reglas.</span></div><div><b>02</b><strong>Activa tus herramientas</strong><span>Elige lo que necesitas hoy y suma más cuando lo necesites.</span></div><div><b>03</b><strong>Hazlo crecer</strong><span>Lee lo que pasa, conversa con tu comunidad y toma impulso.</span></div></div>
	</section>

	<section class="home-cta wrap"><div><p class="eyebrow">Tu próximo paso empieza acá</p><h2>Lo que haces merece<br>un lugar propio.</h2></div><a class="button" href="{{ route('register') }}">Empezar gratis <span>→</span></a></section>
</div>
@endsection