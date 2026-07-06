// components/blog/BlogFilters.tsx
"use client";

import React, { useState, useEffect } from "react";
import { BlogFilters as BlogFiltersType } from "@/types/blog";

interface BlogFiltersProps {
  onFilterChange: (filters: BlogFiltersType) => void;
  categories: string[];
  loading?: boolean;
}

const BlogFilters: React.FC<BlogFiltersProps> = ({
  onFilterChange,
  categories,
  loading = false,
}) => {
  const [localFilters, setLocalFilters] = useState<BlogFiltersType>({
    category: "",
    search: "",
    sort_by: "published_at",
  });
  const [searchInput, setSearchInput] = useState("");

  // Debounce search input (500ms delay)
  useEffect(() => {
    const timer = setTimeout(() => {
      setLocalFilters((prev) => ({ ...prev, search: searchInput }));
    }, 500);

    return () => clearTimeout(timer);
  }, [searchInput]);

  // Apply filters to parent with debounce
  useEffect(() => {
    onFilterChange(localFilters);
  }, [localFilters, onFilterChange]);

  const handleCategoryChange = (category: string) => {
    setLocalFilters((prev) => ({ ...prev, category }));
  };

  const handleSortChange = (sort_by: string) => {
    setLocalFilters((prev) => ({ ...prev, sort_by }));
  };

  const clearAllFilters = () => {
    setLocalFilters({ category: "", search: "", sort_by: "published_at" });
    setSearchInput("");
  };

  const hasActiveFilters = localFilters.category || localFilters.search;

  return (
    <div className="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-8">
      {/* Mobile View - Stacked Layout */}
      <div className="lg:hidden space-y-4">
        {/* Search Bar */}
        <div className="relative">
          <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg
              className="h-5 w-5 text-gray-400"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24">
              <path
                strokeLinecap="round"
                strokeLinejoin="round"
                strokeWidth={2}
                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
              />
            </svg>
          </div>
          <input
            type="text"
            value={searchInput}
            onChange={(e) => setSearchInput(e.target.value)}
            placeholder="Search blogs..."
            className="w-full pl-10 text-black pr-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent bg-gray-50"
            disabled={loading}
          />
        </div>

        <div className="flex gap-3">
          {/* Category Filter */}
          <div className="flex-1">
            <select
              value={localFilters.category || ""}
              onChange={(e) => handleCategoryChange(e.target.value)}
              className="w-full px-3 py-3 border text-black border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent bg-gray-50"
              disabled={loading}>
              <option value="">All Categories</option>
              {categories.map((category) => (
                <option key={category} value={category}>
                  {category}
                </option>
              ))}
            </select>
          </div>

          {/* Sort Filter */}
          <div className="flex-1">
            <select
              value={localFilters.sort_by || "published_at"}
              onChange={(e) => handleSortChange(e.target.value)}
              className="w-full px-3 py-3 text-black border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent bg-gray-50"
              disabled={loading}>
              <option value="published_at">Newest</option>
              <option value="title">Title A-Z</option>
              <option value="reading_time">Reading Time</option>
            </select>
          </div>
        </div>

        {/* Clear Button - Mobile */}
        {hasActiveFilters && (
          <button
            onClick={clearAllFilters}
            className="w-full px-4 py-3 text-gray-600 hover:text-gray-800 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors font-medium"
            disabled={loading}>
            Clear Filters
          </button>
        )}
      </div>

      {/* Desktop View - Horizontal Layout */}
      <div className="hidden lg:flex items-end gap-4" style={{justifyContent:"space-between"}}>
        {/* Search Bar */}
        <div className="flex-1 max-w-md">
          <label
            htmlFor="search"
            className="block text-sm font-medium text-gray-700 mb-2">
            Search
          </label>
          <div className="relative">
            <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <svg
                className="h-5 w-5 text-gray-400"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24">
                <path
                  strokeLinecap="round"
                  strokeLinejoin="round"
                  strokeWidth={2}
                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                />
              </svg>
            </div>
            <input
              type="text"
              id="search"
              value={searchInput}
              onChange={(e) => setSearchInput(e.target.value)}
              placeholder="Search by title, content..."
              className="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent bg-gray-50"
              disabled={loading}
            />
          </div>
        </div>

        <div className="flex gap-3 items-center" >
          {/* Category Filter */}
          <div className="w-48">
            <label
              htmlFor="category"
              className="block text-sm font-medium text-gray-700 mb-2">
              Category
            </label>
            <select
              id="category"
              value={localFilters.category || ""}
              onChange={(e) => handleCategoryChange(e.target.value)}
              className="w-full px-3 py-2.5 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent bg-gray-50"
              disabled={loading}>
              <option value="">All Categories</option>
              {categories.map((category) => (
                <option key={category} value={category}>
                  {category}
                </option>
              ))}
            </select>
          </div>

          {/* Sort Filter */}
          <div className="w-40">
            <label
              htmlFor="sort_by"
              className="block text-sm font-medium text-gray-700 mb-2">
              Sort by
            </label>
            <select
              id="sort_by"
              value={localFilters.sort_by || "published_at"}
              onChange={(e) => handleSortChange(e.target.value)}
              className="w-full px-3 py-2.5 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent bg-gray-50"
              disabled={loading}>
              <option value="published_at">Newest First</option>
              <option value="title">Title A-Z</option>
              <option value="reading_time">Reading Time</option>
            </select>
          </div>

          {/* Clear Button - Desktop */}
          {hasActiveFilters && (
            <button
              onClick={clearAllFilters}
              className="px-6 mt-7 py-2.5 text-gray-600 hover:text-gray-800 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors font-medium whitespace-nowrap"
              disabled={loading}>
              Clear All
            </button>
          )}
        </div>
      </div>

      {/* Active Filters Indicator */}
      {hasActiveFilters && (
        <div className="mt-4 pt-4 border-t border-gray-100">
          <div className="flex items-center gap-2 text-sm">
            <span className="text-gray-500">Active filters:</span>
            {localFilters.search && (
              <span className="inline-flex items-center gap-1 bg-orange-50 text-orange-700 px-2 py-1 rounded-full text-xs">
                Search: &quot;{localFilters.search}&quot;
                <button
                  onClick={() => setSearchInput("")}
                  className="hover:text-orange-900">
                  ×
                </button>
              </span>
            )}
            {localFilters.category && (
              <span className="inline-flex items-center gap-1 bg-green-50 text-green-700 px-2 py-1 rounded-full text-xs">
                Category: {localFilters.category}
                <button
                  onClick={() => handleCategoryChange("")}
                  className="hover:text-green-900">
                  ×
                </button>
              </span>
            )}
          </div>
        </div>
      )}
    </div>
  );
};

export default BlogFilters;
