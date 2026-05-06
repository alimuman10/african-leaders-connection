import { Outlet, useNavigate } from 'react-router-dom';
import { useQueryClient } from '@tanstack/react-query';
import AdminSidebar from './AdminSidebar';
import { apiRequest } from '../lib/api';

export default function AdminLayout({ user }) {
    const navigate = useNavigate();
    const queryClient = useQueryClient();

    async function logout() {
        try {
            await apiRequest('/api/logout', { method: 'POST' });
        } catch {
            // Clear local auth state even if the server token is already invalid.
        }

        localStorage.removeItem('alc_token');
        sessionStorage.removeItem('alc_token');
        queryClient.clear();
        navigate('/login', { replace: true });
    }

    return (
        <div className="min-h-screen bg-[#f7f2e8] text-[#111827]">
            <AdminSidebar />

            <main className="lg:pl-76">
                <header className="sticky top-0 z-20 border-b border-[#e2d5b8] bg-[#fffaf0]/95 px-4 py-4 sm:px-6">
                    <div className="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                        <div>
                            <p className="text-xs font-black uppercase tracking-[0.18em] text-[#9b6825]">Super Admin Workspace</p>
                            <h1 className="mt-1 text-2xl font-black tracking-tight text-[#061311] sm:text-3xl">African Leaders Connection</h1>
                        </div>
                        <div className="flex flex-wrap items-center gap-3">
                            <span className="rounded-md border border-[#d7c49a] bg-white px-3 py-2 text-sm font-bold text-[#10211e]">
                                {user?.name || 'Super Admin'}
                            </span>
                            <a className="rounded-md bg-[#061311] px-4 py-2 text-sm font-black text-white" href="/">Public Site</a>
                            <button onClick={logout} className="rounded-md border border-[#061311] px-4 py-2 text-sm font-black text-[#061311]">Logout</button>
                        </div>
                    </div>
                </header>

                <section className="p-4 sm:p-6 lg:p-8">
                    <Outlet />
                </section>
            </main>
        </div>
    );
}
