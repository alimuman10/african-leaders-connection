import { useState } from 'react';
import { Alert, AuthShell, Field, SubmitButton } from './Login';
import { apiRequest } from '../lib/api';

export default function ForgotPassword() {
    const [email, setEmail] = useState('');
    const [message, setMessage] = useState('');
    const [error, setError] = useState('');
    const [loading, setLoading] = useState(false);

    async function submit(event) {
        event.preventDefault();
        setLoading(true);
        setError('');
        setMessage('');
        try {
            const payload = await apiRequest('/api/forgot-password', { method: 'POST', body: JSON.stringify({ email }) });
            setMessage(payload?.message || 'Password reset link sent if the email exists.');
        } catch (exception) {
            setError(exception.payload?.message || exception.message);
        } finally {
            setLoading(false);
        }
    }

    return (
        <AuthShell title="Forgot Password" subtitle="Request a secure password reset link.">
            {message && <Alert>{message}</Alert>}
            {error && <Alert type="error">{error}</Alert>}
            <form onSubmit={submit} className="grid gap-4">
                <Field label="Email address" type="email" value={email} onChange={setEmail} />
                <SubmitButton loading={loading}>Send reset link</SubmitButton>
            </form>
        </AuthShell>
    );
}
