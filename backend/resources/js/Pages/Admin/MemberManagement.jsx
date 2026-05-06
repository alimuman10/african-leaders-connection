import { useState } from 'react';
import { useApiMutation, useCollectionQuery } from '../../lib/query';
import { AdminTable, ErrorState, LoadingState, PageHeader, SectionCard, StatusBadge } from './adminPageComponents';

export default function MemberManagement() {
    const [filters, setFilters] = useState({ search: '', status: '', country: '', role: '' });
    const query = new URLSearchParams(Object.entries(filters).filter(([, value]) => value)).toString();
    const endpoint = `/api/admin/members${query ? `?${query}` : ''}`;
    const { data: members = [], isLoading, isError } = useCollectionQuery(['admin-members', filters], endpoint);
    const mutation = useApiMutation({ invalidate: [['admin-members'], ['admin-dashboard']] });

    async function setStatus(member, status) {
        await mutation.mutateAsync({
            endpoint: `/api/admin/members/${member.id}/status`,
            method: 'PUT',
            body: { status },
        });
    }

    return (
        <div className="grid gap-6">
            <PageHeader title="Member Management" description="Search, filter, review, suspend, ban, and restore members across the platform." />
            <SectionCard title="Search & Filters">
                <div className="grid gap-3 md:grid-cols-4">
                    <input placeholder="Search name or email" value={filters.search} onChange={(event) => setFilters({ ...filters, search: event.target.value })} className="rounded-md border border-slate-300 px-3 py-2 text-sm" />
                    <input placeholder="Country" value={filters.country} onChange={(event) => setFilters({ ...filters, country: event.target.value })} className="rounded-md border border-slate-300 px-3 py-2 text-sm" />
                    <select value={filters.status} onChange={(event) => setFilters({ ...filters, status: event.target.value })} className="rounded-md border border-slate-300 px-3 py-2 text-sm">
                        <option value="">All statuses</option>
                        <option value="active">Active</option>
                        <option value="suspended">Suspended</option>
                        <option value="banned">Banned</option>
                    </select>
                    <select value={filters.role} onChange={(event) => setFilters({ ...filters, role: event.target.value })} className="rounded-md border border-slate-300 px-3 py-2 text-sm">
                        <option value="">All roles</option>
                        <option value="Member">Member</option>
                        <option value="Moderator">Moderator</option>
                    </select>
                </div>
            </SectionCard>
            {isLoading && <LoadingState />}
            {isError && <ErrorState />}
            {!isLoading && (
                <AdminTable
                    rows={members}
                    emptyTitle="No members found"
                    emptyDescription="Members matching your filters will appear here."
                    columns={[
                        { key: 'name', label: 'Name' },
                        { key: 'email', label: 'Email' },
                        { key: 'country', label: 'Country' },
                        { key: 'status', label: 'Status', render: (row) => <StatusBadge status={row.status || 'pending'} /> },
                        { key: 'roles', label: 'Roles', render: (row) => row.roles?.map?.((role) => role.name || role)?.join(', ') || 'Member' },
                        {
                            key: 'actions',
                            label: 'Actions',
                            render: (row) => row.id ? (
                                <div className="flex flex-wrap gap-2">
                                    <button onClick={() => setStatus(row, 'active')} className="rounded-md border px-2 py-1 text-xs font-bold">Restore</button>
                                    <button onClick={() => setStatus(row, 'suspended')} className="rounded-md border px-2 py-1 text-xs font-bold">Suspend</button>
                                    <button onClick={() => setStatus(row, 'banned')} className="rounded-md bg-red-600 px-2 py-1 text-xs font-bold text-white">Ban</button>
                                </div>
                            ) : '-',
                        },
                    ]}
                />
            )}
        </div>
    );
}
