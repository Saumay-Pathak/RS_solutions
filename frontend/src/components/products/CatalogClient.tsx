"use client";

import { useEffect, useState, useRef, useCallback } from "react";
import SectionList from "./SectionList";
import Sidebar from "./Sidebar";
import {
  getProducts,
  getAllCategoriesWithOrder,
} from "@/services/productService";

export type Product = {
  slug: string;
  id: string;
  title: string;
  description: string;
  category: { name: string; slug: string };
  images: string[];
};

export type Category = {
  title: string;
  items: { id: string; name: string; image: string; slug: string }[];
};

type RawCategory = {
  name?: string;
  title?: string;
  slug?: string;
  order?: number | string;
  sort_order?: number | string;
  sort?: number | string;
};

export default function CatalogClient() {
  const [products, setProducts] = useState<Product[]>([]);
  const [categoryOrderMap, setCategoryOrderMap] = useState<
    Record<string, number>
  >({});
  const [activeIndex, setActiveIndex] = useState(0);
  const [page, setPage] = useState(1);
  const [hasMore, setHasMore] = useState(true);
  const [loading, setLoading] = useState(true);

  const loaderRef = useRef<HTMLDivElement | null>(null);
  const observerRef = useRef<IntersectionObserver | null>(null);
  const lastScrollY = useRef(0);

  /* ----------------------------------------
     Fetch category order
  ---------------------------------------- */
  useEffect(() => {
    const fetchCategoriesOrder = async () => {
      try {
        const res = await getAllCategoriesWithOrder();
        const list = Array.isArray(res?.data)
          ? res.data
          : Array.isArray(res)
          ? res
          : [];

        const map: Record<string, number> = {};
        list.forEach((c: RawCategory) => {
          const name = (c?.name || c?.title || "").trim();
          const slug = (c?.slug || "").trim();
          const orderRaw = c?.order ?? c?.sort_order ?? c?.sort ?? 0;
          const order =
            typeof orderRaw === "string"
              ? parseInt(orderRaw, 10)
              : Number(orderRaw) || 0;

          if (name) map[name] = order;
          if (slug) map[slug] = order;
        });

        setCategoryOrderMap(map);
      } catch (err) {
        console.error("Error fetching category order:", err);
      }
    };

    fetchCategoriesOrder();
  }, []);

  /* ----------------------------------------
     Load products (pagination)
  ---------------------------------------- */
  const loadProducts = useCallback(async (pageNum: number) => {
    const prevScrollY = window.scrollY;

    try {
      setLoading(true);
      const res = await getProducts(pageNum);

      if (res?.data?.length) {
        setProducts((prev) => {
          const merged = [...prev, ...res.data];
          return Array.from(new Map(merged.map((p) => [p.id, p])).values());
        });
        setHasMore(res.meta.current_page < res.meta.last_page);
      } else {
        setHasMore(false);
      }
    } catch (err) {
      console.error("Error loading products:", err);
    } finally {
      setLoading(false);
      setTimeout(() => window.scrollTo(0, prevScrollY), 50);
    }
  }, []);

  useEffect(() => {
    loadProducts(page);
  }, [page, loadProducts]);

  /* ----------------------------------------
     Infinite scroll observer
  ---------------------------------------- */
  useEffect(() => {
    const node = loaderRef.current;
    if (!node || !hasMore) return;

    if (observerRef.current) observerRef.current.disconnect();

    let lastTrigger = 0;

    const observer = new IntersectionObserver(
      (entries) => {
        const first = entries[0];
        const now = Date.now();

        const isScrollingDown = window.scrollY > lastScrollY.current;
        lastScrollY.current = window.scrollY;

        if (
          first.isIntersecting &&
          hasMore &&
          !loading &&
          isScrollingDown &&
          now - lastTrigger > 800
        ) {
          lastTrigger = now;
          observer.unobserve(node);
          setTimeout(() => setPage((prev) => prev + 1), 300);
        }
      },
      { threshold: 1, rootMargin: "150px" }
    );

    observer.observe(node);
    observerRef.current = observer;

    return () => observer.disconnect();
  }, [hasMore, loading]);

  /* ----------------------------------------
     Group & sort categories
  ---------------------------------------- */
  const groupedCategories: Category[] = Object.values(
    products.reduce((acc, p) => {
      const catName = p.category?.name || "Uncategorized";
      if (!acc[catName]) acc[catName] = { title: catName, items: [] };
      acc[catName].items.push({
        id: p.id,
        name: p.title,
        image: p.images?.[0] || "/no-image.jpg",
        slug: p.slug || "",
      });
      return acc;
    }, {} as Record<string, Category>)
  );

  const sortedCategories: Category[] = [...groupedCategories].sort((a, b) => {
    const ao = categoryOrderMap[a.title] ?? 0;
    const bo = categoryOrderMap[b.title] ?? 0;
    if (ao === 0 && bo === 0) return a.title.localeCompare(b.title);
    if (ao === 0) return 1;
    if (bo === 0) return -1;
    return ao - bo;
  });

  /* ----------------------------------------
     UI
  ---------------------------------------- */
  return (
    <div className="flex gap-8 min-h-screen px-4 md:px-8 lg:px-24 pb-32 lg:pb-24">
      {/* Desktop Sidebar */}
      <aside className="hidden lg:block w-64 sticky top-24 h-[calc(100vh-120px)]">
        <div className="h-full overflow-y-auto">
          <Sidebar
            categories={sortedCategories}
            activeIndex={activeIndex}
            onSelect={setActiveIndex}
          />
        </div>
      </aside>

      {/* Main Content */}
      <section className="flex-1">
        <SectionList
          categories={sortedCategories}
          activeIndex={activeIndex}
          onActiveChange={setActiveIndex}
        />

        {/* Skeleton loader */}
        {loading && (
          <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 py-8">
            {Array.from({ length: 8 }).map((_, i) => (
              <div
                key={i}
                className="h-40 rounded-lg bg-gray-200 animate-pulse"
              />
            ))}
          </div>
        )}

        {/* Infinite scroll trigger */}
        <div ref={loaderRef} className="h-20 flex items-center justify-center">
          {hasMore && !loading && (
            <span className="text-sm text-gray-400">Scroll to load more</span>
          )}
        </div>
      </section>

      {/* ================= Mobile Category Bar ================= */}
      <div className="lg:hidden fixed bottom-0 left-0 right-0 z-50 bg-white border-t shadow-inner">
        <Sidebar
          categories={sortedCategories}
          activeIndex={activeIndex}
          onSelect={setActiveIndex}
        />
      </div>
    </div>
  );
}
