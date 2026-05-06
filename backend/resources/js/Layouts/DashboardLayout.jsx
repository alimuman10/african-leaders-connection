const brand = 'African Leaders Connection';

export default function DashboardLayout({ title, eyebrow = 'Platform Dashboard', navItems = [], user = null, children }) {
    return (
        <div className="min-h-screen bg-[#f7f2e8] text-[#111827]">
            <aside className="fixed inset-y-0 left-0 z-30 hidden w-76 border-r border-[#d7c49a] bg-[#061311] p-6 text-white lg:block">
                <a href="/" className="flex items-center gap-3">
                    <div className="grid h-12 w-12 place-items-center rounded-lg bg-[#dfb15b] text-sm font-black text-[#1d1305]">ALC</div>
                    <div>
                        <p className="text-sm font-black leading-tight">{brand}</p>
                        <p className="text-[0.68rem] font-bold uppercase tracking-[0.2em] text-[#dfb15b]">Leadership. Unity. Progress.</p>
                    </div>
                </a>

                <nav className="mt-10 grid gap-1.5">
                    {navItems.map((item) => (
                        <a
                            key={item.label}
                            href={item.href || '#'}
                            className="rounded-md px-3 py-2.5 text-sm font-bold text-slate-200 transition hover:bg-[#102a25] hover:text-[#dfb15b]"
                        >
                            {item.label}
                        </a>
                    ))}
                </nav>
            </aside>

            <main className="lg:pl-76">
                <header className="sticky top-0 z-20 border-b border-[#e2d5b8] bg-[#fffaf0]/95 px-4 py-4 backdrop-blur-none sm:px-6">
                    <div className="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                        <div>
                            <p className="text-xs font-black uppercase tracking-[0.18em] text-[#9b6825]">{eyebrow}</p>
                            <h1 className="mt-1 text-2xl font-black tracking-tight text-[#061311] sm:text-3xl">{title}</h1>
                        </div>
                        <div className="flex flex-wrap items-center gap-3">
                            {user && <span className="rounded-md border border-[#d7c49a] bg-white px-3 py-2 text-sm font-bold text-[#10211e]">{user.name}</span>}
                            <a className="rounded-md bg-[#061311] px-4 py-2 text-sm font-black text-white" href="/">Public Site</a>
                        </div>
                    </div>
                </header>

                <section className="p-4 sm:p-6 lg:p-8">{children}</section>
            </main>
        </div>
    );
}
