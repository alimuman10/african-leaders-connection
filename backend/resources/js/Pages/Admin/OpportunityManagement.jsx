import ResourceManager from '../../Components/ResourceManager';
import { ActionButton, PageHeader } from './adminPageComponents';

const fields = [
    { name: 'title', label: 'Title', required: true },
    { name: 'category', label: 'Category', required: true },
    { name: 'country', label: 'Country' },
    { name: 'eligibility', label: 'Eligibility' },
    { name: 'summary', label: 'Summary', type: 'textarea' },
    { name: 'external_url', label: 'External URL', type: 'url' },
    { name: 'deadline_at', label: 'Deadline', type: 'datetime-local' },
    { name: 'status', label: 'Status', type: 'select', options: ['active', 'expired', 'draft', 'featured'] },
];

export default function OpportunityManagement() {
    return (
        <div className="grid gap-6">
            <PageHeader title="Opportunities" description="Publish scholarships, fellowships, grants, jobs, internships, competitions, and leadership programs." action={<ActionButton>Add Opportunity</ActionButton>} />
            <ResourceManager title="Opportunities Table" endpoint="/api/admin/opportunities" fields={fields} />
        </div>
    );
}
