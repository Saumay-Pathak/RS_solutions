"use client";

import Image from "next/image";
import Link from "next/link";
import Slider from "../ui/Slider";
import { useEffect, useState } from "react";
import { blogService } from "@/services/blogService";
import { Blog } from "@/types/blog";
import { baseUri } from "@/services/constant";

const BlogSection = () => {
  const [blogs, setBlogs] = useState<Blog[]>([]);
  const [loading, setLoading] = useState(true);

  // Fetch blogs
  useEffect(() => {
    const fetchBlogs = async () => {
      try {
        const response = await blogService.getPublishedBlogs();
        if (response?.success && Array.isArray(response.data)) {
          setBlogs(response.data);
        } else {
          setBlogs([]);
        }
      } catch (error) {
        console.error("Error fetching blogs:", error);
        setBlogs([]);
      } finally {
        setLoading(false);
      }
    };
    fetchBlogs();
  }, []);

  // Skeleton loader
  if (loading) {
    return (
      <section className="py-5 md:py-20 lg:py-0 bg-white">
        <div className="w-[85%] mx-auto px-4">
          <div className="animate-pulse">
            <div className="h-6 w-1/3 bg-gray-300 mb-3 rounded"></div>
            <div className="h-4 w-1/4 bg-gray-200 mb-6 rounded"></div>
            <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
              {[1, 2, 3, 4].map((i) => (
                <div
                  key={i}
                  className="bg-white rounded-2xl shadow-sm h-60"></div>
              ))}
            </div>
          </div>
        </div>
      </section>
    );
  }

  // No blogs case
  if (!blogs.length) {
    return (
      <section className="py-5 md:py-20 lg:py-0 bg-white text-center">
        <div className="w-[85%] mx-auto px-4">
          <h2 className="section-title">No Blogs Found</h2>
        </div>
      </section>
    );
  }

  return (
    <section className="py-5 md:py-20 lg:py-0 bg-white">
      <div className="w-[85%] mx-auto px-6">
        {/* Header */}
        <div className="mb-6 md:mb-10 text-center">
          <h2 className="section-title md:mb-4">Blogs</h2>
          <p className="section-subtitle max-w-3xl mx-auto">Our Latest Updates</p>
        </div>

        {/* Mobile Slider */}
        <div className="md:hidden">
          <Slider
            autoPlay
            autoPlayInterval={3000}
            showArrows={false}
            showDots
            slidesToShow={1}
            dotStyle={{ position: 'outside' }}
            responsive={[{ breakpoint: 640, slidesToShow: 1, showDots: true }]}
            className="pb-6">
            {blogs.map((post) => (
              <div
                key={post.id}
                className="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden mx-1 flex flex-col min-h-[280px]"
              >
                <div className="relative h-28 md:h-48">
                  <Image
                    src={
                      post.featured_image
                        ? `${baseUri}${post.featured_image}`
                        : "/images/blog1.png"
                    }
                    alt={post.title}
                    fill
                    className="object-contain"
                    unoptimized
                  />
                </div>
                <div className="p-2 md:p-4 flex flex-col gap-1 flex-1">
                  <h3 className="text-xs md:text-lg font-light text-black md:mb-2 line-clamp-2">
                    {post.title}
                  </h3>
                  <p className="text-[#4F423D] text-xs md:text-[16px] font-[300] md:mb-2 line-clamp-2">
                    {post.excerpt || ""}
                  </p>
                  <p className="text-gray-600 text-[10px] mb-0">
                    Posted on:{" "}
                    {new Date(
                      post.published_at || post.created_at
                    ).toLocaleDateString("en-US", {
                      month: "short",
                      day: "numeric",
                      year: "numeric",
                    })}
                  </p>
                  <Link
                    href={`/blog/${post.slug}`}
                    className="mt-auto inline-block bg-orange-500 text-white text-[10px] md:text-xs px-3 py-1.5 rounded-md font-medium hover:bg-orange-600 transition"
                    aria-label={`Read more about ${post.title}`}>
                    Read More
                  </Link>
                </div>
              </div>
            ))}
          </Slider>
        </div>

        {/* Desktop Grid */}
        <div className="hidden md:grid md:grid-cols-2 lg:grid-cols-4 gap-2 md:gap-6 mb-0">
          {blogs.map((post) => (
            <div
              key={post.id}
              className="bg-white rounded-4xl shadow-sm border border-gray-100 overflow-hidden"
            >
              <div className="relative h-60 lg:h-48 xl:h-60">
                <Image
                  src={
                    post.featured_image
                      ? `${baseUri}${post.featured_image}`
                      : "/images/blog1.png"
                  }
                  alt={post.title}
                  fill
                  className="object-contain"
                  unoptimized
                />
              </div>
              <div className="p-4">
                <h3 className="text-lg font-light text-black mb-2 line-clamp-2">
                  {post.title}
                </h3>
                <p className="text-[#4F423D] text-[14px] md:text-[16px] font-[400] mb-3 line-clamp-2">
                  {post.excerpt || ""}
                </p>
                <p className="text-[#9B918D] text-sm mb-2">
                  Posted on:{" "}
                  {new Date(
                    post.published_at || post.created_at
                  ).toLocaleDateString("en-US", {
                    month: "short",
                    day: "numeric",
                    year: "numeric",
                  })}
                </p>
                {/* <p className="text-[#9B918D] text-xs">
                  Author: {post.author?.name || "Unknown"}
                </p> */}
                <Link
                  href={`/blog/${post.slug}`}
                  className="mt-2 inline-block bg-orange-500 text-white text-xs md:text-sm px-4 py-2 rounded-md font-medium hover:bg-orange-600 transition"
                  aria-label={`Read more about ${post.title}`}>
                  Read More
                </Link>
              </div>
            </div>
          ))}
        </div>

        {/* View All Button */}
        <div className="text-center mt-0 md:mt-6">
          <Link
            href="/blog"
            className="bg-orange-500 text-white text-xs md:text-sm px-4 py-2 md:px-6 md:py-3 rounded-md font-medium hover:bg-orange-600 transition inline-flex items-center">
            View All
            <svg
              xmlns="http://www.w3.org/2000/svg"
              className="h-4 w-4 ml-1"
              viewBox="0 0 20 20"
              fill="currentColor">
              <path
                fillRule="evenodd"
                d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z"
                clipRule="evenodd"
              />
            </svg>
          </Link>
        </div>
      </div>
    </section>
  );
};

export default BlogSection;
