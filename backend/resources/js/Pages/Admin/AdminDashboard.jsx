import { useEffect, useState } from 'react';
import DashboardLayout from '../../Layouts/DashboardLayout';
import MetricCard from '../../Components/MetricCard';
import SectionCard from '../../Components/SectionCard';
import StatusBadge from '../../Components/StatusBadge';
import { apiRequest, collectionFrom } from '../../lib/api';

const navItems = [
    'Overview',
    'Member Management',
    'Moderator Invitations',
    'Content Management',
    'Announcements',
    'Events',
    'Opportunities',
    'Campaigns',
    'Reports & Moderation',
    'Homepage Control',
    'Analytics',
    'System Settings',
    'Security & Audit Logs',
].map((label) => ({ label, href: `#${label.toLowerCase().replaceAll(' ', '-').replaceAll('&', 'and')}` }));

export default function AdminDashboard() {
    const [dashboard, setDashboard] = useState(null);
    const [members, setMembers] = useState([]);
    const [reports, setReports] = useState([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        Promise.all([
            apiRequest('/api/admin/dashboard').catch(() => null),
            apiRequest('/api/admin/members').catch(() => ({ data: [] })),
            apiRequest('/api/admin/reports').catch(() => ({ data: [] })),
        ]).then(([dashboardPayload, memberPayload, reportPayload]) => {
            setDashboard(dashboardPayload || null);
            setMembers(collectionFrom(memberPayload));
            setReports(collectionFrom(reportPayload));
        }).finally(() => setLoading(false));
    }, []);

    const metrics = dashboard?.metrics || {};

    return (
        <DashboardLayout title="Super Admin Dashboard" eyebrow="Full Platform Control" navItems={navItems}>
            <div className="grid gap-6">
                <section id="overview" className="rounded-lg bg-[#061311] p-6 text-white">
                    <p className="text-xs font-black uppercase tracking-[0.2em] text-[#dfb15b]">Command Center</p>
                    <h2 className="mt-2 text-3xl font-black">African Leaders Connection Super Admin</h2>
                    <p className="mt-3 max-w-3xl text-slate-200">
                        Manage members, moderators, content, events, opportunities, campaigns, homepage controls, analytics, settings, and security audit logs.
                    </p>
                </section>

                <div className="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                    <MetricCard label="Total Members" value={metrics.total_members ?? 0} />
                    <MetricCard label="Active Members" value={metrics.active_members ?? 0} />
                    <MetricCard label="Moderators" value={metrics.total_moderators ?? 0} />
                    <MetricCard label="Reported Content" value={metrics.reported_content ?? 0} />
                    <MetricCard label="Suspended" value={metrics.suspended_members ?? 0} />
                    <MetricCard label="Pending Submissions" value={metrics.pending_submissions ?? 0} />
                    <MetricCard label="Upcoming Events" value={metrics.upcoming_events ?? 0} />
                    <MetricCard label="Growth Records" value={dashboard?.growth?.length ?? 0} />
                </div>

                <div className="grid gap-6 xl:grid-cols-[1.35fr_0.95fr]">
                    <SectionCard title="Member Management">
                        <div className="overflow-x-auto">
                            <table className="w-full min-w-[640px] text-left text-sm">
                                <thead className="border-b border-[#e2d5b8] text-xs uppercase tracking-wide text-[#9b6825]">
                                    <tr><th className="py-2">Name</th><th>Email</th><th>Country</th><th>Status</th><th>Roles</th></tr>
                                </thead>
                                <tbody>
                                    {(members.length ? members : [{ name: 'No members loaded', email: '-', status: 'pending', roles: [] }]).slice(0, 8).map((member, index) => (
                                        <tr key={member.id || index} className="border-b border-slate-100">
                                            <td className="py-3 font-bold">{member.name}</td>
                                            <td>{member.email}</td>
                                            <td>{member.country || '-'}</td>
                                            <td><StatusBadge status={member.status || 'pending'} /></td>
                                            <td>{member.roles?.map?.((role) => role.name || role)?.join(', ') || 'Member'}</td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    </SectionCard>

                    <SectionCard title="Reports & Moderation">
                        <div className="grid gap-3">
                            {(reports.length ? reports : [{ reason: 'No open reports', status: 'active' }]).slice(0, 6).map((report, index) => (
                                <div key={report.id || index} className="flex items-center justify-between gap-3 rounded-md bg-[#fffaf0] p-3">
                                    <span className="text-sm font-bold">{report.reason}</span>
                                    <StatusBadge status={report.status || 'open'} />
                                </div>
                            ))}
                        </div>
                    </SectionCard>
                </div>

                <div className="grid gap-6 xl:grid-cols-3">
                    <AdminModule title="Moderator Invitation Management" items={['Invite a member', 'Track invitation status', 'Revoke moderator role', 'View moderator reports']} />
                    <AdminModule title="Content Management" items={['Leadership articles', 'Platform news', 'Member submissions', 'Homepage features']} />
                    <AdminModule title="Announcements" items={['All-member announcements', 'Country targeting', 'Scheduled notices', 'Archive history']} />
                    <AdminModule title="Event Management" items={['Create events', 'Manage registrations', 'Upload resources', 'Send reminders']} />
                    <AdminModule title="Opportunity Management" items={['Scholarships', 'Fellowships', 'Grants', 'Jobs and internships']} />
                    <AdminModule title="Advocacy Campaign Management" items={['Create campaigns', 'Approve proposals', 'Track supporters', 'Impact reports']} />
                    <AdminModule title="Homepage & Website Control" items={['Hero text', 'Featured stories', 'CTA sections', 'Public pages']} />
                    <AdminModule title="Analytics" items={['Member growth', 'Engagement', 'Country participation', 'Top contributors']} />
                    <AdminModule title="System Settings" items={['Brand colors', 'SEO settings', 'Email settings', 'Privacy and terms']} />
                    <AdminModule title="Security & Audit Logs" items={['Login history', 'Role changes', 'Content changes', 'Suspicious activity']} />
                </div>
            </div>
        </DashboardLayout>
    );
}

function AdminModule({ title, items }) {
    return (
        <SectionCard title={title}>
            <div className="grid gap-2">
                {items.map((item) => (
                    <button key={item} className="rounded-md border border-[#e2d5b8] bg-white px-3 py-2 text-left text-sm font-bold text-[#10211e] hover:bg-[#fffaf0]">
                        {item}
                    </button>
                ))}
            </div>
        </SectionCard>
    );
}
