"use client";
import {
  FaCheckCircle,
  FaStar,
  FaFingerprint,
  FaCloud,
  FaMoneyCheckAlt,
  FaChartLine,
  FaBolt,
  FaShieldAlt,
  FaUserCheck,
  FaBuilding,
  FaProjectDiagram,
  FaFileAlt,
} from "react-icons/fa";
import Image from "next/image";
import Link from "next/link";
import AdvancedBreadcrumb from "@/components/common/Bredacrumb";
import { baseUri } from "@/services/constant";
import React from "react";

export interface Solution {
  id: string;
  title: string;
  slug: string;
  short_description: string;
  description: string;
  features: string[];
  benefits: string[];
  technologies: string[];
  status: boolean;
  featured: boolean;
  sort_order: number;
  category: string | null;
  price_range: string | null;
  delivery_time: string | null;
  meta_description: string | null;
  meta_keywords: string | null;
  meta_title: string | null;
  image?: string | null;
  featured_image?: string | null;
  created_at: string;
  updated_at: string;
}

// Helper to get icon for features
const getFeatureIcon = (text: string) => {
  text = text.toLowerCase();
  if (/biometric|finger/.test(text)) return FaFingerprint;
  if (/payroll|salary|wage/.test(text)) return FaMoneyCheckAlt;
  if (/cloud|online|web/.test(text)) return FaCloud;
  if (/real[- ]?time|instant|live/.test(text)) return FaBolt;
  if (/analytics|report|insight|dashboard/.test(text)) return FaChartLine;
  if (/attendance|check.?in|presence/.test(text)) return FaUserCheck;
  if (/multi|branch|location/.test(text)) return FaBuilding;
  if (/integration|api|connect/.test(text)) return FaProjectDiagram;
  if (/security|secure|compliance|privacy/.test(text)) return FaShieldAlt;
  if (/report|pdf|export/.test(text)) return FaFileAlt;
  return FaCheckCircle;
};

// Reusable Card for features/benefits
const Card = ({ text, icon }: { text: string; icon: React.ElementType }) => (
  <div className="bg-white rounded-2xl border border-gray-200 p-5 shadow-md hover:shadow-xl hover:scale-105 transition-transform duration-300">
    <div className="flex items-center gap-4">
      <span className="inline-flex items-center justify-center w-12 h-12 rounded-full bg-gradient-to-br from-orange-200 to-orange-400 text-white flex-shrink-0 shadow-lg">
        {React.createElement(icon, { size: 20 })}
      </span>
      <p className="text-gray-800 text-sm md:text-base font-medium">{text}</p>
    </div>
  </div>
);

const SolutionDetails = ({ solution }: { solution: Solution }) => {
  const breadcrumbItems = [
    { label: "Home", href: "/" },
    { label: "Solutions", href: "/solutions" },
    { label: solution.title, href: `/solutions/${solution.slug}` },
  ];

  const imageSrc =
    solution.image || solution.featured_image
      ? `${baseUri}${solution.image || solution.featured_image}`
      : "/images/solution1.png";

  return (
    <div className="bg-white">
      <AdvancedBreadcrumb items={breadcrumbItems} />

      <div className="container mx-auto px-4 py-6 md:py-12">
        {/* Hero */}
        <section className="grid lg:grid-cols-2 gap-10 items-center mb-12">
          <div className="order-2">
            <span className="inline-block px-4 py-1 bg-orange-100 text-orange-700 rounded-full text-sm font-semibold mb-4">
              Solution
            </span>
            <h1 className="text-2xl md:text-3xl font-bold text-gray-900 mb-4">
              {solution.title}
            </h1>
            {solution.short_description && (
              <p className="text-gray-700 text-md">
                {solution.short_description}
              </p>
            )}
            <div className="flex flex-col sm:flex-row gap-4 mb-6 mt-5">
              <Link
                href="/contact"
                className="bg-orange-500 hover:bg-orange-600 text-white px-5 py-2 rounded-md font-medium shadow-md hover:shadow-lg transition-all duration-300 text-center"
              >
                Talk to Us
              </Link>
              <Link
                href="/solutions"
                className="border border-orange-500 text-orange-600 px-5 py-2 rounded-md font-medium hover:bg-orange-50 transition-all duration-300 text-center"
              >
                Explore More Solutions
              </Link>
            </div>
          </div>
          <div className="order-1 relative w-full h-64 md:h-96 rounded-3xl overflow-hidden border border-gray-200 shadow-lg">
            <Image
              src={imageSrc}
              alt={solution.title}
              fill
              className="object-cover object-center"
              unoptimized
            />
          </div>
        </section>

        {/* Info Cards */}
        <section className="mb-12 grid grid-cols-1 md:grid-cols-3 gap-6">
          <div className="rounded-xl border border-[#e5e5e5] bg-white p-4 text-center shadow-sm hover:shadow-md transition-shadow">
            <p className="text-xs text-gray-500">Category</p>
            <p className="text-sm md:text-base font-medium text-gray-900">
              {solution.category || "N/A"}
            </p>
          </div>
          <div className="rounded-xl border border-[#e5e5e5] bg-white p-4 text-center shadow-sm hover:shadow-md transition-shadow">
            <p className="text-xs text-gray-500">Delivery Time</p>
            <p className="text-sm md:text-base font-medium text-gray-900">
              {solution.delivery_time || "Standard"}
            </p>
          </div>
          <div className="rounded-xl border border-[#e5e5e5] bg-white p-4 text-center shadow-sm hover:shadow-md transition-shadow">
            <p className="text-xs text-gray-500">Price Range</p>
            <p className="text-sm md:text-base font-medium text-gray-900">
              {solution.price_range || "Contact for pricing"}
            </p>
          </div>
        </section>

        {/* Features */}
        {solution.features?.length > 0 && (
          <section>
            <h2 className="text-2xl sm:text-3xl section-title font-bold text-center text-gray-900 mb-8 relative before:absolute before:-bottom-2 before:left-1/2 before:-translate-x-1/2 before:w-24 before:h-1 before:bg-gradient-to-r from-orange-400 to-orange-600 before:rounded-full">
              Key Features
            </h2>
            <div className="grid sm:grid-cols-2 md:grid-cols-3 gap-8 mt-10">
              {solution.features.map((feature) => (
                <Card
                  key={feature}
                  text={feature}
                  icon={getFeatureIcon(feature)}
                />
              ))}
            </div>
          </section>
        )}

        {/* Benefits */}
        {solution.benefits?.length > 0 && (
          <section className="mb-12">
            <h2 className="text-2xl sm:text-3xl section-title font-bold text-center text-gray-900 mb-8 relative before:absolute before:-bottom-2 before:left-1/2 before:-translate-x-1/2 before:w-24 before:h-1 before:bg-gradient-to-r from-orange-400 to-orange-600 before:rounded-full pt-5">
              Benefits
            </h2>
            <div className="grid sm:grid-cols-2 md:grid-cols-3 gap-8 mt-10">
              {solution.benefits.map((benefit) => {
                const icon = /cost|save|roi|budget/.test(benefit.toLowerCase())
                  ? FaMoneyCheckAlt
                  : /secure|security|compliance/.test(benefit.toLowerCase())
                  ? FaShieldAlt
                  : /faster|speed|real[- ]?time|instant/.test(
                      benefit.toLowerCase()
                    )
                  ? FaBolt
                  : /insight|analytics|report/.test(benefit.toLowerCase())
                  ? FaChartLine
                  : /cloud|remote/.test(benefit.toLowerCase())
                  ? FaCloud
                  : FaStar;
                return <Card key={benefit} text={benefit} icon={icon} />;
              })}
            </div>
          </section>
        )}

        {/* Technologies */}
        {solution.technologies?.length > 0 && (
          <section>
            <h2 className="text-2xl sm:text-3xl section-title font-bold text-center text-gray-900 mb-6 relative before:absolute before:-bottom-2 before:left-1/2 before:-translate-x-1/2 before:w-24 before:h-1 before:bg-gradient-to-r from-orange-400 to-orange-600 before:rounded-full">
              Technologies
            </h2>
            <div className="flex flex-wrap justify-center gap-3 mt-6">
              {solution.technologies.map((tech) => (
                <span
                  key={tech}
                  className="px-4 py-2 rounded-full text-xs md:text-sm font-medium bg-orange-100 text-orange-700 shadow-sm"
                >
                  {tech}
                </span>
              ))}
            </div>
          </section>
        )}
      </div>

      {/* CTA Section */}
      <section className="bg-gradient-to-r from-orange-800 to-orange-600 text-center">
        <h3 className="text-2xl sm:text-3xl font-bold text-white mb-1 pt-10">
          Ready to modernize your workforce?
        </h3>
        <p className="text-orange-100 mb-8">
          Book a demo and see how this solution transforms operations.
        </p>
        <div className="flex flex-col sm:flex-row px-4 justify-center gap-4 pb-10">
          <Link
            href="/contact"
            className="bg-white text-orange-600 px-6 py-2 rounded-md font-semibold hover:rounded-lg transition-all duration-400 hover:shadow-lg"
          >
            Request a Demo
          </Link>
          <Link
            href="/solutions"
            className="border border-white text-white px-6 py-2 rounded-md hover:bg-white/10 hover:rounded-lg transition-all duration-400"
          >
            View Other Solutions
          </Link>
        </div>
      </section>
    </div>
  );
};

export default SolutionDetails;
