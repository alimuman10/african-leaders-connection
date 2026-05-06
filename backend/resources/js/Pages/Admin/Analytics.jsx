import { useApiQuery } from '../../lib/query';
import { ErrorState, LoadingState, PageHeader, SectionCard, StatusBadge } from './adminPageComponents';

export default function Analytics() {
    const { data, isLoading, isError } = useApiQuery(['admin-analytics'], '/api/admin/analytics');

    if (isLoading) return <LoadingState label="Loading analytics..." />;

    return (
        <div className="grid gap-6">
            <PageHeader title="Analytics" description="Track member growth, content engagement, event attendance, country participation, opportunity clicks, and campaign engagement." />
            {isError && <ErrorState />}
            <div className="grid gap-6 xl:grid-cols-2">
                <SectionCard title="Member Growth">
                    <div className="grid gap-2">
                        {(data?.member_growth?.length ? data.member_growth : [{ date: 'No growth data yet', total: 0 }]).slice(0, 8).map((item) => (
                            <div key={item.date} className="flex items-center justify-between rounded-md bg-slate-50 p-3">
                                <span className="text-sm font-bold">{item.date}</span>
                                <StatusBadge status={`${item.total} members`} />
                            </div>
                        ))}
                    </div>
                </SectionCard>
                <SectionCard title="Engagement Summary">
                    <div className="grid gap-3 md:grid-cols-2">
                        <Summary label="Posts" value={data?.content_engagement?.posts ?? 0} />
                        <Summary label="Comments" value={data?.content_engagement?.comments ?? 0} />
                        <Summary label="Event Attendance" value={data?.event_attendance ?? 0} />
                        <Summary label="Campaign Supporters" value={data?.campaign_engagement ?? 0} />
                    </div>
                </SectionCard>
            </div>
        </div>
    );
}

function Summary({ label, value }) {
    return (
        <div className="rounded-md bg-[#fffaf0] p-4">
            <p className="text-xs font-black uppercase tracking-wide text-[#9b6825]">{label}</p>
            <p className="mt-2 text-3xl font-black text-[#061311]">{value}</p>
        </div>
    );
}
