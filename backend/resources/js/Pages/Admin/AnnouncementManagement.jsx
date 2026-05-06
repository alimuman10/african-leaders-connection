import ResourceManager from '../../Components/ResourceManager';
import { ActionButton, PageHeader } from './adminPageComponents';

const fields = [
    { name: 'title', label: 'Title', required: true },
    { name: 'body', label: 'Body', type: 'textarea', required: true },
    { name: 'audience', label: 'Audience', type: 'select', options: ['all', 'members', 'moderators'] },
    { name: 'country', label: 'Country' },
    { name: 'category', label: 'Category' },
];

export default function AnnouncementManagement() {
    return (
        <div className="grid gap-6">
            <PageHeader title="Announcements" description="Create, publish, schedule, and archive platform announcements." action={<ActionButton>Create Announcement</ActionButton>} />
            <ResourceManager title="Announcement List" endpoint="/api/admin/announcements" fields={fields} />
        </div>
    );
}
