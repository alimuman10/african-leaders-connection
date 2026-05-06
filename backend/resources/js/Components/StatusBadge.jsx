const styles = {
    active: 'bg-emerald-50 text-emerald-800 border-emerald-200',
    approved: 'bg-emerald-50 text-emerald-800 border-emerald-200',
    pending: 'bg-amber-50 text-amber-800 border-amber-200',
    open: 'bg-rose-50 text-rose-800 border-rose-200',
    suspended: 'bg-slate-100 text-slate-700 border-slate-300',
    banned: 'bg-red-50 text-red-800 border-red-200',
};

export default function StatusBadge({ status = 'pending' }) {
    return (
        <span className={`inline-flex rounded-full border px-2.5 py-1 text-xs font-black capitalize ${styles[status] || styles.pending}`}>
            {status}
        </span>
    );
}
