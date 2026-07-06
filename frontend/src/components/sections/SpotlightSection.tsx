"use client";

import { useCallback, useEffect, useState } from "react";
import Image from "next/image";
import Link from "next/link";
import Slider from "@/components/ui/Slider";
import { getFeaturedProducts } from "@/services/productService";
import { baseUri } from "@/services/constant";

type Product = {
  id: string;
  slug: string;
  title: string;
  description?: string;
  images: string[];
};

const sliderResponsive = [
  { breakpoint: 1400, slidesToShow: 3, showDots: true },
  { breakpoint: 1024, slidesToShow: 2, showDots: true },
  { breakpoint: 768, slidesToShow: 1, showDots: true },
];

const SpotlightSection = () => {
  const [products, setProducts] = useState<Product[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  const fetchProducts = useCallback(async () => {
    try {
      setLoading(true);
      setError(null);
      const resp = await getFeaturedProducts();
      setProducts(resp?.data ?? []);
    } catch (err) {
      console.error(err);
      setError("Unable to load spotlight products.");
    } finally {
      setLoading(false);
    }
  }, []);

  useEffect(() => {
    fetchProducts();
  }, [fetchProducts]);

  return (
    <section className="bg-white py-16">
      <div className="max-w-7xl mx-auto px-4">
        {/* Header */}
        <div className="text-center mb-10">
          <h2 className="section-title-long font-bold text-2xl sm:text-3xl text-gray-900">
            Spotlight Products
          </h2>
          <p className="mt-2 text-sm text-gray-600">
            Enhance Your Security with Cutting-Edge Technology
          </p>
        </div>

        {/* Loading */}
        {loading && (
          <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
            {[...Array(3)].map((_, i) => (
              <div
                key={i}
                className="h-80 rounded-lg bg-gray-100 animate-pulse"
              />
            ))}
          </div>
        )}

        {/* Error */}
        {error && (
          <div className="text-center py-8">
            <p className="text-sm text-red-600 mb-3">{error}</p>
            <button
              onClick={fetchProducts}
              className="text-sm font-medium text-orange-600 hover:underline"
            >
              Retry
            </button>
          </div>
        )}

        {/* Products */}
        {!loading && !error && (
          <Slider
            autoPlay
            autoPlayInterval={4500}
            showArrows={false}
            showDots
            slidesToShow={4}
            responsive={sliderResponsive}
            className="pb-4"
            dotStyle={{
              size: 6,
              activeSize: 10,
              color: "#D1D5DB",
              activeColor: "#EA5921",
              position: "outside",
            }}
          >
            {products.slice(0, 12).map((product) => (
              <Link
                key={product.id}
                href={`/products/${product.slug}`}
                className="group mx-2 block rounded-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500">
                <div className="px-1 py-2">
                  <article className="h-[430px] rounded-lg bg-white border border-gray-200 overflow-hidden transition-[transform,box-shadow] duration-500 ease-out will-change-transform hover:-translate-y-1 hover:shadow-xl">
                    {/* Image */}
                    <div className="relative h-72 bg-gray-100 flex items-center justify-center p-5 overflow-hidden">
                      {product.images?.[0] ? (
                        <Image
                          src={`${baseUri}${product.images[0]}`}
                          alt={product.title}
                          fill
                          sizes="(max-width: 768px) 100vw, 25vw"
                          className="object-contain mix-blend-multiply transition-transform duration-700 ease-out group-hover:scale-[1.06]"
                        />
                      ) : (
                        <span className="text-xs text-gray-400">
                          No image available
                        </span>
                      )}
                    </div>

                    {/* Content */}
                    <div className="p-4">
                      <h3 className="text-md font-semibold text-gray-900 line-clamp-1">
                        {product.title}
                      </h3>
                      <p className="mt-1 text-sm text-gray-600 line-clamp-2">
                        {product.description || "No description available."}
                      </p>

                      <div className="mt-3">
                        <span className="inline-flex w-full items-center justify-center rounded-md bg-orange-500 py-1 text-sm font-medium text-white transition-colors duration-300 ease-out group-hover:bg-orange-600">
                          Read More →
                        </span>
                      </div>
                    </div>
                  </article>
                </div>
              </Link>
            ))}
          </Slider>
        )}

        {/* View All */}
        <div className="text-center mt-2">
          <Link
            href="/products"
            className="inline-flex items-center text-sm font-medium text-gray-900 hover:text-orange-600 transition"
          >
            View all products
            <span className="ml-2">→</span>
          </Link>
        </div>
      </div>
    </section>
  );
};

export default SpotlightSection;
