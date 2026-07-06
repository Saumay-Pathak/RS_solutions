"use client";

import React, { useEffect, useMemo, useState } from "react";
import { LifeBuoy } from "lucide-react";
import Image from "next/image";
import Link from "next/link";
import { getSolutions } from "@/services/solutionServices";
import { baseUri } from "@/services/constant";

type SolutionItem = {
  title: string;
  description?: string;
  image?: string | null;
  featured_image?: string | null;
  slug?: string;
};

const SolutionsGrid: React.FC = () => {
  const [items, setItems] = useState<SolutionItem[]>([]);
  const [loading, setLoading] = useState(true);
  const [query, setQuery] = useState("");

  useEffect(() => {
    (async () => {
      try {
        const res = await getSolutions();
        setItems(res.data || []);
      } catch (err) {
        console.error("Error loading solutions:", err);
      } finally {
        setLoading(false);
      }
    })();
  }, []);

  const filteredItems = useMemo(() => {
    if (!query.trim()) return items;
    const q = query.toLowerCase();
    return items.filter((item) =>
      [item.title, item.description]
        .filter(Boolean)
        .some((text) => String(text).toLowerCase().includes(q))
    );
  }, [items, query]);

  return (
    <section className="py-10 md:py-14">
      <div className="max-w-7xl mx-auto px-4">
        {/* Search */}
        <div className="mb-6">
          <div className="relative max-w-md">
            <input
              type="text"
              value={query}
              onChange={(e) => setQuery(e.target.value)}
              placeholder="Search solutions…"
              className="w-full h-11 rounded-xl border border-gray-300 bg-white px-11 text-sm text-black focus:outline-none focus:ring-2 focus:ring-[#EFAF00]"
            />
            <span className="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
              🔍
            </span>
            {query && (
              <button
                onClick={() => setQuery("")}
                className="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
                aria-label="Clear search"
              >
                ✕
              </button>
            )}
          </div>

          {!loading && (
            <p className="mt-2 text-sm text-gray-500">
              Showing {filteredItems.length} solution
              {filteredItems.length !== 1 && "s"}
            </p>
          )}
        </div>

        {/* Loading Skeleton */}
        {loading ? (
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            {Array.from({ length: 6 }).map((_, i) => (
              <div
                key={i}
                className="animate-pulse rounded-2xl border border-gray-200 bg-white overflow-hidden"
              >
                <div className="h-48 bg-gray-200" />
                <div className="p-5 space-y-3">
                  <div className="h-5 bg-gray-200 rounded w-2/3" />
                  <div className="h-4 bg-gray-200 rounded w-full" />
                  <div className="h-4 bg-gray-200 rounded w-5/6" />
                </div>
              </div>
            ))}
          </div>
        ) : filteredItems.length === 0 ? (
          <div className="rounded-2xl border border-dashed border-gray-300 bg-gray-50 p-10 text-center">
            <p className="text-lg font-medium text-gray-700">
              No solutions found
            </p>
            <p className="mt-2 text-sm text-gray-500">
              Try adjusting your search keywords.
            </p>
          </div>
        ) : (
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            {filteredItems.map((item, idx) => {
              const src =
                item.image || item.featured_image
                  ? `${baseUri}${item.image || item.featured_image}`
                  : null;

              return (
                <Link
                  key={idx}
                  href={item.slug ? `/solutions/${item.slug}` : "/solutions"}
                  className="group rounded-2xl border border-gray-200 bg-white overflow-hidden transition-all duration-300 hover:-translate-y-1 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-[#EFAF00]"
                >
                  {/* Image */}
                  <div className="relative h-52 overflow-hidden">
                    {src && (
                      <>
                        <Image
                          src={src}
                          alt={item.title}
                          fill
                          unoptimized
                          className="object-cover transition-transform duration-500 group-hover:scale-105"
                        />
                        <div className="absolute inset-0 bg-gradient-to-t from-black/30 via-black/0" />
                      </>
                    )}
                  </div>

                  {/* Content */}
                  <div className="p-5 flex flex-col h-[190px]">
                    <h3 className="text-lg font-semibold text-[#1E1410] mb-2">
                      {item.title}
                    </h3>

                    {item.description && (
                      <p className="text-sm text-gray-600 line-clamp-3">
                        {item.description}
                      </p>
                    )}

                    <div className="mt-auto pt-4 flex items-center justify-between">
                      <span className="text-sm font-medium text-orange-600 px-3 py-1.5 rounded-full transition-all duration-300 group-hover:bg-orange-500 group-hover:text-white">
                        View details
                      </span>
                      <span className="text-orange-600 transition-transform group-hover:translate-x-1">
                        →
                      </span>
                    </div>
                  </div>
                </Link>
              );
            })}
          </div>
        )}
      </div>

      <div className="py-10 px-4">
          <div className="rounded-2xl border border-neutral-200 shadow-sm p-5 md:p-8 flex flex-col md:flex-row items-center gap-4 md:gap-6 bg-white">
            <div className="w-12 h-12 rounded-full bg-orange-50 text-orange-600 flex items-center justify-center">
              <LifeBuoy className="w-6 h-6" />
            </div>
            <div className="flex-1 text-center md:text-left">
              <h3 className="text-base md:text-lg font-semibold text-neutral-800">Need Help Getting Started?</h3>
              <p className="mt-1 text-sm md:text-base text-neutral-600 pe-0 sm:pe-10">Our technical support team is ready to assist you with installation, configuration, and any questions you may have about our software.</p>
            </div>
            <Link href="/sales" className="me-0 sm:me-10 inline-flex items-center bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg text-sm md:text-base font-medium transition hover:translate-y-1">Contact Sales</Link>
          </div>
        </div>

    </section>
  );
};

export default SolutionsGrid;