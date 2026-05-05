import { useEffect, useMemo, useState } from 'react';
import { apiRequest, collectionFrom } from '../lib/api';

const defaultFields = [
    { name: 'title', label: 'Title', required: true },
    { name: 'summary', label: 'Summary' },
    { name: 'status', label: 'Status' },
];

export default function ResourceManager({ title, endpoint, fields = defaultFields }) {
    const initialForm = useMemo(() => Object.fromEntries(fields.map((field) => [field.name, ''])), [fields]);
    const [items, setItems] = useState([]);
    const [form, setForm] = useState(initialForm);
    const [editingId, setEditingId] = useState(null);
    const [loading, setLoading] = useState(true);
    const [saving, setSaving] = useState(false);
    const [message, setMessage] = useState('');
    const [error, setError] = useState('');

    async function load() {
        setLoading(true);
        setError('');
        try {
            const payload = await apiRequest(endpoint);
            setItems(collectionFrom(payload));
        } catch (exception) {
            setError(exception.message);
        } finally {
            setLoading(false);
        }
    }

    useEffect(() => {
        load();
    }, [endpoint]);

    function edit(item) {
        setEditingId(item.id);
        setForm(Object.fromEntries(fields.map((field) => [field.name, item[field.name] ?? ''])));
        setMessage('');
        setError('');
    }

    function reset() {
        setEditingId(null);
        setForm(initialForm);
    }

    async function submit(event) {
        event.preventDefault();
        setSaving(true);
        setMessage('');
        setError('');
        try {
            await apiRequest(editingId ? `${endpoint}/${editingId}` : endpoint, {
                method: editingId ? 'PUT' : 'POST',
                body: JSON.stringify(form),
            });
            setMessage(editingId ? `${title} updated successfully.` : `${title} created successfully.`);
            reset();
            await load();
        } catch (exception) {
            setError(exception.payload?.message || exception.message);
        } finally {
            setSaving(false);
        }
    }

    async function remove(item) {
        if (!window.confirm(`Delete "${item.title || item.name || item.subject}"?`)) return;
        setError('');
        setMessage('');
        try {
            await apiRequest(`${endpoint}/${item.id}`, { method: 'DELETE' });
            setMessage(`${title} deleted successfully.`);
            await load();
        } catch (exception) {
            setError(exception.message);
        }
    }

    return (
        <section className="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
            <div className="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <h3 className="text-xl font-extrabold text-slate-950">{title}</h3>
                    <p className="mt-1 text-sm text-slate-500">Create, update, filter, and maintain platform content.</p>
                </div>
                {editingId && <button onClick={reset} className="rounded-md border border-slate-300 px-3 py-2 text-sm font-bold text-slate-700">Cancel edit</button>}
            </div>

            {message && <p className="mt-4 rounded-md bg-emerald-50 p-3 text-sm font-semibold text-emerald-700">{message}</p>}
            {error && <p className="mt-4 rounded-md bg-red-50 p-3 text-sm font-semibold text-red-700">{error}</p>}

            <form onSubmit={submit} className="mt-5 grid gap-4 md:grid-cols-2">
                {fields.map((field) => (
                    <label key={field.name} className={field.type === 'textarea' ? 'md:col-span-2' : ''}>
                        <span className="text-sm font-bold text-slate-700">{field.label}</span>
                        {field.type === 'textarea' ? (
                            <textarea
                                required={field.required}
                                value={form[field.name] ?? ''}
                                onChange={(event) => setForm({ ...form, [field.name]: event.target.value })}
                                className="mt-2 min-h-28 w-full rounded-md border border-slate-300 px-3 py-2 text-sm outline-none focus:border-amber-500"
                            />
                        ) : (
                            <input
                                required={field.required}
                                value={form[field.name] ?? ''}
                                onChange={(event) => setForm({ ...form, [field.name]: event.target.value })}
                                className="mt-2 w-full rounded-md border border-slate-300 px-3 py-2 text-sm outline-none focus:border-amber-500"
                            />
                        )}
                    </label>
                ))}
                <button disabled={saving} className="rounded-md bg-slate-950 px-4 py-3 text-sm font-extrabold text-white hover:bg-slate-800 disabled:opacity-60">
                    {saving ? 'Saving...' : editingId ? 'Update' : 'Create'}
                </button>
            </form>

            <div className="mt-6 overflow-hidden rounded-lg border border-slate-200">
                {loading ? (
                    <p className="p-6 text-sm text-slate-500">Loading {title.toLowerCase()}...</p>
                ) : items.length === 0 ? (
                    <p className="p-6 text-sm text-slate-500">No records yet. Add the first item above.</p>
                ) : (
                    <div className="divide-y divide-slate-200">
                        {items.map((item) => (
                            <article key={item.id} className="flex flex-wrap items-center justify-between gap-4 p-4">
                                <div>
                                    <strong className="block text-sm text-slate-950">{item.title || item.name || item.subject || `Record #${item.id}`}</strong>
                                    <span className="text-sm text-slate-500">{item.summary || item.email || item.status || item.category || 'Managed platform record'}</span>
                                </div>
                                <div className="flex gap-2">
                                    <button onClick={() => edit(item)} className="rounded-md border border-slate-300 px-3 py-2 text-xs font-bold text-slate-700">Edit</button>
                                    <button onClick={() => remove(item)} className="rounded-md bg-red-600 px-3 py-2 text-xs font-bold text-white">Delete</button>
                                </div>
                            </article>
                        ))}
                    </div>
                )}
            </div>
        </section>
    );
}
