import { Metadata } from "next";
import CategoryClient from "./CategoryClient";

type Props = {
  params: Promise<{ slug: string }>;
};

export async function generateMetadata({ params }: Props): Promise<Metadata> {
  const { slug } = await params;
  const apiBase =
    process.env.NEXT_PUBLIC_API_BASE_URL ||
    "https://app.realtimebiometrics.net/api";

  try {
    const res = await fetch(
      `${apiBase}/content/categories?slug=${encodeURIComponent(slug)}`,
      {
        cache: "no-store",
        headers: { Accept: "application/json" },
      }
    );

    if (!res.ok) throw new Error("Failed to fetch metadata");

    const json = await res.json();
    const category = Array.isArray(json?.data) ? json.data[0] : null;

    if (category) {
      return {
        title: category.meta_title || `${category.name} | Realtime Biometrics`,
        description:
          category.meta_description ||
          category.description ||
          `Explore ${category.name} products`,
        openGraph: {
          title: category.meta_title || category.name,
          description: category.meta_description || category.description,
        },
      };
    }
  } catch (error) {
    console.error("Metadata error:", error);
  }

  return {
    title: "Products | Realtime Biometrics",
    description: "Browse our biometric product catalog",
  };
}

export default function CategoryPage() {
  return (
    <main className="min-h-screen bg-gray-50">
      <CategoryClient />
    </main>
  );
}
