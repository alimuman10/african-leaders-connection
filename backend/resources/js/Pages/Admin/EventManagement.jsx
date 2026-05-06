import ResourceManager from '../../Components/ResourceManager';
import { ActionButton, PageHeader } from './adminPageComponents';

const fields = [
    { name: 'title', label: 'Title', required: true },
    { name: 'summary', label: 'Summary' },
    { name: 'description', label: 'Description', type: 'textarea' },
    { name: 'location', label: 'Location' },
    { name: 'starts_at', label: 'Start date/time', type: 'datetime-local' },
    { name: 'status', label: 'Status', type: 'select', options: ['scheduled', 'draft', 'completed', 'cancelled'] },
];

export default function EventManagement() {
    return (
        <div className="grid gap-6">
            <PageHeader title="Events" description="Create events, manage status, track registrations, and prepare resources for members." action={<ActionButton>Create Event</ActionButton>} />
            <ResourceManager title="Events Table" endpoint="/api/admin/events" fields={fields} />
        </div>
    );
}
