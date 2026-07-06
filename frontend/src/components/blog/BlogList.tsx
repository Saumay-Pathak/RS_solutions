// components/blog/BlogList.tsx
'use client';

import React, { useState, useEffect, useCallback } from 'react';
import { blogService } from '@/services/blogService';
import { Blog, BlogFilters as BlogFiltersType } from '@/types/blog';
import BlogCard from './BlogCard';
import BlogFilters from './BlogFilters';

const BlogList: React.FC = () => {
  const [blogs, setBlogs] = useState<Blog[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [categories, setCategories] = useState<string[]>([]);
  const [filters, setFilters] = useState<BlogFiltersType>({});

  // Debounced API call
  const fetchBlogs = useCallback(async (currentFilters: BlogFiltersType) => {
    try {
      setLoading(true);
      setError(null);
      const response = await blogService.getBlogs(currentFilters);
      setBlogs(response.data);
    } catch (err) {
      setError('Failed to fetch blogs. Please try again later.');
      console.error('Error fetching blogs:', err);
    } finally {
      setLoading(false);
    }
  }, []);

  // Debounce effect for filters
  useEffect(() => {
    const timer = setTimeout(() => {
      fetchBlogs(filters);
    }, 300); // 300ms debounce

    return () => clearTimeout(timer);
  }, [filters, fetchBlogs]);

  // Initial data and categories
  useEffect(() => {
    const initializeData = async () => {
      try {
        setLoading(true);
        const [blogsResponse] = await Promise.all([
          blogService.getBlogs(),
        ]);
        
        setBlogs(blogsResponse.data);
        const uniqueCategories = [...new Set(blogsResponse.data.map(blog => blog.category))];
        setCategories(uniqueCategories);
      } catch (err) {
        setError('Failed to load blogs.');
        console.error('Error initializing data:', err);
      } finally {
        setLoading(false);
      }
    };

    initializeData();
  }, []);

  const handleFilterChange = useCallback((newFilters: BlogFiltersType) => {
    setFilters(newFilters);
  }, []);

  const retryLoading = () => {
    fetchBlogs(filters);
  };

  if (error) {
    return (
      <div className="text-center py-12">
        <div className="bg-red-50 border border-red-200 rounded-xl p-8 max-w-md mx-auto">
          <div className="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg className="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <h3 className="text-lg font-semibold text-red-800 mb-2">Unable to load blogs</h3>
          <p className="text-red-600 mb-6">{error}</p>
          <button
            onClick={retryLoading}
            className="px-6 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium"
          >
            Try Again
          </button>
        </div>
      </div>
    );
  }

  return (
    <div>
      {/* Professional Filters */}
      <BlogFilters
        onFilterChange={handleFilterChange}
        categories={categories}
        loading={loading}
      />

      {/* Results Summary */}
      {!loading && blogs.length > 0 && (
        <div className="mb-6">
          <p className="text-gray-600 text-sm">
            Found {blogs.length} blog{blogs.length !== 1 ? 's' : ''}
            {filters.category && (
              <span> in <span className="font-semibold text-gray-800">{filters.category}</span></span>
            )}
            {filters.search && (
              <span> matching &quot;<span className="font-semibold text-gray-800">{filters.search}</span>&quot;</span>
            )}
          </p>
        </div>
      )}

      {/* Loading Skeleton */}
      {loading && (
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          {[...Array(6)].map((_, index) => (
            <div key={index} className="bg-white rounded-xl shadow-sm border border-gray-200 p-6 animate-pulse">
              <div className="flex justify-between mb-4">
                <div className="h-6 bg-gray-200 rounded w-20"></div>
                <div className="h-5 bg-gray-200 rounded w-16"></div>
              </div>
              <div className="h-6 bg-gray-200 rounded w-3/4 mb-3"></div>
              <div className="space-y-2 mb-4">
                <div className="h-4 bg-gray-200 rounded"></div>
                <div className="h-4 bg-gray-200 rounded w-5/6"></div>
              </div>
              <div className="flex items-center justify-between pt-4 border-t border-gray-100">
                <div className="flex items-center">
                  <div className="w-8 h-8 bg-gray-200 rounded-full"></div>
                  <div className="ml-3 space-y-1">
                    <div className="h-3 bg-gray-200 rounded w-16"></div>
                    <div className="h-2 bg-gray-200 rounded w-12"></div>
                  </div>
                </div>
                <div className="h-4 bg-gray-200 rounded w-12"></div>
              </div>
            </div>
          ))}
        </div>
      )}

      {/* Empty State */}
      {!loading && blogs.length === 0 && (
        <div className="text-center py-16">
          <div className="bg-gray-50 rounded-2xl p-12 max-w-md mx-auto">
            <div className="w-16 h-16 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-4">
              <svg className="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
              </svg>
            </div>
            <h3 className="text-xl font-semibold text-gray-900 mb-2">No blogs found</h3>
            <p className="text-gray-500 mb-6">
              {filters.search || filters.category 
                ? "Try adjusting your search criteria or filters" 
                : "Check back later for new content"
              }
            </p>
            {(filters.search || filters.category) && (
              <button
                onClick={() => setFilters({})}
                className="px-6 py-2.5 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-colors font-medium"
              >
                View All Blogs
              </button>
            )}
          </div>
        </div>
      )}

      {/* Blog Grid */}
      {!loading && blogs.length > 0 && (
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          {blogs.map((blog) => (
            <BlogCard key={blog.id} blog={blog} />
          ))}
        </div>
      )}
    </div>
  );
};

export default BlogList;