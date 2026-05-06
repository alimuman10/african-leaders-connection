import ResourceManager from '../../Components/ResourceManager';
import { ActionButton, PageHeader } from './adminPageComponents';

const fields = [
    { name: 'title', label: 'Title', required: true },
    { name: 'country', label: 'Country' },
    { name: 'summary', label: 'Summary' },
    { name: 'description', label: 'Description', type: 'textarea' },
    { name: 'status', label: 'Status', type: 'select', options: ['active', 'draft', 'closed', 'pending'] },
];

export default function CampaignManagement() {
    return (
        <div className="grid gap-6">
            <PageHeader title="Campaigns" description="Create advocacy campaigns, review member proposals, track supporters, and publish impact updates." action={<ActionButton>Create Campaign</ActionButton>} />
            <ResourceManager title="Campaigns Table" endpoint="/api/admin/campaigns" fields={fields} />
        </div>
    );
}
