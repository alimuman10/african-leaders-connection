import { useState } from 'react';
import { Alert, AuthShell, Field, SubmitButton } from './Login';
import { apiRequest } from '../lib/api';

export default function Register() {
    const [form, setForm] = useState({ name: '', email: '', phone: '', country: '', profession: '', organization: '', leadership_interest: '', password: '', password_confirmation: '' });
    const [error, setError] = useState('');
    const [success, setSuccess] = useState('');
    const [loading, setLoading] = useState(false);

    function update(field, value) {
        setForm((current) => ({ ...current, [field]: value }));
    }

    async function submit(event) {
        event.preventDefault();
        setLoading(true);
        setError('');
        setSuccess('');
        try {
            const payload = await apiRequest('/api/register', { method: 'POST', body: JSON.stringify(form) });
            if (payload?.token) sessionStorage.setItem('alc_token', payload.token);
            setSuccess('Account created. Please check your email to verify your account before accessing the dashboard.');
            window.location.href = payload?.user?.redirect_path || '/member/dashboard';
        } catch (exception) {
            setError(exception.payload?.message || exception.message);
        } finally {
            setLoading(false);
        }
    }

    return (
        <AuthShell title="Create Member Account" subtitle="Join a platform built for leadership, unity, and progress.">
            {error && <Alert type="error">{error}</Alert>}
            {success && <Alert>{success}</Alert>}
            <form onSubmit={submit} className="grid gap-4 md:grid-cols-2">
                <Field label="Full name" value={form.name} onChange={(value) => update('name', value)} />
                <Field label="Email address" type="email" value={form.email} onChange={(value) => update('email', value)} />
                <Field label="Phone number" value={form.phone} onChange={(value) => update('phone', value)} />
                <Field label="Country" value={form.country} onChange={(value) => update('country', value)} />
                <Field label="Profession / role" value={form.profession} onChange={(value) => update('profession', value)} />
                <Field label="Organization" required={false} value={form.organization} onChange={(value) => update('organization', value)} />
                <Field label="Leadership interest" required={false} value={form.leadership_interest} onChange={(value) => update('leadership_interest', value)} />
                <Field label="Password" type="password" value={form.password} onChange={(value) => update('password', value)} />
                <Field label="Confirm password" type="password" value={form.password_confirmation} onChange={(value) => update('password_confirmation', value)} />
                <div className="md:col-span-2"><SubmitButton loading={loading}>Create account</SubmitButton></div>
            </form>
            <p className="mt-5 text-sm text-slate-600">Already have an account? <a className="font-bold text-amber-700" href="/login">Login</a></p>
        </AuthShell>
    );
}
