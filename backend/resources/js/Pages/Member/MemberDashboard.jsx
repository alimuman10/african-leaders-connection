import { useEffect, useMemo, useState } from 'react';
import DashboardLayout from '../../Layouts/DashboardLayout';
import MetricCard from '../../Components/MetricCard';
import SectionCard from '../../Components/SectionCard';
import StatusBadge from '../../Components/StatusBadge';
import { apiRequest, collectionFrom } from '../../lib/api';

const nav = [
    'Overview',
    'My Profile',
    'Leadership Feed',
    'My Submissions',
    'Events',
    'Opportunities',
    'Advocacy Campaigns',
    'Notifications',
    'Settings',
].map((label) => ({ label, href: `#${label.toLowerCase().replaceAll(' ', '-')}` }));

export default function MemberDashboard() {
    const [user, setUser] = useState(null);
    const [dashboard, setDashboard] = useState(null);
    const [events, setEvents] = useState([]);
    const [opportunities, setOpportunities] = useState([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        Promise.all([
            apiRequest('/api/user').catch(() => null),
            apiRequest('/api/member/dashboard').catch(() => null),
            apiRequest('/api/member/events').catch(() => ({ data: [] })),
            apiRequest('/api/member/opportunities').catch(() => ({ data: [] })),
        ]).then(([userPayload, dashboardPayload, eventPayload, opportunityPayload]) => {
            setUser(userPayload?.data || userPayload || null);
            setDashboard(dashboardPayload || null);
            setEvents(collectionFrom(eventPayload));
            setOpportunities(collectionFrom(opportunityPayload));
        }).finally(() => setLoading(false));
    }, []);

    const isModerator = useMemo(() => Boolean(user?.is_moderator || user?.roles?.includes?.('Moderator') || dashboard?.moderator?.enabled), [user, dashboard]);
    const navItems = isModerator ? [...nav, { label: 'Moderator Tools', href: '#moderator-tools' }] : nav;

    if (loading) {
        return (
            <DashboardLayout title="Member Dashboard" navItems={navItems}>
                <div className="grid gap-4 md:grid-cols-3">
                    {[1, 2, 3].map((item) => <div key={item} className="h-32 animate-pulse rounded-lg bg-white" />)}
                </div>
            </DashboardLayout>
        );
    }

    return (
        <DashboardLayout title="Member Dashboard" eyebrow="Member Workspace" navItems={navItems} user={user}>
            <div className="grid gap-6">
                <section id="overview" className="rounded-lg bg-[#061311] p-6 text-white">
                    <p className="text-xs font-black uppercase tracking-[0.2em] text-[#dfb15b]">Welcome</p>
                    <h2 className="mt-2 text-3xl font-black">Welcome, {user?.name || 'Member'}.</h2>
                    <p className="mt-3 max-w-3xl text-slate-200">
                        Your dashboard brings profile growth, leadership opportunities, submissions, campaigns, events, and notifications into one professional workspace.
                    </p>
                </section>

                <div className="grid gap-4 md:grid-cols-3 xl:grid-cols-4">
                    <MetricCard label="Profile Completion" value={`${dashboard?.profile_completion ?? 0}%`} detail="Keep your profile current for better opportunities." />
                    <MetricCard label="Impact Score" value={dashboard?.leadership_impact_score ?? 0} detail="A starter score for engagement and contribution." />
                    <MetricCard label="Upcoming Events" value={events.length} detail="Leadership events and reminders." />
                    <MetricCard label="Opportunities" value={opportunities.length} detail="Programs, grants, jobs, and fellowships." />
                </div>

                <div className="grid gap-6 xl:grid-cols-2">
                    <SectionCard title="Quick Actions">
                        <div className="grid gap-3 sm:grid-cols-2">
                            {['Complete profile', 'Submit leadership story', 'Find opportunities', 'Join advocacy campaign'].map((action) => (
                                <button key={action} className="rounded-md bg-[#102a25] px-4 py-3 text-left text-sm font-black text-white hover:bg-[#061311]">{action}</button>
                            ))}
                        </div>
                    </SectionCard>

                    <SectionCard title="Latest Announcements">
                        <div className="grid gap-3">
                            {(dashboard?.latest_announcements?.length ? dashboard.latest_announcements : [{ title: 'No announcements yet', body: 'Platform announcements will appear here.' }]).map((item, index) => (
                                <article key={item.id || index} className="rounded-md bg-[#fffaf0] p-3">
                                    <h3 className="font-black text-[#061311]">{item.title}</h3>
                                    <p className="mt-1 text-sm text-slate-600">{item.body || item.summary}</p>
                                </article>
                            ))}
                        </div>
                    </SectionCard>
                </div>

                <div className="grid gap-6 xl:grid-cols-3">
                    <SectionCard title="My Profile">
                        <dl className="grid gap-3 text-sm">
                            <div><dt className="font-black text-[#9b6825]">Country</dt><dd>{user?.country || user?.profile?.country || 'Not provided'}</dd></div>
                            <div><dt className="font-black text-[#9b6825]">Organization</dt><dd>{user?.organization || user?.profile?.organization || 'Not provided'}</dd></div>
                            <div><dt className="font-black text-[#9b6825]">Leadership Category</dt><dd>{user?.profile?.leadership_category || 'Not provided'}</dd></div>
                        </dl>
                    </SectionCard>

                    <SectionCard title="Events">
                        <div className="grid gap-3">
                            {(events.length ? events : [{ title: 'No events published yet', status: 'pending' }]).slice(0, 4).map((event, index) => (
                                <div key={event.id || index} className="flex items-center justify-between gap-3 rounded-md bg-slate-50 p-3">
                                    <span className="text-sm font-bold">{event.title}</span>
                                    <StatusBadge status={event.status || 'pending'} />
                                </div>
                            ))}
                        </div>
                    </SectionCard>

                    <SectionCard title="Opportunities">
                        <div className="grid gap-3">
                            {(opportunities.length ? opportunities : [{ title: 'No opportunities published yet', status: 'pending' }]).slice(0, 4).map((opportunity, index) => (
                                <div key={opportunity.id || index} className="rounded-md bg-slate-50 p-3">
                                    <p className="text-sm font-black">{opportunity.title}</p>
                                    <p className="text-xs uppercase tracking-wide text-[#9b6825]">{opportunity.category || 'Leadership'}</p>
                                </div>
                            ))}
                        </div>
                    </SectionCard>
                </div>

                <div className="grid gap-6 xl:grid-cols-2">
                    <SectionCard title="Leadership Feed">
                        <p className="text-sm leading-6 text-slate-600">Read articles, react, comment, save posts, share posts, and report inappropriate content as the feed grows.</p>
                    </SectionCard>
                    <SectionCard title="My Submissions">
                        <div className="flex flex-wrap gap-2">
                            {['Pending', 'Approved', 'Rejected', 'Needs revision'].map((status) => <StatusBadge key={status} status={status.toLowerCase()} />)}
                        </div>
                    </SectionCard>
                    <SectionCard title="Advocacy Campaigns">
                        <p className="text-sm leading-6 text-slate-600">View campaigns, join campaigns, support proposals, and track campaign impact.</p>
                    </SectionCard>
                    <SectionCard title="Settings">
                        <p className="text-sm leading-6 text-slate-600">Change password, notification preferences, privacy settings, and delete account requests.</p>
                    </SectionCard>
                </div>

                {isModerator && (
                    <SectionCard title="Moderator Tools">
                        <div id="moderator-tools" className="grid gap-3 md:grid-cols-2 xl:grid-cols-4">
                            {['Review reported posts', 'Approve pending comments', 'Hide inappropriate content', 'Submit moderation report'].map((tool) => (
                                <button key={tool} className="rounded-md border border-[#d7c49a] bg-[#fffaf0] px-4 py-3 text-left text-sm font-black text-[#061311]">{tool}</button>
                            ))}
                        </div>
                    </SectionCard>
                )}
            </div>
        </DashboardLayout>
    );
}
