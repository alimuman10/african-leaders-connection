import { useCollectionQuery } from '../../lib/query';
import { ActionButton, AdminTable, ErrorState, PageHeader, SectionCard, StatusBadge } from './adminPageComponents';

export default function ModeratorInvitations() {
    const { data: moderators = [], isError } = useCollectionQuery(['admin-moderators'], '/api/admin/moderators', {
        retry: false,
    });
    const fallback = [
        { name: 'No pending invitations', email: '-', status: 'pending' },
    ];

    return (
        <div className="grid gap-6">
            <PageHeader title="Moderator Invitations" description="Invite trusted members to moderation capacity while keeping them inside the Member Dashboard." action={<ActionButton>Invite Member</ActionButton>} />
            {isError && <ErrorState label="Moderator invitation endpoint is not complete yet. Showing safe placeholder data." />}
            <SectionCard title="Invitation List">
                <AdminTable
                    rows={moderators.length ? moderators : fallback}
                    emptyTitle="No moderator invitations"
                    columns={[
                        { key: 'name', label: 'Member' },
                        { key: 'email', label: 'Email' },
                        { key: 'status', label: 'Status', render: (row) => <StatusBadge status={row.status || 'pending'} /> },
                        { key: 'actions', label: 'Actions', render: () => <button className="rounded-md border px-3 py-2 text-xs font-bold">Revoke</button> },
                    ]}
                />
            </SectionCard>
        </div>
    );
}
