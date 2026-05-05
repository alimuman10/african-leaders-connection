const navItems = ['Dashboard', 'Stories', 'Projects', 'Services', 'Advocacy', 'Messages', 'Media', 'Users'];

export default function DashboardLayout({ title, children }) {
    return (
        <div className="min-h-screen bg-slate-50">
            <aside className="fixed inset-y-0 left-0 hidden w-72 border-r border-slate-200 bg-slate-950 p-6 text-white lg:block">
                <div>
                    <p className="text-xs font-bold uppercase tracking-[0.22em] text-amber-300">African Leaders Connection</p>
                    <h1 className="mt-3 text-2xl font-black leading-tight">Leadership. Unity. Progress.</h1>
                </div>
                <nav className="mt-10 grid gap-2">
                    {navItems.map((item) => (
                        <a key={item} href="#" className="rounded-md px-3 py-2 text-sm font-semibold text-slate-200 hover:bg-white/10 hover:text-amber-200">
                            {item}
                        </a>
                    ))}
                </nav>
            </aside>

            <main className="lg:pl-72">
                <header className="border-b border-slate-200 bg-white px-6 py-5">
                    <p className="text-sm font-semibold uppercase tracking-[0.18em] text-amber-600">Platform Dashboard</p>
                    <h2 className="mt-1 text-2xl font-extrabold text-slate-950">{title}</h2>
                </header>
                <section className="p-6">{children}</section>
            </main>
        </div>
    );
}
