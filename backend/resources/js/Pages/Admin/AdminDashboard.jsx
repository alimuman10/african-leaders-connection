import DashboardLayout from '../../Layouts/DashboardLayout';
import MetricCard from '../../Components/MetricCard';
import ResourceManager from '../../Components/ResourceManager';
import SectionCard from '../../Components/SectionCard';
import StatusBadge from '../../Components/StatusBadge';
import { useApiMutation, useApiQuery, useCollectionQuery } from '../../lib/query';

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
    const { data: dashboard = null } = useApiQuery(['admin-dashboard'], '/api/admin/dashboard');
    const { data: members = [] } = useCollectionQuery(['admin-members'], '/api/admin/members');
    const { data: reports = [] } = useCollectionQuery(['admin-reports'], '/api/admin/reports');
    const statusMutation = useApiMutation({ invalidate: [['admin-members'], ['admin-dashboard']] });

    const metrics = dashboard?.metrics || {};

    async function changeStatus(member, status) {
        await statusMutation.mutateAsync({
            endpoint: `/api/admin/members/${member.id}/status`,
            method: 'PUT',
            body: { status },
        });
    }

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
                                    <tr><th className="py-2">Name</th><th>Email</th><th>Country</th><th>Status</th><th>Roles</th><th>Action</th></tr>
                                </thead>
                                <tbody>
                                    {(members.length ? members : [{ name: 'No members loaded', email: '-', status: 'pending', roles: [] }]).slice(0, 8).map((member, index) => (
                                        <tr key={member.id || index} className="border-b border-slate-100">
                                            <td className="py-3 font-bold">{member.name}</td>
                                            <td>{member.email}</td>
                                            <td>{member.country || '-'}</td>
                                            <td><StatusBadge status={member.status || 'pending'} /></td>
                                            <td>{member.roles?.map?.((role) => role.name || role)?.join(', ') || 'Member'}</td>
                                            <td>
                                                {member.id && (
                                                    <select
                                                        value={member.status || 'active'}
                                                        disabled={statusMutation.isPending}
                                                        onChange={(event) => changeStatus(member, event.target.value)}
                                                        className="rounded-md border border-slate-300 px-2 py-1 text-xs font-bold"
                                                    >
                                                        <option value="active">Active</option>
                                                        <option value="suspended">Suspended</option>
                                                        <option value="banned">Banned</option>
                                                        <option value="pending verification">Pending verification</option>
                                                    </select>
                                                )}
                                            </td>
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

                <div id="content-management" className="grid gap-6">
                    <ResourceManager title="Leadership Articles" endpoint="/api/admin/posts" fields={postFields} />
                    <ResourceManager title="Event Management" endpoint="/api/admin/events" fields={eventFields} />
                    <ResourceManager title="Opportunity Management" endpoint="/api/admin/opportunities" fields={opportunityFields} />
                    <ResourceManager title="Advocacy Campaign Management" endpoint="/api/admin/campaigns" fields={campaignFields} />
                    <ResourceManager title="Announcement Management" endpoint="/api/admin/announcements" fields={announcementFields} />
                    <ResourceManager title="Homepage Control" endpoint="/api/admin/homepage" fields={homepageFields} />
                    <ResourceManager title="System Settings" endpoint="/api/admin/settings" fields={settingFields} />
                </div>

                <div className="grid gap-6 xl:grid-cols-3">
                    <AdminModule title="Moderator Invitation Management" items={['Invite selected members', 'Track invitation status', 'Revoke moderator role', 'Review moderator reports']} />
                    <AdminModule title="Analytics" items={['Member growth', 'Engagement', 'Country participation', 'Top contributors']} />
                    <AdminModule title="Security & Audit Logs" items={['Login history', 'Role changes', 'Content changes', 'Suspicious activity']} />
                </div>
            </div>
        </DashboardLayout>
    );
}

const statusOptions = ['draft', 'published', 'active', 'scheduled', 'expired', 'archived'];
const postFields = [
    { name: 'title', label: 'Title', required: true },
    { name: 'excerpt', label: 'Excerpt' },
    { name: 'body', label: 'Body', type: 'textarea', required: true },
    { name: 'category', label: 'Category' },
    { name: 'status', label: 'Status', type: 'select', options: statusOptions },
];
const eventFields = [
    { name: 'title', label: 'Title', required: true },
    { name: 'summary', label: 'Summary' },
    { name: 'description', label: 'Description', type: 'textarea' },
    { name: 'location', label: 'Location' },
    { name: 'starts_at', label: 'Start date/time', type: 'datetime-local' },
    { name: 'status', label: 'Status', type: 'select', options: ['scheduled', 'draft', 'completed', 'cancelled'] },
];
const opportunityFields = [
    { name: 'title', label: 'Title', required: true },
    { name: 'category', label: 'Category', required: true },
    { name: 'country', label: 'Country' },
    { name: 'eligibility', label: 'Eligibility' },
    { name: 'summary', label: 'Summary', type: 'textarea' },
    { name: 'external_url', label: 'External URL', type: 'url' },
    { name: 'deadline_at', label: 'Deadline', type: 'datetime-local' },
    { name: 'status', label: 'Status', type: 'select', options: ['active', 'expired', 'draft', 'featured'] },
];
const campaignFields = [
    { name: 'title', label: 'Title', required: true },
    { name: 'country', label: 'Country' },
    { name: 'summary', label: 'Summary' },
    { name: 'description', label: 'Description', type: 'textarea' },
    { name: 'status', label: 'Status', type: 'select', options: ['active', 'draft', 'closed', 'pending'] },
];
const announcementFields = [
    { name: 'title', label: 'Title', required: true },
    { name: 'body', label: 'Body', type: 'textarea', required: true },
    { name: 'audience', label: 'Audience', type: 'select', options: ['all', 'members', 'moderators'] },
    { name: 'country', label: 'Country' },
    { name: 'category', label: 'Category' },
];
const homepageFields = [
    { name: 'key', label: 'Section Key', required: true },
    { name: 'title', label: 'Title', required: true },
    { name: 'subtitle', label: 'Subtitle' },
    { name: 'content', label: 'Content', type: 'textarea' },
    { name: 'sort_order', label: 'Sort order', type: 'number' },
];
const settingFields = [
    { name: 'key', label: 'Setting Key', required: true },
    { name: 'value', label: 'Value', required: true },
    { name: 'group', label: 'Group' },
];

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
