export async function apiRequest(path, options = {}) {
    const token = localStorage.getItem('alc_token');
    const headers = {
        Accept: 'application/json',
        ...(options.body instanceof FormData ? {} : { 'Content-Type': 'application/json' }),
        ...(token ? { Authorization: `Bearer ${token}` } : {}),
        ...(options.headers || {}),
    };

    const response = await fetch(path, {
        headers,
        ...options,
    });

    const payload = response.status === 204 ? null : await response.json().catch(() => null);

    if (!response.ok) {
        const message = payload?.message || 'Something went wrong. Please review and try again.';
        const error = new Error(message);
        error.payload = payload;
        throw error;
    }

    return payload;
}

export function collectionFrom(payload) {
    return Array.isArray(payload?.data) ? payload.data : [];
}
