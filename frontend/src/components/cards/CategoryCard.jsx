import { Link } from "react-router-dom";
import { UtensilsCrossed } from "lucide-react";

export default function CategoryCard({ name, icon, image, href, onClick }) {
  const content = (
    <article className="flex min-h-24 flex-col items-center justify-center rounded-2xl bg-[#F2E6D8] px-3 py-4 text-center text-[#333333] shadow-[0_4px_12px_rgba(51,51,51,0.06)] transition duration-300 hover:-translate-y-0.5 hover:bg-[#e8dac9] hover:shadow-[0_10px_20px_rgba(51,51,51,0.12)]">
      {image ? (
        <img src={image} alt={name} className="h-8 w-8 rounded-full object-cover" loading="lazy" />
      ) : (
        <span aria-hidden="true" className="text-xl">
          {icon || <UtensilsCrossed className="h-5 w-5" />}
        </span>
      )}
      <p className="mt-2 text-sm font-semibold">{name}</p>
    </article>
  );

  if (href) {
    return <Link to={href}>{content}</Link>;
  }

  return (
    <button type="button" onClick={onClick} className="w-full text-left">
      {content}
    </button>
  );
}
