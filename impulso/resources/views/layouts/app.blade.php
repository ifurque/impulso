<!doctype html>
<html lang="es">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>{{ $title ?? 'Impulso' }} | Impulso</title>
	<link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
	@vite(['resources/css/app.css', 'resources/js/app.js'])
	@stack('head')
</head>
@php
	$ui = auth()->user()?->ui_preferences ?? [];
	$uiFont = $ui['font_family'] ?? 'dm';
	$uiMode = $ui['theme_mode'] ?? 'day';
	$uiBackground = $ui['screen_background'] ?? 'paper';
	$uiPrimary = $ui['primary_color'] ?? ($ui['navbar_color'] ?? '#f7f8f3');
	$uiSecondary = $ui['secondary_color'] ?? '#dfe5dc';
	$uiNavbar = $ui['navbar_color'] ?? '#eef1ea';
	$uiText = $ui['text_color'] ?? '#182522';
@endphp
<body
	class="user-font-{{ $uiFont }} user-mode-{{ $uiMode }} user-bg-{{ $uiBackground }} @yield('body_class')"
	style="--user-primary: {{ $uiPrimary }}; --user-secondary: {{ $uiSecondary }}; --user-navbar-bg: {{ $uiNavbar }}; --user-text-color: {{ $uiText }};"
>
	<nav class="nav">
		<a class="brand" href="{{ route('home') }}"><span class="brand-mark">+</span> impulso</a>
		<div class="nav-links">
			<a href="{{ route('discover') }}">Explorar</a>
			@auth
				<a href="{{ route('user.customization') }}">Personalización</a>
				@php($hasBusinessAccess = auth()->user()->ownedBusinesses()->exists() || auth()->user()->businesses()->exists())
				@if($hasBusinessAccess)
					<a href="{{ route('business.panels') }}">Mis emprendimientos</a>
				@else
					<a href="{{ route('business.create') }}">Crear emprendimiento</a>
				@endif
				<form method="POST" action="{{ route('logout') }}">
					@csrf
					<button class="link-button">Salir</button>
				</form>
			@else
				<a href="{{ route('login') }}">Ingresar</a>
				<a class="button button-small" href="{{ route('register') }}">Crear cuenta</a>
			@endauth
		</div>
	</nav>

	<main>
		@if(session('success'))
			<div class="notice success">{{ session('success') }}</div>
		@endif
		@yield('content')
	</main>

	@auth
		@include('partials.inbox-bubble')
	@endauth

	<footer>
		<span class="brand"><span class="brand-mark">+</span> impulso</span>
		<span>Herramientas simples para ideas que crecen.</span>
	</footer>
</body>
</html>