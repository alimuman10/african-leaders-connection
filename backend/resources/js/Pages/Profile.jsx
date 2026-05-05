import { useEffect, useState } from 'react';
import DashboardLayout from '../Layouts/DashboardLayout';
import { Alert, Field, SubmitButton } from './Login';
import { apiRequest } from '../lib/api';

export default function Profile() {
    const [form, setForm] = useState({ name: '', phone: '', country: '', profession: '', organization: '', leadership_interest: '', bio: '' });
    const [passwords, setPasswords] = useState({ current_password: '', password: '', password_confirmation: '' });
    const [message, setMessage] = useState('');
    const [error, setError] = useState('');
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        apiRequest('/api/profile')
            .then((payload) => {
                const user = payload.data;
                setForm({
                    name: user.name || '',
                    phone: user.phone || user.profile?.phone || '',
                    country: user.country || user.profile?.country || '',
                    profession: user.profession || user.profile?.profession || '',
                    organization: user.organization || user.profile?.organization || '',
                    leadership_interest: user.leadership_interest || user.profile?.leadership_interest || '',
                    bio: user.profile?.bio || '',
                });
            })
            .catch((exception) => setError(exception.message))
            .finally(() => setLoading(false));
    }, []);

    function update(field, value) {
        setForm((current) => ({ ...current, [field]: value }));
    }

    async function saveProfile(event) {
        event.preventDefault();
        setMessage('');
        setError('');
        try {
            await apiRequest('/api/profile', { method: 'PUT', body: JSON.stringify(form) });
            setMessage('Profile updated successfully.');
        } catch (exception) {
            setError(exception.payload?.message || exception.message);
        }
    }

    async function savePassword(event) {
        event.preventDefault();
        setMessage('');
        setError('');
        try {
            await apiRequest('/api/password', { method: 'PUT', body: JSON.stringify(passwords) });
            setMessage('Password updated. Please log in again.');
            localStorage.removeItem('alc_token');
        } catch (exception) {
            setError(exception.payload?.message || exception.message);
        }
    }

    async function deactivate() {
        if (!window.confirm('Deactivate this account?')) return;
        await apiRequest('/api/account/deactivate', { method: 'POST', body: JSON.stringify({}) });
        localStorage.removeItem('alc_token');
        window.location.href = '/login';
    }

    return (
        <DashboardLayout title="Profile Settings">
            {loading ? <p className="text-sm text-slate-500">Loading profile...</p> : (
                <div className="grid gap-6 xl:grid-cols-2">
                    <section className="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
                        <h3 className="text-xl font-extrabold text-slate-950">Profile</h3>
                        {message && <Alert>{message}</Alert>}
                        {error && <Alert type="error">{error}</Alert>}
                        <form onSubmit={saveProfile} className="mt-5 grid gap-4">
                            {Object.entries({ name: 'Full name', phone: 'Phone', country: 'Country', profession: 'Profession / role', organization: 'Organization', leadership_interest: 'Leadership interest' }).map(([key, label]) => (
                                <Field key={key} label={label} required={!['organization', 'leadership_interest'].includes(key)} value={form[key]} onChange={(value) => update(key, value)} />
                            ))}
                            <label>
                                <span className="text-sm font-bold text-slate-700">Short bio</span>
                                <textarea value={form.bio} onChange={(event) => update('bio', event.target.value)} className="mt-2 min-h-28 w-full rounded-md border border-slate-300 px-3 py-3 text-sm" />
                            </label>
                            <SubmitButton>Save profile</SubmitButton>
                        </form>
                    </section>
                    <section className="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
                        <h3 className="text-xl font-extrabold text-slate-950">Security</h3>
                        <form onSubmit={savePassword} className="mt-5 grid gap-4">
                            <Field label="Current password" type="password" value={passwords.current_password} onChange={(value) => setPasswords({ ...passwords, current_password: value })} />
                            <Field label="New password" type="password" value={passwords.password} onChange={(value) => setPasswords({ ...passwords, password: value })} />
                            <Field label="Confirm new password" type="password" value={passwords.password_confirmation} onChange={(value) => setPasswords({ ...passwords, password_confirmation: value })} />
                            <SubmitButton>Update password</SubmitButton>
                        </form>
                        <button onClick={deactivate} className="mt-6 rounded-md border border-red-300 px-4 py-3 text-sm font-black text-red-700">Deactivate account</button>
                    </section>
                </div>
            )}
        </DashboardLayout>
    );
}
