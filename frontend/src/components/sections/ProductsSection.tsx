"use client";

import Image from "next/image";
import Link from "next/link";
import Slider from "../ui/Slider";
import { useEffect, useState } from "react";
import { getProductsWithoutPagination } from "@/services/productService";
import { baseUri } from "@/services/constant";

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
const ProductsSection = () => {
  const [products, setProducts] = useState<Product[]>([]);

  useEffect(() => {
    async function fetchData() {
      try {
        const resp = await getProductsWithoutPagination();
        setProducts(resp.data);
      } catch (err) {
        console.error(err);
      }
    }
    fetchData();
  }, []);

  return (
    <section className="py-5 md:py-20 lg:py-0 bg-white">
      <div className="container mx-auto px-4">
        <div className="text-center mb-3 md:mb-12">
          <h2 className="section-title mb-0 md:mb-4">Featured Products</h2>
          <p className="section-subtitle uppercase">OUR BEST-IN-CLASS SECURITY PRODUCTS</p>
        </div>

        <div>
          <Slider
            autoPlay={true}
            autoPlayInterval={2000}
            showArrows={false}
            showDots={false}
            slidesToShow={3.4}
            className="h-full"
            responsive={[
              {
                breakpoint: 992,
                showDots: false,
                slidesToShow: 2,
              },
            ]}>
            {products &&
              products.map((product, index) => (
                <Link key={product.id} href={`/products/${product?.slug}`}>
                  <div
                    
                    className="rounded-lg sm:rounded-3xl p-3 sm:p-6 md:p-8 transition-transform mx-2 md:mx-4"
                    style={{
                      background:
                        index % 2 === 0
                          ? "linear-gradient(to bottom, #FFCC33, #FFB347)"
                          : "linear-gradient(to bottom, #FF7F50, #FF6347)",
                    }}>
                    <div className="flex flex-col">
                      <div className="relative h-[120px] sm:h-80 w-full mb-4 bg-white rounded-lg sm:rounded-xl pt-2 sm:pt-12  flex align-middle justify-center">
                        <Image
                          src={`${baseUri}${product.images[0]}`}
                          alt={product.title}
                          width={0}
                          height={0}
                          unoptimized
                          className="h-[100px] lg:h-[200px] w-[100px] lg:w-[200px] object-contain"
                        />
                      </div>
                      <div
                        style={{
                          color: index % 2 === 0 ? "#000" : "#fff",
                        }}>
                        <p className="text-xs sm:text-lg md:text-xl font-thin sm:mb-1 line-clamp-1">
                          {product.category?.name.charAt(0).toLocaleUpperCase()}
                          {product?.category?.name?.slice(1)}
                        </p>
                        <h3 className="text-sm sm:text-2xl md:text-3xl font-thin tracking-[0.5px] md:tracking-[1px] line-clamp-1">
                          {product.title.charAt(0).toUpperCase()}
                          {product.title.slice(1)}
                        </h3>
                      </div>
                    </div>
                  </div>
                </Link>
              ))}
          </Slider>
        </div>

        <div className="text-center mt-3 sm:mt-10">
          <Link
            href="/products"
            className="inline-flex items-center bg-orange-500 text-xs sm:text-lg text-white px-4 sm:px-6 py-2 sm:py-2 rounded-md font-medium hover:bg-orange-600 transition">
            VIEW ALL
            <svg
              xmlns="http://www.w3.org/2000/svg"
              className="h-4 w-4 ml-2"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor">
              <path
                strokeLinecap="round"
                strokeLinejoin="round"
                strokeWidth={2}
                d="M14 5l7 7m0 0l-7 7m7-7H3"
              />
            </svg>
          </Link>
        </div>
      </div>
    </section>
  );
};

export default ProductsSection;
