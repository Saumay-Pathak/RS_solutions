"use client";

import React, { useEffect, useState } from "react";
import Link from "next/link";
import Image from "next/image";
import axiosClient from "@/services/axiosClient";

type Product = {
  id: string;
  title: string;
  description?: string;
  slug: string;
  images: string[];
  category?: {
    name: string;
    slug: string;
  } | null;
};

type ApiResponse = {
  success: boolean;
  data: Product[];
};

const SpotlightSection = () => {
  const [products, setProducts] = useState<Product[]>([]);
  const [loading, setLoading] = useState<boolean>(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    const fetchProducts = async () => {
      try {
        setLoading(true);
        setError(null);
        const res = await axiosClient.get("/content/products?per_page=12&page=1");
        const data: ApiResponse = res.data;
        if (data?.success) {
          setProducts(data.data || []);
        } else {
          setProducts([]);
        }
      } catch (e: unknown) {
        console.error("Failed to load products", e);
        setError("Failed to load products");
      } finally {
        setLoading(false);
      }
    };
    fetchProducts();
  }, []);

  const baseUrl = "https://app.realtimebiometrics.net";

  return (
    <section className="pt-16 lg:py-16 bg-white">
      <div className="container mx-auto px-4">
        <div className="mb-8 text-center">
          <h2 className="text-2xl lg:text-3xl font-light text-black">
            Featured Products
          </h2>
          <p className="text-sm text-black/70">Handpicked highlights from our catalog</p>
        </div>

        {loading ? (
          <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            {Array.from({ length: 8 }).map((_, idx) => (
              <div
                key={idx}
                className="rounded-xl border border-gray-200 bg-white p-4 animate-pulse"
              >
                <div className="h-36 bg-gray-200 rounded-lg mb-3" />
                <div className="h-4 bg-gray-200 rounded w-3/4 mb-2" />
                <div className="h-3 bg-gray-200 rounded w-1/2" />
              </div>
            ))}
          </div>
        ) : error ? (
          <div className="text-center text-red-600">{error}</div>
        ) : (
          <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            {products.map((product) => (
              <Link
                key={product.id}
                href={`/products/${product.slug}`}
                className="group rounded-xl border border-gray-200 bg-white shadow-sm hover:shadow-md transition"
              >
                <div className="p-4">
                  <div className="relative h-36 md:h-40 lg:h-44 rounded-lg bg-gray-100 overflow-hidden">
                    {product.images && product.images.length > 0 ? (
                      <Image
                        src={`${baseUrl}/storage/${product.images[0]}`}
                        alt={product.title}
                        fill
                        className="object-contain p-2"
                      />
                    ) : (
                      <div className="flex items-center justify-center h-full text-gray-400">
                        No image
                      </div>
                    )}
                  </div>
                  <h3 className="mt-3 text-sm md:text-base font-medium text-gray-900 line-clamp-2 group-hover:text-orange-600">
                    {product.title}
                  </h3>
                  {product.category?.name && (
                    <div className="mt-1 text-xs text-gray-500">
                      {product.category.name}
                    </div>
                  )}
                </div>
              </Link>
            ))}
          </div>
        )}

        <div className="mt-10 flex justify-center">
          <Link
            href="/products"
            className="inline-flex items-center gap-2 px-5 py-2.5 rounded-md bg-orange-500 text-white hover:bg-orange-600 transition"
          >
            View All Products
          </Link>
        </div>
      </div>
    </section>
  );
};

export default SpotlightSection;
