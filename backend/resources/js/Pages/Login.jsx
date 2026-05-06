import { useState } from 'react';
import { apiRequest } from '../lib/api';

const adminRoles = ['Super Admin', 'Admin', 'Content Manager', 'Community Manager'];

export default function Login() {
    const [showPassword, setShowPassword] = useState(false);
    const [form, setForm] = useState({ email: '', password: '', remember: false });
    const [error, setError] = useState('');
    const [loading, setLoading] = useState(false);

    async function submit(event) {
        event.preventDefault();
        setLoading(true);
        setError('');
        try {
            const payload = await apiRequest('/api/login', {
                method: 'POST',
                body: JSON.stringify({ ...form, device_name: 'dashboard' }),
            });
            if (payload?.token) {
                const storage = form.remember ? localStorage : sessionStorage;
                storage.setItem('alc_token', payload.token);
                (form.remember ? sessionStorage : localStorage).removeItem('alc_token');
            }
            const roles = payload?.user?.roles || [];
            window.location.href = payload?.user?.redirect_path || (roles.some((role) => adminRoles.includes(role)) ? '/admin/dashboard' : '/member/dashboard');
        } catch (exception) {
            setError(exception.payload?.message || exception.message);
        } finally {
            setLoading(false);
        }
    }

    return (
        <AuthShell title="Platform Login" subtitle="Access your African Leaders Connection workspace.">
            {error && <Alert type="error">{error}</Alert>}
            <form onSubmit={submit} className="grid gap-4">
                <Field label="Email address" type="email" value={form.email} onChange={(value) => setForm({ ...form, email: value })} />
                <Field label="Password" type={showPassword ? 'text' : 'password'} value={form.password} onChange={(value) => setForm({ ...form, password: value })} />
                <div className="flex flex-wrap items-center justify-between gap-3 text-sm">
                    <label className="flex items-center gap-2 font-semibold text-slate-600">
                        <input type="checkbox" checked={form.remember} onChange={(event) => setForm({ ...form, remember: event.target.checked })} />
                        Remember me
                    </label>
                    <button type="button" onClick={() => setShowPassword(!showPassword)} className="font-bold text-amber-700">{showPassword ? 'Hide password' : 'Show password'}</button>
                    <a href="/forgot-password" className="font-bold text-amber-700">Forgot Password?</a>
                </div>
                <SubmitButton loading={loading}>Login</SubmitButton>
            </form>
            <p className="mt-5 text-sm text-slate-600">Need an account? <a className="font-bold text-amber-700" href="/register">Create account</a></p>
        </AuthShell>
    );
}

export function AuthShell({ title, subtitle, children }) {
    return (
        <main className="grid min-h-screen place-items-center bg-slate-950 px-5 py-10">
            <section className="w-full max-w-lg rounded-lg bg-white p-7 shadow-2xl">
                <p className="text-xs font-extrabold uppercase tracking-[0.22em] text-amber-600">African Leaders Connection</p>
                <h1 className="mt-3 text-3xl font-black text-slate-950">{title}</h1>
                <p className="mt-2 text-sm leading-6 text-slate-600">{subtitle || 'Leadership. Unity. Progress.'}</p>
                <div className="mt-6">{children}</div>
            </section>
        </main>
    );
}

export function Alert({ type = 'success', children }) {
    const classes = type === 'error' ? 'bg-red-50 text-red-700' : 'bg-emerald-50 text-emerald-700';
    return <p className={`mb-4 rounded-md p-3 text-sm font-bold ${classes}`}>{children}</p>;
}

export function Field({ label, value, onChange, type = 'text', required = true }) {
    return (
        <label>
            <span className="text-sm font-bold text-slate-700">{label}</span>
            <input required={required} type={type} value={value} onChange={(event) => onChange(event.target.value)} className="mt-2 w-full rounded-md border border-slate-300 px-3 py-3 text-sm outline-none focus:border-amber-500" />
        </label>
    );
}

export function SubmitButton({ loading, children, onClick, type = 'submit' }) {
    return <button type={type} onClick={onClick} disabled={loading} className="rounded-md bg-slate-950 px-4 py-3 text-sm font-black text-white disabled:opacity-60">{loading ? 'Please wait...' : children}</button>;
}
