import { useState } from 'react';
import { Alert, AuthShell, SubmitButton } from './Login';
import { apiRequest } from '../lib/api';

export default function VerifyEmail() {
    const [message, setMessage] = useState('Please verify your email address before accessing the dashboard.');
    const [error, setError] = useState('');
    const [loading, setLoading] = useState(false);

    async function resend() {
        setLoading(true);
        setError('');
        try {
            const payload = await apiRequest('/api/email/verification-notification', { method: 'POST', body: JSON.stringify({}) });
            setMessage(payload?.message || 'Verification link sent.');
        } catch (exception) {
            setError(exception.payload?.message || exception.message);
        } finally {
            setLoading(false);
        }
    }

    return (
        <AuthShell title="Verify Email" subtitle="One more step to protect the platform community.">
            {message && <Alert>{message}</Alert>}
            {error && <Alert type="error">{error}</Alert>}
            <SubmitButton loading={loading} onClick={resend}>Resend verification link</SubmitButton>
        </AuthShell>
    );
}
