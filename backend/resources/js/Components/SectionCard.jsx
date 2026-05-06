export default function SectionCard({ title, children, action }) {
    return (
        <section className="rounded-lg border border-[#e2d5b8] bg-white p-5 shadow-sm">
            <div className="flex items-start justify-between gap-4">
                <h2 className="text-lg font-black text-[#061311]">{title}</h2>
                {action}
            </div>
            <div className="mt-4">{children}</div>
        </section>
    );
}
