// components/blog/SingleBlogPage.tsx
"use client";

import React from "react";
import Image from "next/image";
import Link from "next/link";
import { Blog } from "@/types/blog";
import { baseUri } from "@/services/constant";

interface SingleBlogPageProps {
  blog: Blog;
}

const SingleBlogPage: React.FC<SingleBlogPageProps> = ({ blog }) => {
  const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString("en-IN", {
      year: "numeric",
      month: "long",
      day: "numeric",
      hour: "2-digit",
      minute: "2-digit",
    });
  };

  // Convert HTML content to React elements safely
  const createMarkup = (htmlContent: string) => {
    return { __html: htmlContent };
  };

  return (
    <article className="bg-white">
      {/* Hero image in centered container */}
      <div className="container mx-auto px-4">
        <div className="max-w-3xl mx-auto">
          <div className="relative aspect-[16/9] rounded-lg overflow-hidden border border-gray-200 bg-gray-50">
        <Image
          src={blog.featured_image ? `${baseUri}${blog.featured_image}` : "/images/blog1.png"}
          alt={blog.title}
          fill
          className="object-cover"
          unoptimized
        />
        
        <div className="hidden">
          <div className="container mx-auto px-4 pb-8">
            <div className="max-w-4xl">
              <div className="flex items-center gap-3 mb-3">
                <span className="inline-block bg-white/20 backdrop-blur text-white text-xs px-3 py-1 rounded-full">
                  {blog.category}
                </span>
                {blog.reading_time ? (
                  <span className="text-white/90 text-xs">{blog.reading_time} min read</span>
                ) : null}
              </div>
              <h1 className="text-white text-3xl md:text-5xl font-semibold leading-tight">
                {blog.title}
              </h1>
              <div className="mt-3 text-white/90 text-sm">
                Published {formatDate(blog.published_at)}
              </div>
            </div>
          </div>
        </div>
          </div>
        </div>
      </div>

      {/* New header below hero */}
      <header className="container mx-auto px-4 pt-8 pb-6">
        <div className="max-w-3xl mx-auto">
          <div className="flex items-center gap-3 mb-4">
            {blog.category ? (
              <span className="inline-block px-3 py-1 rounded-full bg-orange-50 text-orange-700 border border-orange-200 text-xs">
                {blog.category}
              </span>
            ) : null}
            {blog.reading_time ? (
              <span className="text-gray-500 text-xs">{blog.reading_time} min read</span>
            ) : null}
          </div>
          <h1 className="text-3xl md:text-4xl font-bold text-gray-900">
            {blog.title}
          </h1>
          <div className="mt-3 text-gray-600 text-sm">
            {blog.author?.name ? (
              <>
                By <span className="font-medium text-gray-900">{blog.author.name}</span> ·{" "}
              </>
            ) : null}
            <span>{formatDate(blog.published_at)}</span>
          </div>
        </div>
      </header>

      {/* Article body */}
      <div className="container mx-auto px-4 pb-16">
        <div className="mx-auto max-w-3xl">
          {/* Author */}
          {blog.author?.name ? (
            <div className="flex items-center gap-4 mt-8 mb-8">
              <div className="w-12 h-12 rounded-full bg-orange-500 text-white flex items-center justify-center font-semibold">
                {(blog.author?.name || "").charAt(0)}
              </div>
              <div>
                <p className="font-medium text-gray-900">{blog.author?.name}</p>
              </div>
            </div>
          ) : null}

          {/* Excerpt removed from detail page */}

          {/* Content */}
          <div className="prose prose-lg text-justify max-w-none mt-6 prose-headings:text-gray-900 prose-a:text-orange-600 prose-strong:text-gray-900 prose-blockquote:border-l-orange-400 prose-img:rounded-lg">
            <div dangerouslySetInnerHTML={createMarkup(blog.content)} />
          </div>

          {/* Tags */}
          {blog.tags?.length ? (
            <div className="mt-8 pt-6 border-t border-gray-200">
              <div className="flex flex-wrap gap-2">
                {blog.tags.map((tag) => (
                  <span key={tag} className="inline-block bg-orange-50 text-orange-700 text-sm px-3 py-1 rounded-full border border-orange-200">#{tag}</span>
                ))}
              </div>
            </div>
          ) : null}

          {/* Footer actions */}
          <div className="mt-10 flex items-center justify-between">
            <Link
              href="/blog"
              className="inline-flex items-center gap-2 px-5 py-2.5 bg-orange-500 text-white rounded-md hover:bg-orange-600 transition">
              <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M10 19l-7-7m0 0l7-7m-7 7h18" />
              </svg>
              Back to Blogs
            </Link>
            <div className="text-sm text-gray-500">Last updated {formatDate(blog.updated_at)}</div>
          </div>
        </div>
      </div>
    </article>
  );
};

export default SingleBlogPage;
