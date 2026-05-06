import './bootstrap';
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
                    <Route path="/member/*" element={<MemberDashboard />} />
                    <Route path="/admin/*" element={<AdminDashboard />} />
                    <Route path="*" element={<Navigate to="/member/dashboard" replace />} />
                </Routes>
            </BrowserRouter>
        </QueryClientProvider>
    );
}

if (root) {
    createRoot(root).render(<App />);
}
