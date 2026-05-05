import { useEffect, useState } from 'react';
import { apiRequest, collectionFrom } from '../lib/api';

export default function MessagesPanel() {
    const [messages, setMessages] = useState([]);
    const [loading, setLoading] = useState(true);
    const [notice, setNotice] = useState('');
    const [error, setError] = useState('');

    async function load() {
        setLoading(true);
        try {
            setMessages(collectionFrom(await apiRequest('/api/contact/messages')));
        } catch (exception) {
            setError(exception.message);
        } finally {
            setLoading(false);
        }
    }

    useEffect(() => {
        load();
    }, []);

    async function setStatus(message, action) {
        setNotice('');
        setError('');
        try {
            await apiRequest(`/api/contact/messages/${message.id}/${action}`, { method: 'POST', body: JSON.stringify({ reply: 'Handled from admin dashboard.' }) });
            setNotice(`Message ${action} completed.`);
            await load();
        } catch (exception) {
            setError(exception.message);
        }
    }

    return (
        <section className="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
            <h3 className="text-xl font-extrabold text-slate-950">Contact Messages</h3>
            {notice && <p className="mt-4 rounded-md bg-emerald-50 p-3 text-sm font-semibold text-emerald-700">{notice}</p>}
            {error && <p className="mt-4 rounded-md bg-red-50 p-3 text-sm font-semibold text-red-700">{error}</p>}
            {loading ? (
                <p className="mt-5 text-sm text-slate-500">Loading messages...</p>
            ) : messages.length === 0 ? (
                <p className="mt-5 text-sm text-slate-500">No contact messages yet.</p>
            ) : (
                <div className="mt-5 divide-y divide-slate-200 rounded-lg border border-slate-200">
                    {messages.map((message) => (
                        <article key={message.id} className="grid gap-3 p-4">
                            <div>
                                <strong className="text-slate-950">{message.subject}</strong>
                                <p className="text-sm text-slate-500">{message.name} · {message.email} · {message.status}</p>
                            </div>
                            <p className="text-sm leading-6 text-slate-600">{message.message}</p>
                            <div className="flex gap-2">
                                <button onClick={() => setStatus(message, 'reply')} className="rounded-md bg-slate-950 px-3 py-2 text-xs font-bold text-white">Mark replied</button>
                                <button onClick={() => setStatus(message, 'archive')} className="rounded-md border border-slate-300 px-3 py-2 text-xs font-bold text-slate-700">Archive</button>
                            </div>
                        </article>
                    ))}
                </div>
            )}
        </section>
    );
}
