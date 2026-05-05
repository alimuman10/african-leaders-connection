import './bootstrap';
import React from 'react';
import { createRoot } from 'react-dom/client';
import AdminDashboard from './Pages/Admin/AdminDashboard';
import MemberDashboard from './Pages/Member/MemberDashboard';
import Login from './Pages/Login';
import Register from './Pages/Register';
import ForgotPassword from './Pages/ForgotPassword';
import ResetPassword from './Pages/ResetPassword';
import VerifyEmail from './Pages/VerifyEmail';
import Profile from './Pages/Profile';

const root = document.getElementById('root');

function App() {
    const path = window.location.pathname;

    if (path === '/login') return <Login />;
    if (path === '/register') return <Register />;
    if (path === '/forgot-password') return <ForgotPassword />;
    if (path === '/reset-password') return <ResetPassword />;
    if (path === '/verify-email') return <VerifyEmail />;
    if (path.endsWith('/profile')) return <Profile />;

    if (path.startsWith('/member')) {
        return <MemberDashboard />;
    }

    return <AdminDashboard />;
}

if (root) {
    createRoot(root).render(<App />);
}
