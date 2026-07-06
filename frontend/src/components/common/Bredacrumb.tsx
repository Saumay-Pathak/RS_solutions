// components/common/AdvancedBreadcrumb.tsx
"use client";

import React from "react";
import Link from "next/link";
import { BreadcrumbProps } from "@/types/breadcrumb";

const AdvancedBreadcrumb: React.FC<BreadcrumbProps> = ({
  items,
  separator = "chevron", // 'chevron', 'slash', 'arrow', custom
  className = "",
}) => {
  const getSeparator = () => {
    switch (separator) {
      case "chevron":
        return (
          <span className="mx-2 text-gray-300">
            <svg
              className="w-4 h-4"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24">
              <path
                strokeLinecap="round"
                strokeLinejoin="round"
                strokeWidth={2}
                d="M9 5l7 7-7 7"
              />
            </svg>
          </span>
        );
      case "slash":
        return <span className="mx-2 text-gray-300">/</span>;
      case "arrow":
        return <span className="mx-2 text-gray-300">→</span>;
      default:
        return separator;
    }
  };

  return (
    <nav className={`bg-[#F9F9F9] py-[14px] ${className} mb-5`}>
      <div className="container mx-auto px-6 flex items-center flex-wrap gap-1 text-sm">
      {/* Home Icon - Always first */}
      {/* <Link
        href="/"
        className="flex items-center text-gray-500 hover:text-gray-700 transition-colors duration-200"
      >
        <svg className="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
        </svg>
        Home
      </Link> */}

      {/* {getSeparator()} */}

      {/* Dynamic Items */}
        {items.map((item, index) => {
          const isLastItem = index === items.length - 1;

        return (
          <div key={index} className="flex items-center">
            {/* Item with icon */}
            <div className="flex items-center">
              {item.icon && (
                <span className="mr-1 flex items-center text-gray-400">
                  {item.icon}
                </span>
              )}

              {item.href && !isLastItem ? (
                <Link
                  href={item.href}
                  className="text-gray-500 hover:text-gray-700 transition-colors duration-200">
                  {item.label?.toLocaleUpperCase()}
                </Link>
              ) : (
                <span
                  className={`${
                    isLastItem ? "text-[#EA5921] font-[300]" : "text-gray-500"
                  }`}>
                  {item.label?.toLocaleUpperCase()}
                </span>
              )}
            </div>

            {/* Separator - last item ke baad nahi */}
            {!isLastItem && getSeparator()}
          </div>
        );
        })}
      </div>
    </nav>
  );
};

export default AdvancedBreadcrumb;
