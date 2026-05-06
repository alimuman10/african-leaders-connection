import React from 'react';
import { createRoot } from 'react-dom/client';
import { QueryClient, QueryClientProvider } from '@tanstack/react-query';
import { BrowserRouter, Navigate, Route, Routes } from 'react-router-dom';
import AdminDashboard from './Pages/Admin/AdminDashboard';
import MemberDashboard from './Pages/Member/MemberDashboard';
import Login from './Pages/Login';
import Register from './Pages/Register';
import ForgotPassword from './Pages/ForgotPassword';
import ResetPassword from './Pages/ResetPassword';
import VerifyEmail from './Pages/VerifyEmail';
import Profile from './Pages/Profile';
import { useApiQuery } from './lib/query';

const root = document.getElementById('root');
const queryClient = new QueryClient();

function App() {
    return (
        <QueryClientProvider client={queryClient}>
            <BrowserRouter>
                <Routes>
                    <Route path="/login" element={<Login />} />
                    <Route path="/register" element={<Register />} />
                    <Route path="/forgot-password" element={<ForgotPassword />} />
                    <Route path="/reset-password" element={<ResetPassword />} />
                    <Route path="/verify-email" element={<VerifyEmail />} />
                    <Route path="/profile" element={<Profile />} />
                    <Route path="/dashboard" element={<Navigate to="/member/dashboard" replace />} />
                    <Route path="/member/*" element={<ProtectedRoute><MemberDashboard /></ProtectedRoute>} />
                    <Route path="/admin/*" element={<ProtectedRoute requireSuperAdmin><AdminDashboard /></ProtectedRoute>} />
                    <Route path="*" element={<Navigate to="/member/dashboard" replace />} />
                </Routes>
            </BrowserRouter>
        </QueryClientProvider>
    );
}

function ProtectedRoute({ children, requireSuperAdmin = false }) {
    const token = sessionStorage.getItem('alc_token') || localStorage.getItem('alc_token');
    const { data: userPayload, isLoading, isError } = useApiQuery(['current-user'], '/api/user', {
        enabled: Boolean(token),
    });

    if (!token) return <Navigate to="/login" replace />;
    if (isLoading) return <main className="grid min-h-screen place-items-center bg-[#f7f2e8] text-sm font-black text-[#061311]">Loading secure workspace...</main>;
    if (isError) {
        localStorage.removeItem('alc_token');
        sessionStorage.removeItem('alc_token');
        return <Navigate to="/login" replace />;
    }

    const user = userPayload?.data || userPayload;

    if (requireSuperAdmin && !user?.is_super_admin) {
        return <Navigate to="/member/dashboard" replace />;
    }

    return children;
}

if (root) {
    createRoot(root).render(<App />);
}
