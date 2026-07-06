"use client";

import React, { useEffect, useState } from "react";
import Image from "next/image";
import Link from "next/link";
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
import Layout from "@/components/layout/Layout";
import AdvancedBreadcrumb from "@/components/common/Bredacrumb";
import { getSolutionBySlug } from "@/services/solutionServices";
import { baseUri } from "@/services/constant";

type Solution = {
  title: string;
  short_description: string;
  features: string[];
  benefits: string[];
  technologies: string[];
  category: string | null;
  price_range: string | null;
  delivery_time: string | null;
  image?: string | null;
};

export default function RecreatedSolutionPage() {
  const [solution, setSolution] = useState<Solution | null>(null);
  const [loading, setLoading] = useState(true);
  const slug = "cloud-attendance-and-payroll-software";

  useEffect(() => {
    getSolutionBySlug(slug).then((res) => {
      setSolution(res?.data ?? null);
      setLoading(false);
    });
  }, [slug]);

  const breadcrumbItems = [
    { label: "Home", href: "/" },
    { label: "Solutions", href: "/solutions" },
    { label: solution?.title ?? "Solution", href: "#" },
  ];

  if (loading) {
    return (
      <Layout>
        <div className="min-h-screen flex items-center justify-center">
          <div className="animate-spin h-10 w-10 border-b-2 border-orange-500 rounded-full" />
        </div>
      </Layout>
    );
  }

  if (!solution) {
    return (
      <Layout>
        <div className="min-h-screen flex flex-col items-center justify-center gap-4">
          <p>Solution not found</p>
          <Link href="/solutions" className="text-orange-600 underline">
            Back to Solutions
          </Link>
        </div>
      </Layout>
    );
  }

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

  return (
    <Layout>
      <AdvancedBreadcrumb items={breadcrumbItems} />
      <section className="mb-10">
        <div className="container mx-auto px-4">
          {/* Image */}
          <div className="relative h-[180px] md:h-[530px] rounded-3xl overflow-hidden border border-gray-200 shadown-sm">
            {solution.image ? (
              <Image
                src={`${baseUri}${solution.image}`}
                alt={solution.title}
                fill
                priority
                className="object-contain"
              />
            ) : (
              <div className="absolute inset-0 bg-gradient-to-br from-gray-800 to-gray-900" />
            )}

            {/* subtle overlay */}
            <div className="absolute inset-0" />
          </div>
          <div className="mt-8">
            <div className="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
              <div className="max-w-6xl text-left">
                <span className="inline-block mb-3 px-3 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-700">
                  {solution.category || "Solution"}
                </span>

                <h1 className="text-3xl md:text-4xl font-bold text-gray-900 leading-tight">
                  {solution.title}
                </h1>

                <p className="text-gray-700 text-lg mt-4">
                  {solution.short_description}
                </p>
              </div>

              {/* CTA Buttons */}
              <div className="flex flex-row items-center justify-center gap-3 mt-6 lg:mt-3 lg:shrink-0 me-0 md:me-5">
                <Link
                  href="/contact"
                  className="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-md font-medium transition"
                >
                  Get Demo
                </Link>

                <Link
                  href="/sales"
                  className="bg-gray-900/5 hover:bg-gray-900/10 text-gray-900 px-4 py-2 rounded-md font-medium ring-1 ring-gray-300 transition"
                >
                  Contact Sales
                </Link>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* META INFO */}
      <section className="mb-10">
        <div className="container mx-auto px-4">
          <div className="grid grid-cols-1 md:grid-cols-3 gap-3">
            <div className="rounded-xl border border-[#e5e5e5] bg-white p-3">
              <p className="text-xs text-gray-500">Category</p>
              <p className="text-sm font-medium text-gray-900">
                {solution.category || "N/A"}
              </p>
            </div>
            <div className="rounded-xl border border-[#e5e5e5] bg-white p-3">
              <p className="text-xs text-gray-500">Delivery Time</p>
              <p className="text-sm font-medium text-gray-900">
                {solution.delivery_time || "Standard"}
              </p>
            </div>
            <div className="rounded-xl border border-[#e5e5e5] bg-white p-3">
              <p className="text-xs text-gray-500">Price Range</p>
              <p className="text-sm font-medium text-gray-900">
                {solution.price_range || "Contact for pricing"}
              </p>
            </div>
          </div>
        </div>
      </section>

      {/* FEATURES */}
      <section className="bg-gray-50">
        <div className="container mx-auto px-4 py-10">
          <h2 className="section-title text-2xl sm:text-3xl font-bold text-center mb-12">
            Key Features
          </h2>
          <div className="grid md:grid-cols-3 gap-4 sm:gap-6 mt-10">
            {solution.features.map((feature) => (
              <Card
                key={feature}
                text={feature}
                icon={getFeatureIcon(feature)}
              />
            ))}
          </div>
        </div>
      </section>

      {/* BENEFITS */}
      {solution.benefits?.length > 0 && (
        <div className="container mx-auto">
          <section className="mb-12 px-4">
            <h2 className="text-2xl sm:text-3xl section-title font-bold text-center text-gray-900 mb-8 relative before:absolute before:-bottom-2 before:left-1/2 before:-translate-x-1/2 before:w-24 before:h-1 before:bg-gradient-to-r from-orange-400 to-orange-600 before:rounded-full pt-5">
              Benefits
            </h2>
            <div className="grid sm:grid-cols-2 md:grid-cols-3 gap-4 sm:gap-8 mt-10">
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
        </div>
      )}

      {/* TECHNOLOGIES */}
      {solution.technologies?.length > 0 && (
        <section className="mb-10">
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

      {/* FINAL CTA */}
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
    </Layout>
  );
}
