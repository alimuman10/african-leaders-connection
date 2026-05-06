import MetricCard from '../../Components/MetricCard';
import SectionCard from '../../Components/SectionCard';
import StatusBadge from '../../Components/StatusBadge';

export function PageHeader({ eyebrow = 'Super Admin', title, description, action }) {
    return (
        <section className="rounded-lg bg-[#061311] p-6 text-white">
            <div className="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                <div>
                    <p className="text-xs font-black uppercase tracking-[0.2em] text-[#dfb15b]">{eyebrow}</p>
                    <h2 className="mt-2 text-3xl font-black">{title}</h2>
                    <p className="mt-3 max-w-3xl text-sm leading-6 text-slate-200">{description}</p>
                </div>
                {action && <div>{action}</div>}
            </div>
        </section>
    );
}

export function LoadingState({ label = 'Loading records...' }) {
    return <div className="rounded-lg bg-white p-6 text-sm font-bold text-slate-500 shadow-sm">{label}</div>;
}

export function ErrorState({ label = 'Unable to load live data. Showing safe fallback state.' }) {
    return <div className="rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm font-bold text-amber-800">{label}</div>;
}

export function EmptyState({ title = 'No records yet', description = 'Records will appear here once they are available.' }) {
    return (
        <div className="rounded-lg border border-dashed border-[#d7c49a] bg-white p-6 text-center">
            <h3 className="text-lg font-black text-[#061311]">{title}</h3>
            <p className="mt-2 text-sm text-slate-600">{description}</p>
        </div>
    );
}

export function AdminTable({ columns, rows, emptyTitle, emptyDescription }) {
    if (!rows?.length) return <EmptyState title={emptyTitle} description={emptyDescription} />;

    return (
        <div className="overflow-x-auto rounded-lg border border-slate-200 bg-white">
            <table className="w-full min-w-[720px] text-left text-sm">
                <thead className="border-b border-[#e2d5b8] bg-[#fffaf0] text-xs uppercase tracking-wide text-[#9b6825]">
                    <tr>
                        {columns.map((column) => <th key={column.key} className="px-4 py-3">{column.label}</th>)}
                    </tr>
                </thead>
                <tbody>
                    {rows.map((row, index) => (
                        <tr key={row.id || index} className="border-b border-slate-100">
                            {columns.map((column) => (
                                <td key={column.key} className="px-4 py-3">
                                    {column.render ? column.render(row) : row[column.key] || '-'}
                                </td>
                            ))}
                        </tr>
                    ))}
                </tbody>
            </table>
        </div>
    );
}

export function DashboardMetrics({ metrics }) {
    return (
        <div className="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            <MetricCard label="Total Members" value={metrics?.total_members ?? 0} />
            <MetricCard label="Active Members" value={metrics?.active_members ?? 0} />
            <MetricCard label="Moderators" value={metrics?.total_moderators ?? 0} />
            <MetricCard label="Pending Submissions" value={metrics?.pending_submissions ?? 0} />
            <MetricCard label="Reported Content" value={metrics?.reported_content ?? 0} />
            <MetricCard label="Upcoming Events" value={metrics?.upcoming_events ?? 0} />
        </div>
    );
}

export function ActionButton({ children }) {
    return <button className="rounded-md bg-[#dfb15b] px-4 py-3 text-sm font-black text-[#1d1305] hover:bg-[#c7973f]">{children}</button>;
}

export { SectionCard, StatusBadge };
