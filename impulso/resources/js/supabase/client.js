import { createClient } from '@supabase/supabase-js';

let client;

export function getSupabaseClient() {
    if (client) {
        return client;
    }

    const url = import.meta.env.VITE_SUPABASE_URL;
    const publishableKey = import.meta.env.VITE_SUPABASE_PUBLISHABLE_KEY;

    if (!url || !publishableKey) {
        throw new Error('Supabase browser configuration is missing.');
    }

    client = createClient(url, publishableKey, {
        auth: {
            autoRefreshToken: true,
            persistSession: true,
            detectSessionInUrl: true,
        },
    });

    return client;
}