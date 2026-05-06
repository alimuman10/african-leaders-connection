import { useMemo, useState } from 'react';
import DashboardLayout from '../../Layouts/DashboardLayout';
import MetricCard from '../../Components/MetricCard';
import SectionCard from '../../Components/SectionCard';
import StatusBadge from '../../Components/StatusBadge';
import { useApiMutation, useApiQuery, useCollectionQuery } from '../../lib/query';

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
    const { data: userPayload, isLoading: userLoading } = useApiQuery(['current-user'], '/api/user');
    const { data: dashboard = null, isLoading: dashboardLoading } = useApiQuery(['member-dashboard'], '/api/member/dashboard');
    const { data: events = [] } = useCollectionQuery(['member-events'], '/api/member/events');
    const { data: opportunities = [] } = useCollectionQuery(['member-opportunities'], '/api/member/opportunities');
    const { data: notifications = [] } = useCollectionQuery(['member-notifications'], '/api/member/notifications');
    const mutation = useApiMutation({ invalidate: [['member-dashboard'], ['current-user'], ['member-events']] });
    const user = userPayload?.data || userPayload || null;
    const loading = userLoading || dashboardLoading;
    const [profileMessage, setProfileMessage] = useState('');
    const [submissionMessage, setSubmissionMessage] = useState('');

    const isModerator = useMemo(() => Boolean(user?.is_moderator || user?.roles?.includes?.('Moderator') || dashboard?.moderator?.enabled), [user, dashboard]);
    const navItems = isModerator ? [...nav, { label: 'Moderator Tools', href: '#moderator-tools' }] : nav;

    async function updateProfile(event) {
        event.preventDefault();
        const form = new FormData(event.currentTarget);
        const body = Object.fromEntries(form.entries());

        await mutation.mutateAsync({ endpoint: '/api/member/profile', method: 'PUT', body });
        setProfileMessage('Profile updated successfully.');
    }

    async function submitLeadershipItem(event) {
        event.preventDefault();
        const form = new FormData(event.currentTarget);
        const body = Object.fromEntries(form.entries());

        await mutation.mutateAsync({ endpoint: '/api/member/submissions', method: 'POST', body });
        event.currentTarget.reset();
        setSubmissionMessage('Submission sent for review.');
    }

    async function registerForEvent(eventId) {
        await mutation.mutateAsync({ endpoint: `/api/member/events/${eventId}/register` });
    }

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
                        {profileMessage && <p className="mb-3 rounded-md bg-emerald-50 p-3 text-sm font-bold text-emerald-700">{profileMessage}</p>}
                        <form onSubmit={updateProfile} className="grid gap-3 text-sm">
                            <input name="name" defaultValue={user?.name || ''} placeholder="Full name" className="rounded-md border border-slate-300 px-3 py-2" />
                            <input name="country" defaultValue={user?.country || user?.profile?.country || ''} placeholder="Country" className="rounded-md border border-slate-300 px-3 py-2" />
                            <input name="city" defaultValue={user?.profile?.city || ''} placeholder="City" className="rounded-md border border-slate-300 px-3 py-2" />
                            <input name="organization" defaultValue={user?.organization || user?.profile?.organization || ''} placeholder="Organization" className="rounded-md border border-slate-300 px-3 py-2" />
                            <input name="professional_title" defaultValue={user?.profile?.professional_title || ''} placeholder="Professional title" className="rounded-md border border-slate-300 px-3 py-2" />
                            <input name="leadership_category" defaultValue={user?.profile?.leadership_category || ''} placeholder="Leadership category" className="rounded-md border border-slate-300 px-3 py-2" />
                            <textarea name="bio" defaultValue={user?.profile?.bio || ''} placeholder="Leadership biography" className="min-h-24 rounded-md border border-slate-300 px-3 py-2" />
                            <button disabled={mutation.isPending} className="rounded-md bg-[#102a25] px-4 py-3 text-sm font-black text-white">Save Profile</button>
                        </form>
                    </SectionCard>

                    <SectionCard title="Events">
                        <div className="grid gap-3">
                            {(events.length ? events : [{ title: 'No events published yet', status: 'pending' }]).slice(0, 4).map((event, index) => (
                                <div key={event.id || index} className="grid gap-2 rounded-md bg-slate-50 p-3">
                                    <div className="flex items-center justify-between gap-3">
                                        <span className="text-sm font-bold">{event.title}</span>
                                        <StatusBadge status={event.status || 'pending'} />
                                    </div>
                                    {event.id && <button onClick={() => registerForEvent(event.id)} className="rounded-md border border-[#d7c49a] px-3 py-2 text-xs font-black">Register</button>}
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
                        {submissionMessage && <p className="mb-3 rounded-md bg-emerald-50 p-3 text-sm font-bold text-emerald-700">{submissionMessage}</p>}
                        <form onSubmit={submitLeadershipItem} className="mb-4 grid gap-3">
                            <select name="type" className="rounded-md border border-slate-300 px-3 py-2 text-sm">
                                <option value="story">Leadership story</option>
                                <option value="project">Community project</option>
                                <option value="campaign">Advocacy campaign proposal</option>
                            </select>
                            <input name="title" required placeholder="Submission title" className="rounded-md border border-slate-300 px-3 py-2 text-sm" />
                            <input name="country" placeholder="Country" className="rounded-md border border-slate-300 px-3 py-2 text-sm" />
                            <textarea name="summary" placeholder="Short summary" className="min-h-20 rounded-md border border-slate-300 px-3 py-2 text-sm" />
                            <textarea name="body" placeholder="Details" className="min-h-28 rounded-md border border-slate-300 px-3 py-2 text-sm" />
                            <button disabled={mutation.isPending} className="rounded-md bg-[#102a25] px-4 py-3 text-sm font-black text-white">Submit for Review</button>
                        </form>
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
                    <SectionCard title="Notifications">
                        <div className="grid gap-2">
                            {(notifications.length ? notifications : [{ data: { title: 'No notifications yet' } }]).slice(0, 5).map((notification, index) => (
                                <div key={notification.id || index} className="rounded-md bg-slate-50 p-3 text-sm font-bold">
                                    {notification.data?.title || notification.data?.message || 'Platform notification'}
                                </div>
                            ))}
                        </div>
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
