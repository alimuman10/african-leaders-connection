import { useCollectionQuery } from '../../lib/query';
import { AdminTable, ErrorState, LoadingState, PageHeader, SectionCard, StatusBadge } from './adminPageComponents';

export default function ReportsModeration() {
    const { data: reports = [], isLoading, isError } = useCollectionQuery(['admin-reports'], '/api/admin/reports');

    return (
        <div className="grid gap-6">
            <PageHeader title="Reports & Moderation" description="Review reported posts and comments, assign reports to moderators, resolve issues, and keep audit history." />
            {isLoading && <LoadingState label="Loading reports..." />}
            {isError && <ErrorState />}
            <SectionCard title="Reported Content">
                <AdminTable
                    rows={reports}
                    emptyTitle="No open reports"
                    emptyDescription="Reported posts and comments will appear here."
                    columns={[
                        { key: 'reason', label: 'Reason' },
                        { key: 'status', label: 'Status', render: (row) => <StatusBadge status={row.status || 'open'} /> },
                        { key: 'assigned_to', label: 'Assigned To', render: () => 'Unassigned' },
                        { key: 'actions', label: 'Actions', render: () => <button className="rounded-md border px-3 py-2 text-xs font-bold">Resolve Report</button> },
                    ]}
                />
            </SectionCard>
        </div>
    );
}
