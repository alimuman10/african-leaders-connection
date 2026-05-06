import { useApiQuery } from '../../lib/query';
import { DashboardMetrics, ErrorState, LoadingState, PageHeader, SectionCard, StatusBadge } from './adminPageComponents';

export default function AdminOverview() {
    const { data, isLoading, isError } = useApiQuery(['admin-dashboard'], '/api/admin/dashboard');

    if (isLoading) return <LoadingState label="Loading command center..." />;

    return (
        <div className="grid gap-6">
            <PageHeader
                title="Super Admin Dashboard"
                description="Monitor members, moderators, reports, events, submissions, activity, and growth across the African Leaders Connection platform."
            />
            {isError && <ErrorState />}
            <DashboardMetrics metrics={data?.metrics} />
            <div className="grid gap-6 xl:grid-cols-2">
                <SectionCard title="Country Distribution">
                    <div className="grid gap-3">
                        {(data?.countries?.length ? data.countries : [{ country: 'No country data yet', total: 0 }]).map((item) => (
                            <div key={item.country} className="flex items-center justify-between rounded-md bg-[#fffaf0] p-3">
                                <span className="font-bold">{item.country || 'Unknown'}</span>
                                <StatusBadge status={`${item.total} members`} />
                            </div>
                        ))}
                    </div>
                </SectionCard>
                <SectionCard title="Recent Activity">
                    <div className="grid gap-3">
                        {(data?.activity?.length ? data.activity : [{ action: 'No recent activity', created_at: null }]).map((item, index) => (
                            <div key={item.id || index} className="rounded-md bg-slate-50 p-3">
                                <p className="text-sm font-black">{item.action}</p>
                                <p className="text-xs text-slate-500">{item.created_at || 'Activity will appear here.'}</p>
                            </div>
                        ))}
                    </div>
                </SectionCard>
            </div>
        </div>
    );
}
