@extends('layouts.app')
@section('content')
<section class="auth-page">
	<div class="auth-panel">
		<p class="eyebrow">Tu próximo paso</p>
		<h1>Crea tu cuenta.</h1>
		<p class="muted">Explora la comunidad o comienza tu propio emprendimiento.</p>
		<form method="POST" action="{{ route('register.store') }}" class="form">
			@csrf
			<label>Nombre completo<input type="text" name="name" value="{{ old('name') }}" required autofocus></label>
			@error('name')<small class="error">{{ $message }}</small>@enderror
			<label>Correo electrónico<input type="email" name="email" value="{{ old('email') }}" required></label>
			@error('email')<small class="error">{{ $message }}</small>@enderror
			<label>Contraseña<input type="password" name="password" required></label>
			<label>Repite tu contraseña<input type="password" name="password_confirmation" required></label>
			@error('password')<small class="error">{{ $message }}</small>@enderror
			<details class="consent-document">
				<summary>Términos y condiciones</summary>
				<div class="consent-document-copy">
					<p><strong>Texto de muestra:</strong> al crear una cuenta, te comprometes a brindar información correcta, mantener segura tu contraseña y utilizar Impulso de manera respetuosa y conforme a la ley.</p>
					<p>Tu cuenta es personal. Eres responsable de la actividad realizada desde ella y de mantener actualizados tus datos. No publiques contenido que infrinja derechos de otras personas ni uses la plataforma para actividades engañosas o dañinas.</p>
					<p>Impulso puede actualizar sus funciones y estas condiciones. Para esta demostración, cualquier consulta puede dirigirse al equipo de soporte de Impulso.</p>
				</div>
			</details>
			<details class="consent-document">
				<summary>Términos de privacidad</summary>
				<div class="consent-document-copy">
					<p><strong>Aviso de muestra:</strong> Impulso utiliza los datos que proporcionas, como tu nombre y correo electrónico, para crear y administrar tu cuenta y ofrecerte las funciones de la comunidad.</p>
					<p>No ingreses información sensible en esta demostración. Los datos podrían conservarse mientras la cuenta esté activa y durante el tiempo necesario para atender obligaciones aplicables. Este texto es ilustrativo y no describe necesariamente las prácticas reales del servicio.</p>
					<p>En un servicio real, aquí se indicarían el responsable del tratamiento, los proveedores utilizados, los plazos de conservación y cómo ejercer tus derechos de privacidad, junto con un contacto válido.</p>
				</div>
			</details>
			<label class="consent-check">
				<input type="checkbox" name="terms_accepted" value="1" id="terms-accepted" @checked(old('terms_accepted')) required>
				<span>Acepto los términos y condiciones</span>
			</label>
			@error('terms_accepted')<small class="error">{{ $message }}</small>@enderror
			<label class="consent-check">
				<input type="checkbox" name="privacy_accepted" value="1" id="privacy-accepted" @checked(old('privacy_accepted')) required>
				<span>Acepto los términos de privacidad</span>
			</label>
			@error('privacy_accepted')<small class="error">{{ $message }}</small>@enderror
			<label class="consent-check consent-check-all">
				<input type="checkbox" id="accept-all-conditions" @checked(old('terms_accepted') && old('privacy_accepted'))>
				<span>Acepto todas las condiciones</span>
			</label>
			<button class="button full" id="register-submit" type="submit" disabled>Crear mi cuenta <span>→</span></button>
		</form>
		<p class="form-foot">¿Ya tienes cuenta? <a href="{{ route('login') }}">Ingresar</a></p>
	</div>
	<div class="auth-note warm"><span class="quote">✦</span><p>Una comunidad de personas que hacen que las cosas pasen.</p><small>Encuentra, conecta, crece.</small></div>
</section>
<script>
	const termsAccepted = document.getElementById('terms-accepted');
	const privacyAccepted = document.getElementById('privacy-accepted');
	const acceptAllConditions = document.getElementById('accept-all-conditions');
	const registerSubmit = document.getElementById('register-submit');
	const updateRegisterButton = () => {
		acceptAllConditions.checked = termsAccepted.checked && privacyAccepted.checked;
		registerSubmit.disabled = !(termsAccepted.checked && privacyAccepted.checked);
	};

	termsAccepted.addEventListener('change', updateRegisterButton);
	privacyAccepted.addEventListener('change', updateRegisterButton);
	acceptAllConditions.addEventListener('change', () => {
		termsAccepted.checked = acceptAllConditions.checked;
		privacyAccepted.checked = acceptAllConditions.checked;
		updateRegisterButton();
	});
	updateRegisterButton();
</script>
@endsection