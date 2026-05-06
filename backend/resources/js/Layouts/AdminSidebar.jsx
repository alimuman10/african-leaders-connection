import { NavLink } from 'react-router-dom';

const navItems = [
    { label: 'Overview', to: '/admin/dashboard' },
    { label: 'Member Management', to: '/admin/members' },
    { label: 'Moderator Invitations', to: '/admin/moderators' },
    { label: 'Content Management', to: '/admin/content' },
    { label: 'Announcements', to: '/admin/announcements' },
    { label: 'Events', to: '/admin/events' },
    { label: 'Opportunities', to: '/admin/opportunities' },
    { label: 'Campaigns', to: '/admin/campaigns' },
    { label: 'Reports & Moderation', to: '/admin/reports' },
    { label: 'Homepage Control', to: '/admin/homepage' },
    { label: 'Analytics', to: '/admin/analytics' },
];

export default function AdminSidebar() {
    return (
        <aside className="fixed inset-y-0 left-0 z-30 hidden w-76 border-r border-[#d7c49a] bg-[#061311] p-6 text-white lg:block">
            <a href="/" className="flex items-center gap-3">
                <div className="grid h-12 w-12 place-items-center rounded-lg bg-[#dfb15b] text-sm font-black text-[#1d1305]">ALC</div>
                <div>
                    <p className="text-sm font-black leading-tight">African Leaders Connection</p>
                    <p className="text-[0.68rem] font-bold uppercase tracking-[0.2em] text-[#dfb15b]">Leadership. Unity. Progress.</p>
                </div>
            </a>

            <nav className="mt-10 grid gap-1.5">
                {navItems.map((item) => (
                    <NavLink
                        key={item.to}
                        to={item.to}
                        end={item.to === '/admin/dashboard'}
                        className={({ isActive }) => [
                            'border-l-4 rounded-r-md px-3 py-2.5 text-sm font-bold transition',
                            isActive
                                ? 'border-[#dfb15b] bg-[#102a25] text-[#dfb15b]'
                                : 'border-transparent text-slate-200 hover:border-[#6f8f62] hover:bg-[#102a25] hover:text-white',
                        ].join(' ')}
                    >
                        {item.label}
                    </NavLink>
                ))}
            </nav>
        </aside>
    );
}
