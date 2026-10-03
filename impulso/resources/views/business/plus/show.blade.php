@extends('layouts.app')

@section('content')
<section class="plus-page wrap">
  <a class="back" href="{{ route('dashboard', $business) }}">← Volver al panel de {{ $business->name }}</a>

  <header class="plus-hero">
    <div class="plus-hero-copy">
      <p class="plus-kicker">Una membresía mensual para tu negocio</p>
      <h1>Tu marca, con más espacio para crecer.</h1>
      <p>Impulso + reúne herramientas para llevar la presencia de {{ $business->name }} un paso más allá.</p>
      <span class="plus-coming-soon">Vista previa · Activación próximamente</span>
    </div>
    <div class="plus-orbit" aria-hidden="true">
      <span>+</span>
      <i></i>
      <b></b>
    </div>
  </header>

  <div class="plus-benefits-heading">
    <div>
      <p class="eyebrow">Beneficios de la membresía</p>
      <h2>Una presencia más tuya.</h2>
    </div>
    <p class="muted">Pensado para que cada punto de contacto hable de tu emprendimiento.</p>
  </div>

  <div class="plus-benefit-grid">
    <article class="plus-benefit-card plus-benefit-featured">
      <span class="plus-benefit-icon" aria-hidden="true">↗</span>
      <p class="eyebrow">01 · Tu dirección</p>
      <h3>Dominio propio</h3>
      <p>Conectá un dominio con el nombre de tu marca y compartí tu página con una dirección propia, fuera del dominio de Impulso.</p>
      <span class="plus-benefit-tag">Tu marca al frente</span>
    </article>
    <article class="plus-benefit-card">
      <span class="plus-benefit-icon" aria-hidden="true">✦</span>
      <p class="eyebrow">02 · Una experiencia limpia</p>
      <h3>Sin anuncios de terceros</h3>
      <p>Una experiencia para que quienes visitan tu página puedan enfocarse en tu negocio, sin anuncios de otras páginas.</p>
      <span class="plus-benefit-tag">Más foco en tu negocio</span>
    </article>
    <article class="plus-benefit-card">
      <span class="plus-benefit-icon" aria-hidden="true">◎</span>
      <p class="eyebrow">03 · Tus redes, en sintonía</p>
      <h3>Sincronización de publicaciones</h3>
      <p>Conectá Instagram y Facebook para reunir fotos y videos de tus publicaciones en un solo lugar.</p>
      <span class="plus-benefit-tag">Instagram · Facebook</span>
    </article>
    <article class="plus-benefit-card">
      <span class="plus-benefit-icon" aria-hidden="true">〰</span>
      <p class="eyebrow">04 · A tu manera</p>
      <h3>Animaciones personalizadas</h3>
      <p>Dale movimiento a tu página con animaciones elegidas para acompañar la personalidad de tu marca.</p>
      <span class="plus-benefit-tag">Un estilo propio</span>
    </article>
  </div>

  <section class="plus-footer" aria-labelledby="plus-price-title">
    <div class="plus-footer-copy">
      <p class="eyebrow">Impulso + · {{ $business->name }}</p>
      <h2 id="plus-price-title">Tu próximo paso empieza acá.</h2>
      <p class="muted">Una membresía mensual para darle más impulso a tu marca.</p>
    </div>
    <div class="plus-price-action">
      <p class="plus-price"><strong>4,99 $</strong><span>/ mes</span></p>
      <details class="plus-cta">
        <summary class="button">Tener Impulso + <span>→</span></summary>
        <p class="plus-cta-note" role="status">La membresía todavía no está disponible para activarse y este botón no realiza ningún cobro.</p>
      </details>
      <span class="plus-status">Activación próximamente</span>
    </div>
  </section>
</section>
@endsection
