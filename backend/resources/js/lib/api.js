export async function apiRequest(path, options = {}) {
    const token = sessionStorage.getItem('alc_token') || localStorage.getItem('alc_token');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const xsrfToken = readCookie('XSRF-TOKEN');
    const headers = {
        Accept: 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {}),
        ...(xsrfToken ? { 'X-XSRF-TOKEN': decodeURIComponent(xsrfToken) } : {}),
        ...(options.body instanceof FormData ? {} : { 'Content-Type': 'application/json' }),
        ...(token ? { Authorization: `Bearer ${token}` } : {}),
        ...(options.headers || {}),
    };

    const response = await fetch(path, {
        credentials: 'same-origin',
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

function readCookie(name) {
    return document.cookie
        .split('; ')
        .find((cookie) => cookie.startsWith(`${name}=`))
        ?.split('=')
        .slice(1)
        .join('=');
}
