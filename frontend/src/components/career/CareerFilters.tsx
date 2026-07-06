// components/career/CareerFilters.tsx
import React, { useState, useRef, useEffect } from 'react';

interface CareerFiltersProps {
  filters: {
    employment_type: string;
    location: string;
    search: string;
    page: number;
    per_page: number;
  };
  onFilterChange: (filters: Partial<CareerFiltersProps['filters']>) => void;
  onClearFilters: () => void;
}

const CareerFilters: React.FC<CareerFiltersProps> = ({ 
  filters, 
  onFilterChange, 
  onClearFilters 
}) => {
  const [isEmploymentOpen, setIsEmploymentOpen] = useState(false);
  const [isLocationOpen, setIsLocationOpen] = useState(false);
  const employmentRef = useRef<HTMLDivElement>(null);
  const locationRef = useRef<HTMLDivElement>(null);

  const employmentTypes = [
    { value: '', label: 'All Types' },
    { value: 'Full-time', label: 'Full-time' },
    { value: 'Part-time', label: 'Part-time' },
    { value: 'Contract', label: 'Contract' },
    { value: 'Internship', label: 'Internship' },
  ];

  const locations = [
    { value: '', label: 'All Locations' },
    { value: 'Noida Uttar Pradesh', label: 'Noida, UP' },
    { value: 'Delhi', label: 'Delhi' },
    { value: 'Mumbai', label: 'Mumbai' },
    { value: 'Bangalore', label: 'Bangalore' },
    { value: 'Remote', label: 'Remote' },
  ];

  const hasActiveFilters = filters.employment_type || filters.location || filters.search;

  // Close dropdowns when clicking outside
  useEffect(() => {
    const handleClickOutside = (event: MouseEvent) => {
      if (employmentRef.current && !employmentRef.current.contains(event.target as Node)) {
        setIsEmploymentOpen(false);
      }
      if (locationRef.current && !locationRef.current.contains(event.target as Node)) {
        setIsLocationOpen(false);
      }
    };

    document.addEventListener('mousedown', handleClickOutside);
    return () => document.removeEventListener('mousedown', handleClickOutside);
  }, []);

  return (
    <div className="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
      <div className="flex items-center justify-between mb-4">
        <h3 className="text-lg font-semibold text-gray-900">Filter Positions</h3>
        {hasActiveFilters && (
          <button
            onClick={onClearFilters}
            className="text-sm text-orange-600 hover:text-orange-800 font-medium transition-all duration-200 hover:bg-orange-50 px-3 py-1 rounded-lg"
          >
            Clear All
          </button>
        )}
      </div>

      <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
        {/* Search */}
        <div>
          <label htmlFor="search" className="block text-sm font-medium text-gray-700 mb-2">
            Search Position
          </label>
          <div className="relative">
            <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <svg className="h-5 w-5 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </div>
            <input
              type="text"
              id="search"
              value={filters.search}
              onChange={(e) => onFilterChange({ search: e.target.value })}
              placeholder="Search by title or description..."
              className="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm transition-all duration-200 hover:border-gray-400 focus:shadow-md"
            />
          </div>
        </div>

        {/* Employment Type */}
        <div ref={employmentRef} className="relative">
          <label htmlFor="employment_type" className="block text-sm font-medium text-gray-700 mb-2">
            Employment Type
          </label>
          <button
            type="button"
            onClick={() => setIsEmploymentOpen(!isEmploymentOpen)}
            className="w-full flex items-center justify-between px-4 py-3 text-left bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm hover:border-gray-400 transition-all duration-200 shadow-sm hover:shadow-md"
          >
            <span className="text-gray-900">
              {employmentTypes.find(type => type.value === filters.employment_type)?.label || 'All Types'}
            </span>
            <svg className={`w-4 h-4 text-gray-400 transition-transform ${isEmploymentOpen ? 'rotate-180' : ''}`} fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 9l-7 7-7-7" />
            </svg>
          </button>
          
          {isEmploymentOpen && (
            <div className="absolute z-10 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-60 overflow-auto transform origin-top transition-all duration-200 ease-out animate-in fade-in slide-in-from-top-2">
              {employmentTypes.map((type) => (
                <button
                  key={type.value}
                  onClick={() => {
                    onFilterChange({ employment_type: type.value });
                    setIsEmploymentOpen(false);
                  }}
                  className={`w-full text-left px-4 py-3 text-sm hover:bg-gray-50 transition-colors ${
                    filters.employment_type === type.value 
                      ? 'bg-orange-50 text-orange-700 font-medium' 
                      : 'text-gray-700'
                  }`}
                >
                  {type.label}
                </button>
              ))}
            </div>
          )}
        </div>

        {/* Location */}
        <div ref={locationRef} className="relative">
          <label htmlFor="location" className="block text-sm font-medium text-gray-700 mb-2">
            Location
          </label>
          <button
            type="button"
            onClick={() => setIsLocationOpen(!isLocationOpen)}
            className="w-full flex items-center justify-between px-4 py-3 text-left bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm hover:border-gray-400 transition-all duration-200 shadow-sm hover:shadow-md"
          >
            <span className="text-gray-900">
              {locations.find(location => location.value === filters.location)?.label || 'All Locations'}
            </span>
            <svg className={`w-4 h-4 text-gray-400 transition-transform ${isLocationOpen ? 'rotate-180' : ''}`} fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 9l-7 7-7-7" />
            </svg>
          </button>
          
          {isLocationOpen && (
            <div className="absolute z-10 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-60 overflow-auto transform origin-top transition-all duration-200 ease-out animate-in fade-in slide-in-from-top-2">
              {locations.map((location) => (
                <button
                  key={location.value}
                  onClick={() => {
                    onFilterChange({ location: location.value });
                    setIsLocationOpen(false);
                  }}
                  className={`w-full text-left px-4 py-3 text-sm hover:bg-gray-50 transition-colors ${
                    filters.location === location.value 
                      ? 'bg-orange-50 text-orange-700 font-medium' 
                      : 'text-gray-700'
                  }`}
                >
                  {location.label}
                </button>
              ))}
            </div>
          )}
        </div>
      </div>

      {/* Active Filters Display */}
      {hasActiveFilters && (
        <div className="mt-4 pt-4 border-t border-gray-200 animate-in fade-in duration-300">
          <div className="flex flex-wrap gap-2">
            {filters.employment_type && (
              <span className="inline-flex items-center px-3 py-2 rounded-full text-xs font-medium bg-orange-100 text-orange-800 border border-orange-200 shadow-sm">
                <span className="w-2 h-2 bg-orange-500 rounded-full mr-2"></span>
                {filters.employment_type}
                <button
                  onClick={() => onFilterChange({ employment_type: '' })}
                  className="ml-2 text-orange-600 hover:text-orange-800 hover:bg-orange-200 rounded-full p-1 transition-all"
                >
                  <svg className="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </span>
            )}
            {filters.location && (
              <span className="inline-flex items-center px-3 py-2 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200 shadow-sm">
                <span className="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                {filters.location}
                <button
                  onClick={() => onFilterChange({ location: '' })}
                  className="ml-2 text-green-600 hover:text-green-800 hover:bg-green-200 rounded-full p-1 transition-all"
                >
                  <svg className="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </span>
            )}
            {filters.search && (
              <span className="inline-flex items-center px-3 py-2 rounded-full text-xs font-medium bg-purple-100 text-purple-800 border border-purple-200 shadow-sm">
                <span className="w-2 h-2 bg-purple-500 rounded-full mr-2"></span>
                Search: {filters.search}
                <button
                  onClick={() => onFilterChange({ search: '' })}
                  className="ml-2 text-purple-600 hover:text-purple-800 hover:bg-purple-200 rounded-full p-1 transition-all"
                >
                  <svg className="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </span>
            )}
          </div>
        </div>
      )}
    </div>
  );
};

export default CareerFilters;