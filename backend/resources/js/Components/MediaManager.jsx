import { useEffect, useState } from 'react';
import { apiRequest, collectionFrom } from '../lib/api';

export default function MediaManager() {
    const [files, setFiles] = useState([]);
    const [file, setFile] = useState(null);
    const [loading, setLoading] = useState(true);
    const [message, setMessage] = useState('');
    const [error, setError] = useState('');

    async function load() {
        setLoading(true);
        try {
            setFiles(collectionFrom(await apiRequest('/api/media')));
        } catch (exception) {
            setError(exception.message);
        } finally {
            setLoading(false);
        }
    }

    useEffect(() => {
        load();
    }, []);

    async function upload(event) {
        event.preventDefault();
        if (!file) return;
        const formData = new FormData();
        formData.append('file', file);
        formData.append('collection', 'dashboard');
        setMessage('');
        setError('');
        try {
            await apiRequest('/api/media', { method: 'POST', body: formData });
            setMessage('Media uploaded successfully.');
            setFile(null);
            await load();
        } catch {
            setError('Upload failed. Confirm file type and size.');
        }
    }

    return (
        <section className="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
            <h3 className="text-xl font-extrabold text-slate-950">Media</h3>
            {message && <p className="mt-4 rounded-md bg-emerald-50 p-3 text-sm font-semibold text-emerald-700">{message}</p>}
            {error && <p className="mt-4 rounded-md bg-red-50 p-3 text-sm font-semibold text-red-700">{error}</p>}
            <form onSubmit={upload} className="mt-4 flex flex-wrap gap-3">
                <input type="file" accept=".jpg,.jpeg,.png,.webp,.pdf" onChange={(event) => setFile(event.target.files?.[0] || null)} className="rounded-md border border-slate-300 px-3 py-2 text-sm" />
                <button className="rounded-md bg-slate-950 px-4 py-2 text-sm font-bold text-white">Upload</button>
            </form>
            <div className="mt-5 grid gap-3 md:grid-cols-3">
                {loading ? <p className="text-sm text-slate-500">Loading media...</p> : files.length === 0 ? <p className="text-sm text-slate-500">No media uploaded yet.</p> : files.map((media) => (
                    <article key={media.id} className="rounded-md border border-slate-200 p-3 text-sm text-slate-600">
                        <strong className="block truncate text-slate-950">{media.original_name}</strong>
                        <span>{media.collection || 'general'} - {Math.round(media.size / 1024)} KB</span>
                    </article>
                ))}
            </div>
        </section>
    );
}
