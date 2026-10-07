import './bootstrap';
import { getSupabaseClient } from './supabase/client';

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
