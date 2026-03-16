import { useEffect, useState } from "react";
import { Link } from "react-router-dom";
import { CakeSlice, Coffee, Leaf, Pizza, Sandwich, Soup, UtensilsCrossed } from "lucide-react";
import CategoryCard from "../components/cards/CategoryCard";
import PageScaffold from "../components/PageScaffold";
import { fetchCategories } from "../api/catalogApi";

function categoryIcon(name) {
  const lower = (name || "").toLowerCase();
  const iconClass = "h-5 w-5";
  if (lower.includes("pizza")) return <Pizza className={iconClass} />;
  if (lower.includes("burger")) return <Sandwich className={iconClass} />;
  if (lower.includes("asian")) return <Soup className={iconClass} />;
  if (lower.includes("dessert")) return <CakeSlice className={iconClass} />;
  if (lower.includes("breakfast")) return <Coffee className={iconClass} />;
  if (lower.includes("dinner")) return <UtensilsCrossed className={iconClass} />;
  return <Leaf className={iconClass} />;
}

export default function CategoriesPage() {
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");
  const [categories, setCategories] = useState([]);

  useEffect(() => {
    let mounted = true;
    async function loadCategories() {
      try {
        setLoading(true);
        setError("");
        const data = await fetchCategories();
        if (!mounted) {
          return;
        }
        setCategories(Array.isArray(data) ? data : []);
      } catch {
        if (mounted) {
          setError("Failed to load categories.");
        }
      } finally {
        if (mounted) {
          setLoading(false);
        }
      }
    }
    loadCategories();
    return () => {
      mounted = false;
    };
  }, []);

  return (
    <PageScaffold title="All categories" subtitle="Browse cuisine and food types in one place.">
      {error ? (
        <section className="rounded-2xl border border-[#D96C4A]/35 bg-[#FFF8F0] p-4 text-sm text-[#D96C4A]">
          {error}
        </section>
      ) : null}

      <section className="space-y-4">
        <div className="flex items-center justify-between">
          <h2 className="text-lg font-semibold sm:text-xl">Categories</h2>
          <Link to="/restaurants" className="text-sm font-medium text-[#7A9E7E] hover:underline">
            Go to restaurants
          </Link>
        </div>
        <div className="grid grid-cols-2 gap-2 sm:grid-cols-3 lg:grid-cols-6">
          {(categories.length > 0 ? categories : [{ name: loading ? "Loading..." : "No categories", slug: "placeholder" }]).map(
            (category) => (
              <CategoryCard
                key={category.slug || category.name}
                name={category.name}
                icon={categoryIcon(category.name)}
                href={`/restaurants?category=${encodeURIComponent(category.slug || category.name)}`}
              />
            ),
          )}
        </div>
      </section>
    </PageScaffold>
  );
}
