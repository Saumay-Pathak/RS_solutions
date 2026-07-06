"use client";

import React, { useState, useEffect } from "react";
import Link from "next/link";
import axiosClient from "@/services/axiosClient";
import { useRouter } from "next/navigation";
import { motion, AnimatePresence } from "framer-motion";

type Solution = {
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
  price_range: string | null;
  delivery_time: string | null;
  category: null;
  meta_title: string | null;
  meta_description: string | null;
  meta_keywords: string | null;
  updated_at: string;
  created_at: string;
};

// ── Shared primitives ─────────────────────────────────────────────────────────
const ChevronRight = ({ className = "w-3 h-3" }: { className?: string }) => (
  <svg className={className} fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2.5} d="M9 5l7 7-7 7" />
  </svg>
);

const InfoCard = ({
  icon,
  title,
  children,
}: {
  icon: React.ReactNode;
  title: string;
  children: React.ReactNode;
}) => (
  <div className="bg-white border border-gray-100 rounded-xl p-3.5 shadow-sm hover:shadow-md transition-shadow duration-200 flex flex-col gap-2">
    <h4 className="font-bold text-[10px] tracking-widest uppercase text-gray-600 flex items-center gap-1.5">
      <span className="p-1 rounded-md bg-orange-50 text-orange-500 flex-shrink-0">{icon}</span>
      {title}
    </h4>
    {children}
  </div>
);

const BulletItem = ({ children }: { children: React.ReactNode }) => (
  <li className="text-gray-800 text-[12px] flex items-start gap-1.5 leading-tight">
    <span className="text-orange-400 font-black mt-0.5 flex-shrink-0">·</span>
    {children}
  </li>
);

const SolutionsMegaMenu = () => {
  const [activeSolution, setActiveSolution] = useState<string | null>(null);
  const [solutions, setSolutions] = useState<Solution[]>([]);
  const [loading, setLoading] = useState(true);
  const router = useRouter();

  useEffect(() => {
    const fetchSolutions = async () => {
      try {
        setLoading(true);
        const response = await axiosClient.get("/content/solutions");
        const data = await response.data;
        if (data.success) {
          const solutionsData = data.data;
          setSolutions(solutionsData);
          if (solutionsData.length > 0) setActiveSolution(solutionsData[0].id);
        }
      } catch (error) {
        console.error("Error fetching solutions:", error);
      } finally {
        setLoading(false);
      }
    };
    fetchSolutions();
  }, []);

  const activeSolutionData = solutions.find((s) => s.id === activeSolution);

  if (loading) {
    return (
      <div className="w-full bg-white border border-gray-100 rounded-2xl shadow-2xl">
        <div className="p-8 flex flex-col justify-center items-center h-48 space-y-3">
          <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-orange-500" />
          <p className="text-gray-600 text-sm">Loading solutions...</p>
        </div>
      </div>
    );
  }

  return (
    <div className="overflow-hidden bg-white rounded-2xl shadow-[0_20px_60px_rgba(0,0,0,0.08)] border border-gray-100">
      <div className="flex">
        {/* ── Sidebar ── */}
        <div className="w-60 flex-shrink-0 bg-gray-50 border-r border-gray-100 p-3">
          <p className="text-[11px] font-bold text-gray-500 tracking-widest uppercase mb-3 px-2">
            Our Solutions
          </p>
          <div className="space-y-0.5 max-h-[390px] overflow-y-auto pr-1 custom-scrollbar">
            {solutions.map((solution) => {
              const isActive = activeSolution === solution.id;
              return (
                <div
                  key={solution.id}
                  className={`group flex items-center justify-between px-2.5 py-2 rounded-lg cursor-pointer transition-all duration-200 ${
                    isActive
                      ? "bg-gradient-to-r from-orange-500 to-amber-500 text-white shadow-sm shadow-orange-500/20 font-semibold"
                      : "text-gray-600 hover:bg-orange-50 hover:text-gray-900"
                  }`}
                  onMouseEnter={() => setActiveSolution(solution.id)}
                  onClick={() => router.push(`/solutions/${solution.slug}`)}
                >
                  <span className="text-[13px] font-medium leading-snug pr-2">{solution.title}</span>
                  <ChevronRight
                    className={`w-3 h-3 flex-shrink-0 transition-all duration-200 ${
                      isActive
                        ? "opacity-100 text-white"
                        : "opacity-0 -translate-x-1 group-hover:opacity-100 group-hover:translate-x-0 text-orange-400"
                    }`}
                  />
                </div>
              );
            })}
          </div>
        </div>

        {/* ── Right Panel ── */}
        <div className="flex-1 p-5 min-h-[430px] flex flex-col">
          <AnimatePresence mode="wait">
            {activeSolutionData ? (
              <motion.div
                key={activeSolutionData.id}
                initial={{ opacity: 0, y: 8 }}
                animate={{ opacity: 1, y: 0 }}
                exit={{ opacity: 0, y: -8 }}
                transition={{ duration: 0.18 }}
                className="flex-1 flex flex-col justify-between"
              >
                {/* Panel Header */}
                <div>
                  <div className="flex items-center gap-2.5 mb-1.5">
                    <h3 className="text-lg font-extrabold text-gray-900 tracking-tight">
                      {activeSolutionData.title}
                    </h3>
                    {activeSolutionData.featured && (
                      <span className="text-[10px] font-bold tracking-wider uppercase text-amber-700 bg-amber-50 border border-amber-200 px-2 py-0.5 rounded-full">
                        ⭐ Featured
                      </span>
                    )}
                  </div>
                  <p className="text-gray-700 text-[13px] leading-relaxed mb-4 max-w-2xl">
                    {activeSolutionData.short_description}
                  </p>

                  {/* Info Cards */}
                  <div className="grid grid-cols-1 md:grid-cols-3 gap-3 max-h-[210px] overflow-y-auto pr-1 custom-scrollbar">
                    {(activeSolutionData.features?.length ?? 0) > 0 && (
                      <InfoCard
                        title="Features"
                        icon={
                          <svg className="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2.5} d="M5 13l4 4L19 7" />
                          </svg>
                        }
                      >
                        <ul className="space-y-1.5">
                          {activeSolutionData.features.map((f, i) => (
                            <BulletItem key={i}>{f}</BulletItem>
                          ))}
                        </ul>
                      </InfoCard>
                    )}
                    {(activeSolutionData.benefits?.length ?? 0) > 0 && (
                      <InfoCard
                        title="Benefits"
                        icon={
                          <svg className="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2.5} d="M13 10V3L4 14h7v7l9-11h-7z" />
                          </svg>
                        }
                      >
                        <ul className="space-y-1.5">
                          {activeSolutionData.benefits.map((b, i) => (
                            <BulletItem key={i}>{b}</BulletItem>
                          ))}
                        </ul>
                      </InfoCard>
                    )}
                    {(activeSolutionData.technologies?.length ?? 0) > 0 && (
                      <InfoCard
                        title="Technologies"
                        icon={
                          <svg className="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2.5} d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                          </svg>
                        }
                      >
                        <ul className="space-y-1.5">
                          {activeSolutionData.technologies.map((t, i) => (
                            <BulletItem key={i}>{t}</BulletItem>
                          ))}
                        </ul>
                      </InfoCard>
                    )}
                  </div>
                </div>

                {/* Panel Footer */}
                <div className="mt-5 pt-4 border-t border-gray-100 flex items-center justify-between">
                  {activeSolutionData.price_range && activeSolutionData.price_range !== "0" ? (
                    <div className="flex flex-col">
                      <span className="text-gray-600 text-[10px] uppercase tracking-wider font-semibold">
                        Starting from
                      </span>
                      <span className="text-orange-600 font-extrabold text-base">
                        {activeSolutionData.price_range}
                      </span>
                    </div>
                  ) : (
                    <div />
                  )}
                  <Link
                    href={`/solutions/${activeSolutionData.slug}`}
                    className="group/btn bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white text-[13px] font-semibold transition-all duration-200 flex items-center gap-2 px-4 py-2.5 rounded-xl shadow-md shadow-orange-500/15 hover:shadow-lg hover:shadow-orange-500/20 hover:-translate-y-0.5"
                  >
                    View Solution Details
                    <ChevronRight className="w-3.5 h-3.5 group-hover/btn:translate-x-0.5 transition-transform duration-200" />
                  </Link>
                </div>
              </motion.div>
            ) : (
              <div className="flex-1 flex items-center justify-center">
                <p className="text-gray-600 text-sm">Select a solution to view details</p>
              </div>
            )}
          </AnimatePresence>
        </div>
      </div>

      <style jsx>{`
        .custom-scrollbar::-webkit-scrollbar { width: 4px; height: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f8f8f8; border-radius: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e5e5e5; border-radius: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #ccc; }
      `}</style>
    </div>
  );
};

export default SolutionsMegaMenu;
