// components/career/CareerCard.tsx
import React from 'react';
import { JobOpening } from '@/services/careerService';
import Link from 'next/link';

interface CareerCardProps {
  job: JobOpening;
}

const CareerCard: React.FC<CareerCardProps> = ({ job }) => {
  const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('en-IN', {
      year: 'numeric',
      month: 'short',
      day: 'numeric'
    });
  };

  const getEmploymentTypeColor = (type: string) => {
    switch (type.toLowerCase()) {
      case 'full-time':
        return 'bg-green-100 text-green-800';
      case 'part-time':
        return 'bg-blue-100 text-blue-800';
      case 'contract':
        return 'bg-purple-100 text-purple-800';
      case 'internship':
        return 'bg-yellow-100 text-yellow-800';
      default:
        return 'bg-gray-100 text-gray-800';
    }
  };

  const handleApplyNow = () => {
    // You can implement application logic here
    // For now, we'll open a mailto link or show contact info
    const subject = encodeURIComponent(`Application for ${job.title}`);
    const body = encodeURIComponent(
      `Dear Hiring Team,\n\nI am writing to apply for the ${job.title} position located in ${job.location}.\n\n[Please attach your resume and cover letter here]\n\nBest regards,\n[Your Name]`
    );
    window.location.href = `mailto:career@realtimebiometrics.com?subject=${subject}&body=${body}`;
  };

  return (
    <article className="bg-white rounded-lg shadow-md hover:shadow-xl hover:scale-100 transition-all duration-300 border border-gray-200 overflow-hidden group">
      <div className="p-6">
        {/* Job Type and Location */}
        <div className="flex items-center justify-between mb-4">
          <span className={`inline-block text-xs px-2 py-1 rounded-full font-medium ${getEmploymentTypeColor(job.employment_type)}`}>
            {job.employment_type}
          </span>
          <div className="flex items-center text-gray-500 text-xs">
            <svg className="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            {job.location}
          </div>
        </div>

        {/* Job Title */}
        <h2 className="text-lg font-bold text-black/70 mb-3 line-clamp-2 group-hover:text-orange-600 transition-colors">
          {job.title}
        </h2>

        {/* Job Description */}
        <div 
          className="text-gray-600 text-sm mb-4 line-clamp-3 leading-relaxed [&_*]:inline [&_*]:mr-1"
          dangerouslySetInnerHTML={{ __html: job.description }}
        />

        {/* Posted Date */}
        <div className="flex items-center justify-between pt-4 border-t border-gray-100">
          <div className="text-xs text-gray-500">
            Posted: {formatDate(job.created_at)}
          </div>
          <div className="text-xs text-gray-500">
            Last updated: {formatDate(job.updated_at)}
          </div>
        </div>
      </div>

      {/* Action Buttons */}
      <div className="px-6 pb-6 space-y-3">
        <Link
          href={`/careers/${job.id}`}
          className="w-full bg-white hover:bg-gray-50 text-gray-900 border border-gray-300 text-sm font-medium py-2 px-4 rounded-lg transition-colors duration-200 flex items-center justify-center group"
        >
          View Details
        </Link>
        <button
          onClick={handleApplyNow}
          className="w-full bg-orange-600 hover:bg-orange-700 text-white text-sm font-medium py-2.5 px-4 rounded-lg transition-colors duration-200 flex items-center justify-center group"
        >
          Apply Now
          <svg 
            className="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" 
            fill="none" 
            stroke="currentColor" 
            viewBox="0 0 24 24"
          >
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
          </svg>
        </button>
      </div>

      {/* Active Indicator */}
      {job.is_active && (
        <div className="absolute top-2 right-2">
          <div className="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
        </div>
      )}
    </article>
  );
};

export default CareerCard;