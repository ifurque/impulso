import './bootstrap';
import { getSupabaseClient } from './supabase/client';
import './help-tour';

// Inicializa Supabase solo si las variables de entorno de Vite están configuradas.
if (import.meta.env.VITE_SUPABASE_URL && import.meta.env.VITE_SUPABASE_PUBLISHABLE_KEY) {
	getSupabaseClient();
}

// Vista previa de imágenes: cada input file con data-image-preview muestra la imagen elegida
// dentro del contenedor indicado por ese selector.
document.querySelectorAll('input[type="file"][data-image-preview]').forEach((input) => {
	input.addEventListener('change', () => {
		const file = input.files?.[0];
		const preview = document.querySelector(input.dataset.imagePreview);
		if (!file || !preview) return;

		let image = preview.querySelector('img');
		if (!image) {
			image = document.createElement('img');
			preview.replaceChildren(image);
		}
		// Libera la URL de objeto anterior para evitar fugas de memoria.
		if (image.dataset.objectUrl) URL.revokeObjectURL(image.dataset.objectUrl);
		image.dataset.objectUrl = URL.createObjectURL(file);
		image.src = image.dataset.objectUrl;
		image.alt = input.dataset.previewAlt || '';
	});
});

// Barra de progreso de navegación: se agrega al body y se anima al hacer clic en enlaces internos.
const navigationProgress = document.createElement('div');
navigationProgress.className = 'navigation-progress';
navigationProgress.setAttribute('aria-hidden', 'true');
document.body.append(navigationProgress);

let navigationProgressValue = 0;
let navigationProgressTimer = null;

/**
 * Detiene la animación de la barra de navegación y la devuelve a 0%.
 * Se usa también al volver a la página desde el historial (pageshow).
 */
function resetNavigationProgress() {
	window.clearInterval(navigationProgressTimer);
	navigationProgressTimer = null;
	navigationProgressValue = 0;
	navigationProgress.classList.remove('is-active');
	navigationProgress.style.width = '0%';
}

document.addEventListener('click', (event) => {
	// Ignora clics modificados, con otro botón o ya manejados por otro script.
	if (event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;

	// Ignora enlaces que abren en otra pestaña, descargas o enlaces externos.
	const link = event.target instanceof Element ? event.target.closest('a[href]') : null;
	if (!link || (link.target && link.target !== '_self') || link.hasAttribute('download') || link.relList.contains('external')) return;

	// Ignora otros orígenes y enlaces a la misma página (no hay navegación real).
	const destination = new URL(link.href, window.location.href);
	if (destination.origin !== window.location.origin || (destination.pathname === window.location.pathname && destination.search === window.location.search)) return;

	navigationProgress.classList.add('is-active');
	navigationProgressValue = 8;
	navigationProgress.style.width = `${navigationProgressValue}%`;

	// Respeta la preferencia de movimiento reducido: ancho fijo, sin animación.
	if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
		navigationProgress.style.width = '40%';
		return;
	}

	// Avanza de forma asintótica hasta 90%, esperando a que cargue la nueva página.
	window.clearInterval(navigationProgressTimer);
	navigationProgressTimer = window.setInterval(() => {
		navigationProgressValue = Math.min(90, navigationProgressValue + Math.max(1, (90 - navigationProgressValue) * 0.08));
		navigationProgress.style.width = `${navigationProgressValue}%`;
	}, 120);
});

// Restablece la barra al restaurar la página desde la caché del navegador (botón atrás).
window.addEventListener('pageshow', resetNavigationProgress);
