// components/career/CareerList.tsx
'use client';

import React, { useState, useEffect, useCallback } from 'react';
import { careerService, JobOpening } from '@/services/careerService';
import CareerCard from './CareerCard';
import CareerFilters from './CareerFilters';

const CareerList: React.FC = () => {
  const [jobs, setJobs] = useState<JobOpening[]>([]);
  const [loading, setLoading] = useState(true);
  const [hasMore, setHasMore] = useState(true);
  const loaderRef = React.useRef<HTMLDivElement | null>(null);
  const [filters, setFilters] = useState({
    employment_type: '',
    location: '',
    search: '',
    page: 1,
    per_page: 9,
  });


  const fetchJobs = useCallback(async (pageToFetch: number, isNewFilter: boolean = false) => {
    try {
      setLoading(true);
      const response = await careerService.getActiveJobOpenings({ ...filters, page: pageToFetch });

      if (response.success) {
        setJobs(prev => isNewFilter ? response.data : [...prev, ...response.data]);
        if (response.meta) {
          setHasMore(response.meta.current_page < response.meta.last_page);
        }
      }
    } catch (error) {
      console.error('Error fetching jobs:', error);
      setJobs([]);
    } finally {
      setLoading(false);
    }
  }, [filters]);

  useEffect(() => {
    fetchJobs(filters.page, filters.page === 1);
  }, [filters.page, filters.employment_type, filters.location, filters.search, fetchJobs]);

  const handleFilterChange = (newFilters: Partial<typeof filters>) => {
    setFilters(prev => ({ ...prev, ...newFilters, page: 1 }));
  };

  useEffect(() => {
    if (!loaderRef.current) return;
    const observer = new IntersectionObserver(
      (entries) => {
        const first = entries[0];
        if (first.isIntersecting && hasMore && !loading) {
          setFilters((prev) => ({ ...prev, page: prev.page + 1 }));
        }
      },
      { threshold: 1 }
    );

    observer.observe(loaderRef.current);
    return () => observer.disconnect();
  }, [hasMore, loading]);

  const clearFilters = () => {
    setFilters({
      employment_type: '',
      location: '',
      search: '',
      page: 1,
      per_page: 9,
    });
  };

  if (loading) {
    return (
      <div className="space-y-6">
        {/* Filters Skeleton */}
        <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6 animate-pulse">
          <div className="h-6 bg-gray-200 rounded w-32 mb-4"></div>
          <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div className="h-10 bg-gray-200 rounded"></div>
            <div className="h-10 bg-gray-200 rounded"></div>
            <div className="h-10 bg-gray-200 rounded"></div>
          </div>
        </div>

        {/* Job Cards Skeleton */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          {[...Array(6)].map((_, index) => (
            <div key={index} className="bg-white rounded-xl shadow-sm border border-gray-200 p-6 animate-pulse">
              <div className="flex items-center justify-between mb-4">
                <div className="h-6 bg-gray-200 rounded w-24"></div>
                <div className="h-5 bg-gray-200 rounded w-20"></div>
              </div>
              <div className="h-8 bg-gray-200 rounded w-3/4 mb-3"></div>
              <div className="space-y-2 mb-4">
                <div className="h-4 bg-gray-200 rounded"></div>
                <div className="h-4 bg-gray-200 rounded w-5/6"></div>
                <div className="h-4 bg-gray-200 rounded w-4/5"></div>
              </div>
              <div className="h-10 bg-gray-200 rounded"></div>
            </div>
          ))}
        </div>
      </div>
    );
  }

  return (
    <div className="space-y-6">
      {/* Filters */}
      <CareerFilters
        filters={filters}
        onFilterChange={handleFilterChange}
        onClearFilters={clearFilters}
      />

      {/* Results Count */}
      <div className="text-center">
        <p className="text-gray-600">
          Showing <span className="font-semibold text-gray-900">{jobs.length}</span> of{' '}
          {filters.employment_type && (
            <span> in <span className="font-semibold text-gray-800">{filters.employment_type}</span></span>
          )}
          {filters.location && (
            <span> in <span className="font-semibold text-gray-800">{filters.location}</span></span>
          )}
          {filters.search && (
            <span> matching &quot;<span className="font-semibold text-gray-800">{filters.search}</span>&quot;</span>
          )}
        </p>
      </div>

      {/* Empty State */}
      {!loading && jobs.length === 0 && (
        <div className="text-center py-16">
          <div className="bg-gray-50 rounded-2xl p-12 max-w-md mx-auto">
            <div className="w-16 h-16 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-4">
              <svg className="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
              </svg>
            </div>
            <h3 className="text-xl font-semibold text-gray-900 mb-2">No positions found</h3>
            <p className="text-gray-500 mb-6">
              {filters.search || filters.employment_type || filters.location
                ? "Try adjusting your search criteria or filters"
                : "No active positions available at the moment. Check back later!"
              }
            </p>
            {(filters.search || filters.employment_type || filters.location) && (
              <button
                onClick={clearFilters}
                className="px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium"
              >
                View All Positions
              </button>
            )}
          </div>
        </div>
      )}

      {/* Job Grid */}
      {!loading && jobs.length > 0 && (
        <>
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {jobs.map((job) => (
              <CareerCard key={job.id} job={job} />
            ))}
          </div>
        </>
      )}

      {/* Loader for infinite scroll */}
      {loading && <div className="text-center py-4">Loading more...</div>}
      <div ref={loaderRef} className="h-10" />
    </div>
  );
};

export default CareerList;