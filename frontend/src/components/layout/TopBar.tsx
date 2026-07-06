"use client";

import Link from "next/link";

const TopBar = () => {
  return (
    <div className="w-full bg-orange-600 text-white text-xs sm:text-sm">
      <div className="container mx-auto px-4 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 sm:gap-0 py-2 sm:py-0 sm:h-10">
        <Link
          href="/sales"
          className="font-semibold hover:text-black transition-colors text-center sm:text-left"
        >
          Request a callback
        </Link>

        <div className="flex flex-wrap justify-center sm:justify-end items-center gap-2 sm:gap-4">
          <Link
            href="https://onlinerealsoft.com"
            target="_blank"
            rel="noopener noreferrer"
            className="font-semibold hover:text-white text-black transition-colors"
          >
            <span className="me-1 bg-green-500 px-2 py-1 rounded">
              Free HRMS
            </span>
          </Link>

          <span className="opacity-60">|</span>

          <Link href="/careers" className="hover:text-black transition-colors">
            Careers
          </Link>

          <span className="opacity-60">|</span>

          <Link href="/blog" className="hover:text-black transition-colors">
            News
          </Link>

          <span className="opacity-60">|</span>

          <Link
            href="/integrations"
            className="hover:text-black transition-colors text-center"
          >
            3rd Party Integrations
          </Link>
        </div>
      </div>
    </div>
  );
};

export default TopBar;
