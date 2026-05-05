import { useEffect, useMemo, useState } from 'react';
import DashboardLayout from '../../Layouts/DashboardLayout';
import { apiRequest, collectionFrom } from '../../lib/api';

export default function MemberDashboard() {
    const [user, setUser] = useState(null);
    const [resources, setResources] = useState([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        Promise.all([
            apiRequest('/api/user').catch(() => null),
            apiRequest('/api/resources').catch(() => ({ data: [] })),
        ]).then(([userPayload, resourcePayload]) => {
            setUser(userPayload?.data || null);
            setResources(collectionFrom(resourcePayload));
        }).finally(() => setLoading(false));
    }, []);

    const completion = useMemo(() => {
        if (!user) return 0;
        const fields = ['name', 'email', 'phone', 'country', 'profession', 'organization', 'leadership_interest'];
        const filled = fields.filter((field) => Boolean(user[field] || user.profile?.[field])).length;
        return Math.round((filled / fields.length) * 100);
    }, [user]);

    return (
        <DashboardLayout title="Member Dashboard">
            {loading ? (
                <p className="text-sm text-slate-500">Loading your member workspace...</p>
            ) : (
                <div className="grid gap-6">
                    <section className="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
                        <p className="text-sm font-bold uppercase tracking-[0.18em] text-amber-600">Welcome</p>
                        <h3 className="mt-2 text-2xl font-black text-slate-950">{user?.name || 'African Leaders Connection Member'}</h3>
                        <p className="mt-2 text-slate-600">Your profile is {completion}% complete. Keep it updated so the platform can connect you with relevant leadership resources and opportunities.</p>
                        <a href="/member/profile" className="mt-4 inline-flex rounded-md bg-slate-950 px-4 py-3 text-sm font-black text-white">Update profile</a>
                    </section>

                    <div className="grid gap-6 lg:grid-cols-3">
                        {[
                            ['Leadership resources', resources.length ? `${resources.length} resources available` : 'No resources published yet'],
                            ['Submit a story', 'Share an impact story for editorial review'],
                            ['Community participation', 'Join initiatives and leadership conversations'],
                        ].map(([title, text]) => (
                            <article key={title} className="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
                                <h3 className="text-lg font-extrabold text-slate-950">{title}</h3>
                                <p className="mt-3 text-sm leading-6 text-slate-600">{text}</p>
                            </article>
                        ))}
                    </div>
                </div>
            )}
        </DashboardLayout>
    );
}
