import { useState } from 'react';
import { Alert, AuthShell, Field, SubmitButton } from './Login';
import { apiRequest } from '../lib/api';

export default function ResetPassword() {
    const params = new URLSearchParams(window.location.search);
    const [form, setForm] = useState({ token: params.get('token') || '', email: params.get('email') || '', password: '', password_confirmation: '' });
    const [message, setMessage] = useState('');
    const [error, setError] = useState('');
    const [loading, setLoading] = useState(false);

    async function submit(event) {
        event.preventDefault();
        setLoading(true);
        setError('');
        setMessage('');
        try {
            const payload = await apiRequest('/api/reset-password', { method: 'POST', body: JSON.stringify(form) });
            setMessage(payload?.message || 'Password reset successfully.');
        } catch (exception) {
            setError(exception.payload?.message || exception.message);
        } finally {
            setLoading(false);
        }
    }

    return (
        <AuthShell title="Reset Password" subtitle="Choose a strong new password.">
            {message && <Alert>{message}</Alert>}
            {error && <Alert type="error">{error}</Alert>}
            <form onSubmit={submit} className="grid gap-4">
                <Field label="Email address" type="email" value={form.email} onChange={(value) => setForm({ ...form, email: value })} />
                <Field label="Reset token" value={form.token} onChange={(value) => setForm({ ...form, token: value })} />
                <Field label="New password" type="password" value={form.password} onChange={(value) => setForm({ ...form, password: value })} />
                <Field label="Confirm new password" type="password" value={form.password_confirmation} onChange={(value) => setForm({ ...form, password_confirmation: value })} />
                <SubmitButton loading={loading}>Reset password</SubmitButton>
            </form>
        </AuthShell>
    );
}
