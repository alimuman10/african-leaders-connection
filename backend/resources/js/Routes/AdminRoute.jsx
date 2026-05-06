import { Navigate } from 'react-router-dom';
import ProtectedRoute from './ProtectedRoute';

export default function AdminRoute({ children }) {
    return (
        <ProtectedRoute>
            {(user) => (isSuperAdmin(user) ? children(user) : <Navigate to="/member/dashboard" replace />)}
        </ProtectedRoute>
    );
}

function isSuperAdmin(user) {
    const roles = user?.roles || [];

    return Boolean(
        user?.is_super_admin
        || user?.role === 'Super Admin'
        || roles.includes?.('Super Admin')
        || roles.some?.((role) => role?.name === 'Super Admin')
    );
}
