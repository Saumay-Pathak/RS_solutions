"use client";

import React, { useState, useEffect } from "react";
import Link from "next/link";
import axiosClient from "@/services/axiosClient";
import Image from "next/image";
import { motion, AnimatePresence } from "framer-motion";
import { baseUri } from "@/services/constant";

type Product = {
  id: string;
  title: string;
  description: string;
  slug: string;
  images: string[];
  category: {
    id: string;
    name: string;
    slug: string;
    parent_id: string | null;
    parent: {
      name: string;
      slug: string;
      id: string;
    } | null;
  };
};

type Category = {
  id: string;
  name: string;
  slug: string;
  parent_id: string | null;
  parent: {
    name: string;
    slug: string;
    id: string;
  } | null;
  sort_order?: number;
  products: Product[];
};

type CategorySummary = {
  id: string | number;
  sort_order?: number;
};

type ApiResponse = {
  success: boolean;
  data: Product[];
  meta: {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number;
    to: number;
  };
  links: {
    first: string;
    last: string;
    prev: string | null;
    next: string | null;
  };
};

// ── Shared primitives ─────────────────────────────────────────────────────────
const ChevronRight = ({ className = "w-3 h-3" }: { className?: string }) => (
  <svg className={className} fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2.5} d="M9 5l7 7-7 7" />
  </svg>
);

const ProductsMegaMenu = () => {
  const [activeCategory, setActiveCategory] = useState<string | null>(null);
  const [categories, setCategories] = useState<Category[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchAllProducts = async () => {
      try {
        setLoading(true);
        let allProducts: Product[] = [];
        let totalPages = 1;

        const firstResponse = await axiosClient.get("/content/products?per_page=100&page=1");
        const firstData: ApiResponse = firstResponse.data;

        if (firstData.success) {
          allProducts = [...firstData.data];
          totalPages = firstData.meta.last_page;

          const pagePromises = [];
          for (let page = 2; page <= totalPages; page++) {
            pagePromises.push(axiosClient.get(`/content/products?page=${page}`));
          }

          if (pagePromises.length > 0) {
            const responses = await Promise.all(pagePromises);
            responses.forEach((response) => {
              const pageData: ApiResponse = response.data;
              if (pageData.success) {
                allProducts = [...allProducts, ...pageData.data];
              }
            });
          }

          const categoriesMap = new Map();
          allProducts.forEach((product: Product) => {
            const category = product.category;
            if (!categoriesMap.has(category.id)) {
              categoriesMap.set(category.id, { ...category, products: [] });
            }
            categoriesMap.get(category.id).products.push(product);
          });

          let categoriesArray: Category[] = Array.from(categoriesMap.values());

          try {
            const catRes = await axiosClient.get("/content/categories?all=true");
            const catPayload = catRes.data as { success: boolean; data: CategorySummary[] };
            if (catPayload?.success && Array.isArray(catPayload?.data)) {
              const orderMap = new Map<string, number>();
              catPayload.data.forEach((cat: CategorySummary) => {
                const id = String(cat.id);
                const orderVal = Number(cat.sort_order ?? Number.POSITIVE_INFINITY);
                orderMap.set(id, orderVal);
              });
              categoriesArray = categoriesArray.map((c) => ({
                ...c,
                sort_order: orderMap.get(String(c.id)) ?? c.sort_order,
              }));
              categoriesArray.sort((a, b) => {
                const normalize = (v: unknown) => {
                  const n = Number(v);
                  if (!isFinite(n) || n === 0) return Number.POSITIVE_INFINITY;
                  return n;
                };
                const sa = normalize(a?.sort_order);
                const sb = normalize(b?.sort_order);
                if (sa !== sb) return sa - sb;
                return String(a.name).localeCompare(String(b.name));
              });
            } else {
              categoriesArray.sort((a, b) => String(a.name).localeCompare(String(b.name)));
            }
          } catch {
            categoriesArray.sort((a, b) => String(a.name).localeCompare(String(b.name)));
          }

          setCategories(categoriesArray);
          if (categoriesArray.length > 0) {
            setActiveCategory(categoriesArray[0].id);
          }
        }
      } catch (error) {
        console.error("Error fetching products:", error);
      } finally {
        setLoading(false);
      }
    };

    fetchAllProducts();
  }, []);

  const activeCategoryData = categories.find((cat) => cat.id === activeCategory);
  const displayedProducts = activeCategoryData?.products || [];
  const totalProductsInCategory = activeCategoryData?.products?.length || 0;

  if (loading) {
    return (
      <div className="w-full bg-white border border-gray-100 rounded-2xl shadow-2xl">
        <div className="p-8 flex flex-col justify-center items-center h-48 space-y-3">
          <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-orange-500" />
          <p className="text-gray-600 text-sm">Loading products...</p>
        </div>
      </div>
    );
  }

  return (
    <div className="overflow-hidden bg-white rounded-2xl shadow-[0_20px_60px_rgba(0,0,0,0.08)] border border-gray-100">
      <div className="flex">
        {/* ── Sidebar ── */}
        <div className="w-60 flex-shrink-0 bg-gray-50 border-r border-gray-100 p-3">
          <p className="text-[11px] font-bold text-gray-500 tracking-widest uppercase mb-3 px-2">
            Categories ({categories.length})
          </p>
          <div className="space-y-0.5 max-h-[390px] overflow-y-auto pr-1 custom-scrollbar">
            {categories.map((category) => {
              const isActive = activeCategory === category.id;
              return (
                <div
                  key={category.id}
                  className={`group flex items-center justify-between px-2.5 py-2 rounded-lg cursor-pointer transition-all duration-200 ${
                    isActive
                      ? "bg-gradient-to-r from-orange-500 to-amber-500 text-white shadow-sm shadow-orange-500/20 font-semibold"
                      : "text-gray-600 hover:bg-orange-50 hover:text-gray-900"
                  }`}
                  onMouseEnter={() => setActiveCategory(category.id)}
                >
                  <span className="text-[13px] font-medium leading-snug pr-2">{category.name}</span>
                  <ChevronRight
                    className={`w-3 h-3 flex-shrink-0 transition-all duration-200 ${
                      isActive
                        ? "opacity-100 text-white"
                        : "opacity-0 -translate-x-1 group-hover:opacity-100 group-hover:translate-x-0 text-orange-400"
                    }`}
                  />
                </div>
              );
            })}
          </div>
        </div>

        {/* ── Right Panel ── */}
        <div className="flex-1 p-5 min-h-[430px] flex flex-col">
          <AnimatePresence mode="wait">
            {activeCategoryData ? (
              <motion.div
                key={activeCategoryData.id}
                initial={{ opacity: 0, y: 8 }}
                animate={{ opacity: 1, y: 0 }}
                exit={{ opacity: 0, y: -8 }}
                transition={{ duration: 0.18 }}
                className="flex-1 flex flex-col justify-between"
              >
                {/* Panel Header */}
                <div>
                  <div className="flex items-center gap-2.5 mb-1.5">
                    <h3 className="text-lg font-extrabold text-gray-900 tracking-tight">
                      {activeCategoryData.name}
                    </h3>
                    <span className="text-[10px] font-bold text-gray-600 bg-gray-100 border border-gray-200 px-2 py-0.5 rounded-full">
                      {totalProductsInCategory} {totalProductsInCategory === 1 ? "Product" : "Products"}
                    </span>
                  </div>
                  <p className="text-gray-700 text-[13px] mb-4">
                    Browse products in this category
                  </p>

                  {/* Product Cards */}
                  {displayedProducts.length > 0 ? (
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-3 max-h-[240px] overflow-y-auto pr-1 custom-scrollbar">
                      {displayedProducts.map((product) => (
                        <Link
                          key={product.id}
                          href={`/products/${product.slug}`}
                          className="group/item flex items-center gap-3 p-3 bg-white border border-gray-100 hover:border-orange-200 hover:bg-orange-50/30 rounded-xl shadow-sm hover:shadow-md transition-all duration-200"
                        >
                          {/* Thumbnail */}
                          {product.images && product.images.length > 0 ? (
                            <div className="flex-shrink-0 w-14 h-14 bg-gray-50 rounded-lg overflow-hidden relative border border-gray-100 group-hover/item:scale-105 transition-transform duration-200">
                              <Image
                                src={`${baseUri}${product.images[0]}`}
                                alt={product.title}
                                fill
                                sizes="56px"
                                className="object-cover"
                                loading="lazy"
                              />
                            </div>
                          ) : (
                            <div className="flex-shrink-0 w-14 h-14 bg-gray-50 rounded-lg flex items-center justify-center border border-gray-100 group-hover/item:scale-105 transition-transform duration-200">
                              <svg className="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                              </svg>
                            </div>
                          )}
                          {/* Info */}
                          <div className="flex-1 min-w-0">
                            <h4 className="font-bold text-gray-900 text-[13px] mb-0.5 truncate group-hover/item:text-orange-600 transition-colors">
                              {product.title}
                            </h4>
                            <p className="text-gray-600 text-[11px] line-clamp-2 leading-relaxed">
                              {product.description.replace(/<[^>]*>/g, "")}
                            </p>
                          </div>
                          <ChevronRight className="w-3 h-3 text-gray-300 group-hover/item:text-orange-400 flex-shrink-0 group-hover/item:translate-x-0.5 transition-all duration-200" />
                        </Link>
                      ))}
                    </div>
                  ) : (
                    <div className="flex items-center justify-center h-32">
                      <p className="text-gray-600 text-sm">No products in this category</p>
                    </div>
                  )}
                </div>

                {/* Panel Footer */}
                <div className="mt-5 pt-4 border-t border-gray-100 flex items-center justify-between">
                  <div />
                  <Link
                    href={`/products/category/${activeCategoryData.slug}`}
                    className="group/btn bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white text-[13px] font-semibold transition-all duration-200 flex items-center gap-2 px-4 py-2.5 rounded-xl shadow-md shadow-orange-500/15 hover:shadow-lg hover:shadow-orange-500/20 hover:-translate-y-0.5"
                  >
                    View all in Category
                    <ChevronRight className="w-3.5 h-3.5 group-hover/btn:translate-x-0.5 transition-transform duration-200" />
                  </Link>
                </div>
              </motion.div>
            ) : (
              <div className="flex-1 flex items-center justify-center">
                <p className="text-gray-600 text-sm">Select a category to view products</p>
              </div>
            )}
          </AnimatePresence>
        </div>
      </div>

      <style jsx>{`
        .custom-scrollbar::-webkit-scrollbar { width: 4px; height: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f8f8f8; border-radius: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e5e5e5; border-radius: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #ccc; }
      `}</style>
    </div>
  );
};

export default ProductsMegaMenu;
