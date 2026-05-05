import { useEffect, useState } from 'react';
import DashboardLayout from '../../Layouts/DashboardLayout';
import MetricCard from '../../Components/MetricCard';
import ResourceManager from '../../Components/ResourceManager';
import MessagesPanel from '../../Components/MessagesPanel';
import MediaManager from '../../Components/MediaManager';
import { apiRequest } from '../../lib/api';

const fallbackMetrics = {
    users: 0,
    contact_messages: 0,
    stories: 0,
    projects: 0,
    services: 0,
};

export default function AdminDashboard() {
    const [dashboard, setDashboard] = useState({ metrics: fallbackMetrics, recent_activities: [], quick_actions: [] });

    useEffect(() => {
        let active = true;
        apiRequest('/api/dashboard')
            .then((data) => {
                if (active) setDashboard(data);
            })
            .catch(() => {});

        return () => {
            active = false;
        };
    }, []);

    return (
        <DashboardLayout title="Admin Overview">
            <div className="grid gap-5 md:grid-cols-2 xl:grid-cols-5">
                <MetricCard label="Total users" value={dashboard.metrics?.users ?? 0} />
                <MetricCard label="Messages" value={dashboard.metrics?.contact_messages ?? 0} />
                <MetricCard label="Stories" value={dashboard.metrics?.stories ?? 0} />
                <MetricCard label="Projects" value={dashboard.metrics?.projects ?? 0} />
                <MetricCard label="Services" value={dashboard.metrics?.services ?? 0} />
            </div>

            <div className="mt-8 grid gap-6 xl:grid-cols-[1.4fr_1fr]">
                <section className="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 className="text-lg font-extrabold text-slate-950">Recent Activity</h3>
                    <div className="mt-4 grid gap-3">
                        {(dashboard.recent_activities?.length ? dashboard.recent_activities : [{ action: 'Platform scaffold ready for activity logs.' }]).map((activity, index) => (
                            <p key={index} className="rounded-md bg-slate-50 p-3 text-sm text-slate-600">{activity.action}</p>
                        ))}
                    </div>
                </section>

                <section className="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 className="text-lg font-extrabold text-slate-950">Quick Actions</h3>
                    <div className="mt-4 grid gap-3">
                        {['Create story', 'Add project', 'Manage services', 'Review messages'].map((action) => (
                            <button key={action} className="rounded-md bg-slate-950 px-4 py-3 text-left text-sm font-bold text-white hover:bg-slate-800">
                                {action}
                            </button>
                        ))}
                    </div>
                </section>
            </div>

            <div className="mt-8 grid gap-6">
                <ResourceManager
                    title="Stories"
                    endpoint="/api/admin/stories"
                    fields={[
                        { name: 'title', label: 'Title', required: true },
                        { name: 'excerpt', label: 'Excerpt' },
                        { name: 'body', label: 'Story body', type: 'textarea' },
                        { name: 'country', label: 'Country' },
                        { name: 'region', label: 'Region' },
                        { name: 'status', label: 'Status' },
                    ]}
                />
                <ResourceManager
                    title="Services"
                    endpoint="/api/admin/services"
                    fields={[
                        { name: 'title', label: 'Title', required: true },
                        { name: 'category', label: 'Category', required: true },
                        { name: 'summary', label: 'Summary' },
                        { name: 'description', label: 'Description', type: 'textarea' },
                    ]}
                />
                <ResourceManager
                    title="Projects"
                    endpoint="/api/admin/projects"
                    fields={[
                        { name: 'title', label: 'Title', required: true },
                        { name: 'summary', label: 'Summary' },
                        { name: 'description', label: 'Description', type: 'textarea' },
                        { name: 'status', label: 'Status' },
                        { name: 'country', label: 'Country' },
                    ]}
                />
                <ResourceManager
                    title="Advocacy"
                    endpoint="/api/admin/advocacy"
                    fields={[
                        { name: 'title', label: 'Title', required: true },
                        { name: 'summary', label: 'Summary' },
                        { name: 'content', label: 'Content', type: 'textarea' },
                    ]}
                />
                <MessagesPanel />
                <MediaManager />
                <ResourceManager
                    title="Users"
                    endpoint="/api/users"
                    fields={[
                        { name: 'name', label: 'Name', required: true },
                        { name: 'email', label: 'Email', required: true },
                        { name: 'status', label: 'Status' },
                    ]}
                />
                <ResourceManager
                    title="Settings"
                    endpoint="/api/settings"
                    fields={[
                        { name: 'key', label: 'Key', required: true },
                        { name: 'value', label: 'Value' },
                        { name: 'group', label: 'Group' },
                    ]}
                />
            </div>
        </DashboardLayout>
    );
}
