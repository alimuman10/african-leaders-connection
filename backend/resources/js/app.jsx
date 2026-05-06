import React from 'react';
import { createRoot } from 'react-dom/client';
import { QueryClient, QueryClientProvider } from '@tanstack/react-query';
import { BrowserRouter, Navigate, Route, Routes } from 'react-router-dom';
import AdminLayout from './Layouts/AdminLayout';
import AdminRoute from './Routes/AdminRoute';
import ProtectedRoute from './Routes/ProtectedRoute';
import AdminOverview from './Pages/Admin/AdminOverview';
import MemberManagement from './Pages/Admin/MemberManagement';
import ModeratorInvitations from './Pages/Admin/ModeratorInvitations';
import ContentManagement from './Pages/Admin/ContentManagement';
import AnnouncementManagement from './Pages/Admin/AnnouncementManagement';
import EventManagement from './Pages/Admin/EventManagement';
import OpportunityManagement from './Pages/Admin/OpportunityManagement';
import CampaignManagement from './Pages/Admin/CampaignManagement';
import ReportsModeration from './Pages/Admin/ReportsModeration';
import HomepageControl from './Pages/Admin/HomepageControl';
import Analytics from './Pages/Admin/Analytics';
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
                    <Route path="/member/*" element={<ProtectedRoute>{() => <MemberDashboard />}</ProtectedRoute>} />
                    <Route path="/admin" element={<Navigate to="/admin/dashboard" replace />} />
                    <Route
                        path="/admin/*"
                        element={<AdminRoute>{(user) => <AdminLayout user={user} />}</AdminRoute>}
                    >
                        <Route path="dashboard" element={<AdminOverview />} />
                        <Route path="members" element={<MemberManagement />} />
                        <Route path="moderators" element={<ModeratorInvitations />} />
                        <Route path="content" element={<ContentManagement />} />
                        <Route path="announcements" element={<AnnouncementManagement />} />
                        <Route path="events" element={<EventManagement />} />
                        <Route path="opportunities" element={<OpportunityManagement />} />
                        <Route path="campaigns" element={<CampaignManagement />} />
                        <Route path="reports" element={<ReportsModeration />} />
                        <Route path="homepage" element={<HomepageControl />} />
                        <Route path="analytics" element={<Analytics />} />
                        <Route path="*" element={<Navigate to="/admin/dashboard" replace />} />
                    </Route>
                    <Route path="*" element={<Navigate to="/member/dashboard" replace />} />
                </Routes>
            </BrowserRouter>
        </QueryClientProvider>
    );
}

if (root) {
    createRoot(root).render(<App />);
}
