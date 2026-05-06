import ResourceManager from '../../Components/ResourceManager';
import { ActionButton, PageHeader, SectionCard } from './adminPageComponents';

const fields = [
    { name: 'key', label: 'Section Key', required: true },
    { name: 'title', label: 'Title', required: true },
    { name: 'subtitle', label: 'Subtitle' },
    { name: 'content', label: 'Content', type: 'textarea' },
    { name: 'sort_order', label: 'Sort order', type: 'number' },
];

export default function HomepageControl() {
    return (
        <div className="grid gap-6">
            <PageHeader title="Homepage Control" description="Manage hero content, featured stories, featured leaders, CTA sections, and homepage statistics." action={<ActionButton>Update Homepage</ActionButton>} />
            <ResourceManager title="Homepage Sections" endpoint="/api/admin/homepage" fields={fields} />
            <SectionCard title="Featured Controls">
                <div className="grid gap-3 md:grid-cols-4">
                    {['Hero section editor', 'Featured stories', 'Featured leaders', 'CTA section control'].map((item) => (
                        <div key={item} className="rounded-md bg-[#fffaf0] p-4 text-sm font-black text-[#061311]">{item}</div>
                    ))}
                </div>
            </SectionCard>
        </div>
    );
}
