"use client";

import { useState } from 'react';

const SolutionsContent = () => {
  const [activeTab, setActiveTab] = useState('office');

  const solutions = [
    {
      id: 'office',
      title: 'Office Management System',
      description: 'Secure access control for office environments. Our office management system provides comprehensive security features including access control, time and attendance tracking, and visitor management. Perfect for businesses of all sizes.',
    },
    {
      id: 'school',
      title: 'School Management System',
      description: 'Protect students and staff with our school management system. Features include attendance tracking, secure access to facilities, and emergency protocols. Designed specifically for educational institutions.',
    },
    {
      id: 'visitor',
      title: 'Visitor Management System',
      description: 'Streamline visitor check-in processes while enhancing security. Our visitor management system includes pre-registration, ID verification, and automated notifications to hosts.',
    },
    {
      id: 'finance',
      title: 'Finance Management System',
      description: 'Secure transactions and identity verification for financial institutions. Our finance management system includes multi-factor authentication and fraud prevention features.',
    },
    {
      id: 'cloud',
      title: 'Cloud Attendance and Payroll Software',
      description: 'Integrate attendance tracking with payroll processing in the cloud. Our software automates wage calculations based on attendance data, reducing errors and saving time.',
    },
  ];

  return (
    <div className="bg-white py-8 md:py-12">
      <div className="container mx-auto px-4">
        {/* Tabs Navigation */}
        <div className="flex overflow-x-auto pb-4 mb-6 md:mb-8 scrollbar-hide">
          <div className="flex space-x-3 md:space-x-4 min-w-max">
            {solutions.map((solution) => (
              <button
                key={solution.id}
                className={`px-4 py-2 rounded-full text-sm md:text-base whitespace-nowrap transition-colors ${
                  activeTab === solution.id
                    ? 'bg-yellow-400 text-gray-800 font-medium'
                    : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                }`}
                onClick={() => setActiveTab(solution.id)}
              >
                {solution.title}
              </button>
            ))}
          </div>
        </div>

        {/* Content Sections - Always show all sections for Figma match */}
        <div className="space-y-8 md:space-y-10">
          {solutions.map((solution, index) => (
            <div
              key={solution.id}
              className="bg-white rounded-lg border border-gray-100 p-5 md:p-6"
            >
              <div className="flex items-start mb-4">
                <div className="flex-shrink-0 w-8 h-8 rounded-full bg-orange-50 text-orange-500 flex items-center justify-center mr-3">
                  <span className="font-semibold">{index + 1}</span>
                </div>
                <h3 className="text-lg md:text-xl font-semibold">{solution.title}</h3>
              </div>
              <p className="text-gray-600 ml-11">{solution.description}</p>
            </div>
          ))}
        </div>
      </div>
    </div>
  );
};

export default SolutionsContent;