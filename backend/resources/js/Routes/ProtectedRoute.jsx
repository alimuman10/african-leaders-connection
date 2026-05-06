import { Navigate } from 'react-router-dom';
import { useApiQuery } from '../lib/query';

export default function ProtectedRoute({ children }) {
    const token = sessionStorage.getItem('alc_token') || localStorage.getItem('alc_token');
    const { data: userPayload, isLoading, isError } = useApiQuery(['current-user'], '/api/user', {
        enabled: Boolean(token),
    });

    if (!token) return <Navigate to="/login" replace />;

    if (isLoading) {
        return (
            <main className="grid min-h-screen place-items-center bg-[#f7f2e8] text-sm font-black text-[#061311]">
                Loading secure workspace...
            </main>
        );
    }

    if (isError) {
        localStorage.removeItem('alc_token');
        sessionStorage.removeItem('alc_token');

        return <Navigate to="/login" replace />;
    }

    return children(userPayload?.data || userPayload);
}
