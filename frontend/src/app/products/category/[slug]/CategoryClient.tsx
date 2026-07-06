"use client";

import React, { useEffect, useState, useCallback } from "react";
import Layout from "@/components/layout/Layout";
import Link from "next/link";
import { useParams, notFound } from "next/navigation";
import Card from "@/components/products/Card";
import AdvancedBreadcrumb from "@/components/common/Bredacrumb";
import {
  getProductByCategorySlug,
  getProductById,
  getProducts,
} from "@/services/productService";
import Image from "next/image";

/* ================= TYPES ================= */

type Product = {
  id: string;
  title: string;
  description: string;
  slug: string;
  images: string[];
  image: string;
  featured_image: string;
  features: string[];
  category: {
    name: string;
    id: string;
    slug: string;
  };
};

type Category = {
  id: string;
  name: string;
  slug: string;
  meta_title?: string;
  meta_description?: string;
  description?: string;
  products: Product[];
};

type CategoryBasic = Omit<Category, "products">;

/* ================= HELPERS ================= */

const isCategoryBasic = (x: unknown): x is CategoryBasic => {
  if (!x || typeof x !== "object") return false;
  const obj = x as { id?: unknown; name?: unknown; slug?: unknown };
  return (
    (typeof obj.id === "string" || typeof obj.id === "number") &&
    typeof obj.name === "string" &&
    typeof obj.slug === "string"
  );
};

const toCategoryList = (input: unknown): CategoryBasic[] => {
  const fromData = (input as { data?: unknown }).data;
  if (Array.isArray(fromData) && fromData.every(isCategoryBasic))
    return fromData as CategoryBasic[];
  if (Array.isArray(input) && input.every(isCategoryBasic))
    return input as CategoryBasic[];
  return [];
};

const isProduct = (x: unknown): x is Product => {
  if (!x || typeof x !== "object") return false;
  const obj = x as {
    id?: unknown;
    title?: unknown;
    slug?: unknown;
    images?: unknown;
    category?: unknown;
  };
  const cat = obj.category as
    | { slug?: unknown; name?: unknown; id?: unknown }
    | undefined;

  return (
    (typeof obj.id === "string" || typeof obj.id === "number") &&
    typeof obj.title === "string" &&
    typeof obj.slug === "string" &&
    (Array.isArray(obj.images) || obj.images === undefined) &&
    (!!cat
      ? typeof cat.slug === "string" ||
        typeof cat.name === "string" ||
        typeof cat.id === "string" ||
        typeof cat.id === "number"
      : true)
  );
};

const toProductList = (input: unknown): Product[] => {
  const fromData = (input as { data?: unknown }).data;
  const fromNested = (input as { data?: { data?: unknown } }).data?.data;

  if (Array.isArray(fromData) && fromData.every(isProduct))
    return fromData as Product[];
  if (Array.isArray(fromNested) && fromNested.every(isProduct))
    return fromNested as Product[];
  if (Array.isArray(input) && input.every(isProduct)) return input as Product[];
  return [];
};

/* ================= COMPONENT ================= */

export default function CategoryClient() {
  const { slug } = useParams();

  const [category, setCategory] = useState<Category | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  /* 🔹 Fetch Category */
  const loadCategory = useCallback(async () => {
    try {
      setLoading(true);
      setError(null);

      const res = await getProductByCategorySlug(slug as string);
      const catList = toCategoryList(res);
      const cat = catList[0];

      if (!cat?.id) {
        setError("Category not found");
        return;
      }

      const prodRes = await getProductById(String(cat.id));
      let products = toProductList(prodRes);

      // 🔹 Fallback matching
      if (!products.length) {
        const allRes = await getProducts(1);
        const allProducts = toProductList(allRes);

        const cSlug = cat.slug.toLowerCase();
        const cName = cat.name.toLowerCase();
        const cId = String(cat.id);

        products = allProducts.filter((p) => {
          return (
            p.category?.slug?.toLowerCase() === cSlug ||
            p.category?.name?.toLowerCase() === cName ||
            String(p.category?.id) === cId
          );
        });
      }

      setCategory({
        ...(cat as Category),
        products,
      });
    } catch (err) {
      console.error(err);
      setError("Failed to load category");
    } finally {
      setLoading(false);
    }
  }, [slug]);

  useEffect(() => {
    if (slug) loadCategory();
  }, [slug, loadCategory]);

  /* ================= LOADING ================= */

  if (loading) {
    return (
      <Layout>
        <div className="max-w-7xl mx-auto px-6 py-16">
          <div className="h-8 w-56 bg-gray-200 rounded mx-auto mb-10 animate-pulse" />

          <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-6">
            {Array.from({ length: 8 }).map((_, i) => (
              <div
                key={i}
                className="h-64 rounded-xl bg-gray-200 animate-pulse"
              />
            ))}
          </div>
        </div>
      </Layout>
    );
  }

  if (error || !category) {
    notFound();
  }

  const breadcrumbItems = [
    { label: "Home", href: "/" },
    { label: "Products", href: "/products" },
    { label: category.name, href: `/products/category/${slug}` },
  ];

  /* ================= UI ================= */

  return (
    <Layout>
      {/* Breadcrumb */}
      <AdvancedBreadcrumb items={breadcrumbItems} />

      <div className="mx-auto px-6 pb-20 mt-10">
        {/* Header */}
        <div className="text-center mb-6">
          <h1 className="text-2xl sm:text-3xl section-title-long font-bold text-gray-900 mb-0">
            {category.name}
          </h1>
          <p className="text-sm text-gray-500">
            {category.products.length} Products
          </p>
        </div>
        {/* Description */}
        {category.description && (
          <div className="max-w-5xl mx-auto mb-14 text-gray-600 leading-relaxed text-[15px] md:text-[16px]">
            {category.description}
          </div>
        )}

        {/* Products */}
        {category.products.length > 0 ? (
          <div className="container mx-auto grid grid-cols-1 sm:grid-cols-3 md:grid-cols-4 gap-6">
            {category.products.map((product) => {
              const img =
                product.images?.[0] ??
                product.featured_image ??
                product.image ??
                "/no-image.jpg";
              return (
                <Link
                  key={product.id}
                  href={`/products/${product.slug}`}
                  className="group"
                  aria-label={`View product ${product.title}`}
                >
                  <div className="transition-transform duration-300 group-hover:-translate-y-1">
                    <Card it={{ name: product.title, image: img }} />
                  </div>
                </Link>
              );
            })}
          </div>
        ) : (
          /* Empty State */
          <div className="text-center py-24">
            <Image
              src="/empty-box.svg"
              alt="No products"
              className="w-40 mx-auto mb-6 opacity-80"
            />
            <h3 className="text-lg font-semibold text-gray-700">
              No products found
            </h3>
            <p className="text-sm text-gray-500 mt-2">
              New products will be added soon.
            </p>
            <Link
              href="/products"
              className="inline-block mt-6 px-6 py-2 rounded-md bg-primary text-white text-sm hover:bg-primary/90 transition"
            >
              Browse all products
            </Link>
          </div>
        )}
      </div>
    </Layout>
  );
}
