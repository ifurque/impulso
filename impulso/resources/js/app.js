import './bootstrap';
import { getSupabaseClient } from './supabase/client';
import './help-tour';

if (import.meta.env.VITE_SUPABASE_URL && import.meta.env.VITE_SUPABASE_PUBLISHABLE_KEY) {
	getSupabaseClient();
}

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
		if (image.dataset.objectUrl) URL.revokeObjectURL(image.dataset.objectUrl);
		image.dataset.objectUrl = URL.createObjectURL(file);
		image.src = image.dataset.objectUrl;
		image.alt = input.dataset.previewAlt || '';
	});
});

const navigationProgress = document.createElement('div');
navigationProgress.className = 'navigation-progress';
navigationProgress.setAttribute('aria-hidden', 'true');
document.body.append(navigationProgress);

let navigationProgressValue = 0;
let navigationProgressTimer = null;

function resetNavigationProgress() {
	window.clearInterval(navigationProgressTimer);
	navigationProgressTimer = null;
	navigationProgressValue = 0;
	navigationProgress.classList.remove('is-active');
	navigationProgress.style.width = '0%';
}

document.addEventListener('click', (event) => {
	if (event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;

	const link = event.target instanceof Element ? event.target.closest('a[href]') : null;
	if (!link || (link.target && link.target !== '_self') || link.hasAttribute('download') || link.relList.contains('external')) return;

	const destination = new URL(link.href, window.location.href);
	if (destination.origin !== window.location.origin || (destination.pathname === window.location.pathname && destination.search === window.location.search)) return;

	navigationProgress.classList.add('is-active');
	navigationProgressValue = 8;
	navigationProgress.style.width = `${navigationProgressValue}%`;

	if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
		navigationProgress.style.width = '40%';
		return;
	}

	window.clearInterval(navigationProgressTimer);
	navigationProgressTimer = window.setInterval(() => {
		navigationProgressValue = Math.min(90, navigationProgressValue + Math.max(1, (90 - navigationProgressValue) * 0.08));
		navigationProgress.style.width = `${navigationProgressValue}%`;
	}, 120);
});

window.addEventListener('pageshow', resetNavigationProgress);
