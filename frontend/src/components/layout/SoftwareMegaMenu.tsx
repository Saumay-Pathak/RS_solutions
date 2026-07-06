"use client";

import React, { useState, useEffect } from "react";
import Link from "next/link";
import axiosClient from "@/services/axiosClient";
import Portal from "../common/Portal";
import DownloadModal from "../software/DownloadModal";
import { motion, AnimatePresence } from "framer-motion";

type Software = {
  id: string;
  title: string;
  slug: string;
  one_line_description: string;
  description: string;
  main_category: string;
  sub_category: string;
  platforms: string[];
  requirements: string[];
  tags: string[];
  version: string | null;
  size: number | null;
  developer: string | null;
  license: string | null;
  price: string | null;
  is_free: boolean;
  download_count: number;
  featured: boolean;
  status: boolean;
  sort_order: number;
  external_url: string | null;
  file: string | null;
  released_at: string | null;
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

const SoftwareMegaMenu = () => {
  const [activeSoftware, setActiveSoftware] = useState<string | null>(null);
  const [software, setSoftware] = useState<Software[]>([]);
  const [loading, setLoading] = useState(true);
  const [selectedSoftware, setSelectedSoftware] = useState<Software | null>(null);

  useEffect(() => {
    const fetchSoftware = async () => {
      try {
        setLoading(true);
        const response = await axiosClient.get("/content/software");
        const data = await response.data;
        if (data.success) {
          const softwareData = data.data;
          const sortedSoftware = [...softwareData].sort((a, b) => a.sort_order - b.sort_order);
          setSoftware(sortedSoftware);
          if (sortedSoftware.length > 0) setActiveSoftware(sortedSoftware[0].id);
        }
      } catch (error) {
        console.error("Error fetching software:", error);
      } finally {
        setLoading(false);
      }
    };
    fetchSoftware();
  }, []);

  const activeSoftwareData = software.find((item) => item.id === activeSoftware);

  const formatFileSize = (bytes: number | null) => {
    if (!bytes) return "N/A";
    const mb = bytes / (1024 * 1024);
    return `${mb.toFixed(1)} MB`;
  };

  if (loading) {
    return (
      <div className="w-full bg-white border border-gray-100 rounded-2xl shadow-2xl">
        <div className="p-8 flex flex-col justify-center items-center h-48 space-y-3">
          <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-orange-500" />
          <p className="text-gray-600 text-sm">Loading downloads...</p>
        </div>
      </div>
    );
  }

  return (
    <>
      <div className="overflow-hidden bg-white rounded-2xl shadow-[0_20px_60px_rgba(0,0,0,0.08)] border border-gray-100">
        <div className="flex">
          {/* ── Sidebar ── */}
          <div className="w-60 flex-shrink-0 bg-gray-50 border-r border-gray-100 p-3">
            <p className="text-[11px] font-bold text-gray-500 tracking-widest uppercase mb-3 px-2">
              Downloads
            </p>
            <div className="space-y-0.5 max-h-[390px] overflow-y-auto pr-1 custom-scrollbar">
              {software.map((item) => {
                const isActive = activeSoftware === item.id;
                return (
                  <Link
                    key={item.id}
                    href={`/software/${item?.slug}`}
                    className={`group flex items-center justify-between px-2.5 py-2 rounded-lg cursor-pointer transition-all duration-200 ${
                      isActive
                        ? "bg-gradient-to-r from-orange-500 to-amber-500 text-white shadow-sm shadow-orange-500/20 font-semibold"
                        : "text-gray-600 hover:bg-orange-50 hover:text-gray-900"
                    }`}
                    onMouseEnter={() => setActiveSoftware(item.id)}
                  >
                    <span className="text-[13px] font-medium leading-snug pr-2">{item.title}</span>
                    <ChevronRight
                      className={`w-3 h-3 flex-shrink-0 transition-all duration-200 ${
                        isActive
                          ? "opacity-100 text-white"
                          : "opacity-0 -translate-x-1 group-hover:opacity-100 group-hover:translate-x-0 text-orange-400"
                      }`}
                    />
                  </Link>
                );
              })}
            </div>
          </div>

          {/* ── Right Panel ── */}
          <div className="flex-1 p-5 min-h-[430px] flex flex-col">
            <AnimatePresence mode="wait">
              {activeSoftwareData ? (
                <motion.div
                  key={activeSoftwareData.id}
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
                        {activeSoftwareData.title}
                      </h3>
                      <div className="flex items-center gap-1.5">
                        {activeSoftwareData.is_free && (
                          <span className="text-[10px] font-bold text-green-700 bg-green-50 border border-green-200 px-2 py-0.5 rounded-full">
                            FREE
                          </span>
                        )}
                        {activeSoftwareData.featured && (
                          <span className="text-[10px] font-bold text-amber-700 bg-amber-50 border border-amber-200 px-2 py-0.5 rounded-full">
                            ⭐ Featured
                          </span>
                        )}
                        {activeSoftwareData.download_count > 0 && (
                          <span className="text-[10px] font-bold text-blue-600 bg-blue-50 border border-blue-200 px-2 py-0.5 rounded-full">
                            {activeSoftwareData.download_count} Downloads
                          </span>
                        )}
                      </div>
                    </div>
                    <p className="text-gray-700 text-[13px] leading-relaxed mb-4 max-w-2xl">
                      {activeSoftwareData.one_line_description}
                    </p>

                    {/* Info Cards */}
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-3 max-h-[210px] overflow-y-auto pr-1 custom-scrollbar">
                      {/* Details Card */}
                      <InfoCard
                        title="Details"
                        icon={
                          <svg className="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2.5} d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2z" />
                          </svg>
                        }
                      >
                        <div className="space-y-1.5 text-[12px]">
                          {activeSoftwareData.version && (
                            <div className="flex justify-between py-0.5 border-b border-gray-50">
                              <span className="text-gray-600 font-medium">Version</span>
                              <span className="text-gray-900 font-semibold">{activeSoftwareData.version}</span>
                            </div>
                          )}
                          {activeSoftwareData.developer && (
                            <div className="flex justify-between py-0.5 border-b border-gray-50">
                              <span className="text-gray-600 font-medium">Developer</span>
                              <span className="text-gray-900 font-semibold">{activeSoftwareData.developer}</span>
                            </div>
                          )}
                          {activeSoftwareData.license && (
                            <div className="flex justify-between py-0.5 border-b border-gray-50">
                              <span className="text-gray-600 font-medium">License</span>
                              <span className="text-gray-900 font-semibold">{activeSoftwareData.license}</span>
                            </div>
                          )}
                          {activeSoftwareData.main_category && (
                            <div className="flex justify-between py-0.5 border-b border-gray-50">
                              <span className="text-gray-600 font-medium">Category</span>
                              <span className="text-gray-900 font-semibold">{activeSoftwareData.main_category}</span>
                            </div>
                          )}
                          {activeSoftwareData.size && (
                            <div className="flex justify-between py-0.5">
                              <span className="text-gray-600 font-medium">Size</span>
                              <span className="text-gray-900 font-semibold">{formatFileSize(activeSoftwareData.size)}</span>
                            </div>
                          )}
                        </div>
                      </InfoCard>

                      {/* Platforms & Requirements */}
                      <div className="flex flex-col gap-3">
                        {activeSoftwareData.platforms?.length > 0 && (
                          <InfoCard
                            title="Platforms"
                            icon={
                              <svg className="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2.5} d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                              </svg>
                            }
                          >
                            <ul className="space-y-1">
                              {activeSoftwareData.platforms.map((platform, i) => (
                                <BulletItem key={i}>{platform}</BulletItem>
                              ))}
                            </ul>
                          </InfoCard>
                        )}
                        {activeSoftwareData.requirements?.length > 0 && (
                          <InfoCard
                            title="Requirements"
                            icon={
                              <svg className="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2.5} d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                              </svg>
                            }
                          >
                            <ul className="space-y-1">
                              {activeSoftwareData.requirements.map((req, i) => (
                                <BulletItem key={i}>{req}</BulletItem>
                              ))}
                            </ul>
                          </InfoCard>
                        )}
                      </div>
                    </div>
                  </div>

                  {/* Panel Footer */}
                  <div className="mt-5 pt-4 border-t border-gray-100 flex items-center justify-between">
                    <button
                      onClick={() => setSelectedSoftware(activeSoftwareData)}
                      className="group/btn bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white text-[13px] font-semibold transition-all duration-200 flex items-center gap-2 px-4 py-2.5 rounded-xl shadow-md shadow-orange-500/15 hover:shadow-lg hover:shadow-orange-500/20 hover:-translate-y-0.5"
                    >
                      <svg className="w-3.5 h-3.5 group-hover/btn:translate-y-0.5 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2.5} d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                      </svg>
                      {activeSoftwareData.file ? "Download Now" : "Get Software"}
                    </button>
                    <Link
                      href={`/software/${activeSoftwareData.slug}`}
                      className="group/btn2 text-orange-600 hover:text-orange-700 text-[13px] font-bold transition-all duration-200 flex items-center gap-1.5 hover:translate-x-0.5"
                    >
                      View Details
                      <ChevronRight className="w-3.5 h-3.5 group-hover/btn2:translate-x-0.5 transition-transform duration-200" />
                    </Link>
                  </div>
                </motion.div>
              ) : (
                <div className="flex-1 flex items-center justify-center">
                  <p className="text-gray-600 text-sm">Select a software to view details</p>
                </div>
              )}
            </AnimatePresence>
          </div>
        </div>
      </div>

      {/* Download Modal */}
      {selectedSoftware && (
        <Portal>
          <DownloadModal
            software={{
              id: selectedSoftware.id,
              slug: selectedSoftware.slug,
              title: selectedSoftware.title,
              version: selectedSoftware.version || "",
              file: selectedSoftware.file,
              external_url: selectedSoftware.external_url,
            }}
            onClose={() => setSelectedSoftware(null)}
          />
        </Portal>
      )}

      <style jsx>{`
        .custom-scrollbar::-webkit-scrollbar { width: 4px; height: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f8f8f8; border-radius: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e5e5e5; border-radius: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #ccc; }
      `}</style>
    </>
  );
};

export default SoftwareMegaMenu;
