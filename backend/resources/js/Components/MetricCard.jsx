export default function MetricCard({ label, value, detail }) {
    return (
        <article className="rounded-lg border border-[#e2d5b8] bg-white p-5 shadow-sm">
            <p className="text-xs font-black uppercase tracking-[0.16em] text-[#9b6825]">{label}</p>
            <strong className="mt-2 block text-3xl font-black text-[#061311]">{value}</strong>
            {detail && <span className="mt-2 block text-sm leading-6 text-slate-600">{detail}</span>}
        </article>
    );
}
