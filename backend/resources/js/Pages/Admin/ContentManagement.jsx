import ResourceManager from '../../Components/ResourceManager';
import { ActionButton, PageHeader, SectionCard } from './adminPageComponents';

const articleFields = [
    { name: 'title', label: 'Title', required: true },
    { name: 'excerpt', label: 'Excerpt' },
    { name: 'body', label: 'Body', type: 'textarea', required: true },
    { name: 'category', label: 'Category' },
    { name: 'status', label: 'Status', type: 'select', options: ['draft', 'published', 'archived'] },
];

export default function ContentManagement() {
    return (
        <div className="grid gap-6">
            <PageHeader title="Content Management" description="Manage articles, platform stories, community projects, and member submissions from one editorial workspace." action={<ActionButton>Create Content</ActionButton>} />
            <ResourceManager title="Leadership Articles" endpoint="/api/admin/posts" fields={articleFields} />
            <SectionCard title="Submission Review">
                <div className="grid gap-3 md:grid-cols-3">
                    {['Stories awaiting review', 'Projects awaiting review', 'Campaign ideas awaiting review'].map((item) => (
                        <div key={item} className="rounded-md bg-[#fffaf0] p-4">
                            <p className="font-black text-[#061311]">{item}</p>
                            <p className="mt-2 text-sm text-slate-600">Approve, reject, or request revision once submissions are connected.</p>
                        </div>
                    ))}
                </div>
            </SectionCard>
        </div>
    );
}
