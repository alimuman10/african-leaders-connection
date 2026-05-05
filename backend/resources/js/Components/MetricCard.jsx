export default function MetricCard({ label, value }) {
    return (
        <article className="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <p className="text-sm font-medium text-slate-500">{label}</p>
            <strong className="mt-2 block text-3xl font-extrabold text-slate-950">{value}</strong>
        </article>
    );
}
