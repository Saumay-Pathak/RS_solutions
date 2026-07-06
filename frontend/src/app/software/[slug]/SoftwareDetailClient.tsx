"use client";

import { useState } from "react";
import {
  Download,
  User,
  Tag,
  CheckCircle,
  AlertCircle,
  Star,
  Cpu,
  Monitor,
  Building,
  BarChart3,
  Shield,
  Menu,
  X,
} from "lucide-react";
import DownloadModal from "@/components/software/DownloadModal";
import Link from "next/link";
import { formatDistanceToNow } from "date-fns";

type Software = {
  id: string;
  slug: string;
  title: string;
  version: string;
  file?: string | null;
  external_url?: string | null;
  price?: string;
  license?: string;
  is_free?: boolean;
  one_line_description?: string;
  description?: string;
  developer?: string;
  requirements?: string[];
  platforms?: string[];
  tags?: string[];
  size?: string | null;
  released_at?: string | null;
  updated_at?: string;
  download_count?: number;
};

interface SoftwareDetailClientProps {
  software: Software | null;
  error: string | null;
}

export default function SoftwareDetailClient({
  software,
  error,
}: SoftwareDetailClientProps) {
  const [showDownloadModal, setShowDownloadModal] = useState(false);
  const [activeTab, setActiveTab] = useState("overview");
  const [mobileMenuOpen, setMobileMenuOpen] = useState(false);

  if (error || !software) {
    return (
      <div className="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50 flex items-center justify-center px-4">
        <div className="max-w-md w-full text-center">
          <div className="bg-white rounded-2xl shadow-lg p-8 border border-slate-200">
            <div className="w-20 h-20 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-6">
              <AlertCircle className="w-10 h-10 text-red-500" />
            </div>
            <h1 className="text-2xl font-bold text-slate-900 mb-3">
              Software Not Found
            </h1>
            <p className="text-slate-600 mb-6">
              {error || "The requested software could not be found."}
            </p>
            <button
              onClick={() => window.history.back()}
              className="bg-orange-500 hover:bg-orange-600 text-white px-6 py-3 rounded-xl font-medium transition-colors shadow-lg hover:shadow-xl"
            >
              Go Back
            </button>
          </div>
        </div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50">
      {/* Header Section */}
      <div className="bg-white border-b border-slate-200">
        <div className="max-w-6xl mx-auto px-4 py-6">
          <div className="flex flex-col lg:flex-row gap-6 items-start">
            {/* Software Info */}
            <div className="flex-1 w-full">
              <div className="flex items-start gap-4 mb-4">
                <div className="w-12 h-12 bg-gradient-to-br from-orange-500 to-amber-500 rounded-xl flex items-center justify-center flex-shrink-0">
                  <Monitor className="w-6 h-6 text-white" />
                </div>
                <div className="flex-1 min-w-0">
                  <h1 className="text-2xl lg:text-4xl font-bold text-slate-900 leading-tight">
                    {software.title}
                  </h1>
                  <p className="text-slate-600 mt-2 text-base lg:text-lg">
                    {software.one_line_description}
                  </p>
                </div>
              </div>

              {/* Badges */}
              <div className="flex flex-wrap items-center gap-2 mb-4">
                <div className="flex items-center gap-2 bg-slate-100 px-3 py-1.5 rounded-full hover:shadow-sm transition">
                  <Tag className="w-4 h-4 text-slate-600" />
                  <span className="text-sm font-medium text-slate-700">
                    v{software.version}
                  </span>
                </div>

                {software.is_free ? (
                  <div className="flex items-center gap-2 bg-green-100 px-3 py-1.5 rounded-full hover:shadow-sm transition">
                    <Star className="w-4 h-4 text-green-600" />
                    <span className="text-sm font-medium text-green-700">
                      Free
                    </span>
                  </div>
                ) : (
                  <div className="flex items-center gap-2 bg-amber-100 px-3 py-1.5 rounded-full hover:shadow-sm transition">
                    <Shield className="w-4 h-4 text-amber-600" />
                    <span className="text-sm font-medium text-amber-700">
                      Premium
                    </span>
                  </div>
                )}

                {software.license && (
                  <div className="flex items-center gap-2 bg-blue-100 px-3 py-1.5 rounded-full hover:shadow-sm transition">
                    <User className="w-4 h-4 text-blue-600" />
                    <span className="text-sm font-medium text-blue-700">
                      {software.license}
                    </span>
                  </div>
                )}
              </div>
            </div>

            {/* Download CTA */}
            <div className="w-full lg:w-80 sticky top-20">
              <div className="bg-gradient-to-br from-orange-500 to-amber-500 rounded-2xl p-6 text-white shadow-lg">
                <div className="text-center mb-4">
                  <div className="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mx-auto mb-3">
                    <Download className="w-6 h-6" />
                  </div>
                  <h3 className="text-lg font-semibold mb-2">
                    Ready to Download?
                  </h3>
                  <p className="text-orange-100 text-sm">
                    {software.is_free ? "Completely free" : "Premium license"} •
                    Latest version
                  </p>
                </div>

                <button
                  onClick={() => setShowDownloadModal(true)}
                  className="w-full bg-white text-orange-600 hover:bg-orange-50 font-semibold py-3 px-4 rounded-xl flex items-center justify-center gap-2 transition-all shadow-lg hover:shadow-xl hover:scale-105 cursor-pointer mb-3"
                >
                  <Download className="w-5 h-5" />
                  Download Now
                </button>

                {software.download_count !== undefined && (
                  <div className="text-center pt-3 border-t border-white/20">
                    <p className="text-orange-100 text-sm">
                      <span className="text-white font-semibold">
                        {software.download_count.toLocaleString()}
                      </span>{" "}
                      downloads
                    </p>
                  </div>
                )}
              </div>
            </div>
          </div>

          {/* Tabs */}
          <div className="w-full mt-6">
            {/* Mobile Tab Button */}
            <div className="lg:hidden">
              <button
                onClick={() => setMobileMenuOpen(!mobileMenuOpen)}
                className="w-full bg-white border border-slate-300 rounded-xl p-3 flex items-center justify-between shadow-sm hover:border-orange-300 transition-colors"
              >
                <span className="font-medium text-slate-900 capitalize">
                  {activeTab}
                </span>
                {mobileMenuOpen ? (
                  <X className="w-5 h-5 text-black" />
                ) : (
                  <Menu className="w-5 h-5 text-black" />
                )}
              </button>

              {mobileMenuOpen && (
                <div className="relative mt-2 bg-white border border-slate-300 rounded-xl shadow-lg z-10">
                  {["overview", "features", "requirements"].map((tab) => (
                    <button
                      key={tab}
                      onClick={() => {
                        setActiveTab(tab);
                        setMobileMenuOpen(false);
                      }}
                      className={`w-full px-4 py-3 text-left border-b border-slate-100 last:border-b-0 transition-colors ${
                        activeTab === tab
                          ? "bg-orange-50 text-orange-700 font-medium border-orange-200"
                          : "text-slate-700 hover:bg-slate-50"
                      }`}
                    >
                      {tab.charAt(0).toUpperCase() + tab.slice(1)}
                    </button>
                  ))}
                </div>
              )}
            </div>

            {/* Desktop Tabs */}
            <div className="hidden lg:flex space-x-1 bg-slate-100 p-1 rounded-xl w-fit mt-4">
              {["overview", "features", "requirements"].map((tab) => (
                <button
                  key={tab}
                  onClick={() => setActiveTab(tab)}
                  className={`px-6 py-3 rounded-lg text-sm font-medium transition-all cursor-pointer ${
                    activeTab === tab
                      ? "bg-white text-orange-600 shadow-md border border-orange-200"
                      : "text-slate-600 hover:text-slate-900 hover:bg-white/50"
                  }`}
                >
                  {tab.charAt(0).toUpperCase() + tab.slice(1)}
                </button>
              ))}
            </div>
          </div>
        </div>
      </div>

      {/* Main Content */}
      <div className="max-w-6xl mx-auto px-4 py-6 space-y-6">
        <div className="grid lg:grid-cols-3 gap-6">
          {/* Left Column */}
          <div className="lg:col-span-2 space-y-6">
            {/* Overview */}
            {activeTab === "overview" && (
              <div className="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 hover:border-orange-200 transition-colors">
                <h2 className="text-xl lg:text-2xl font-bold text-slate-900 mb-4">
                  Overview
                </h2>
                <div
                  className="prose prose-slate text-gray-700 max-w-none"
                  dangerouslySetInnerHTML={{ __html: software.description || "" }}
                />
              </div>
            )}

            {/* Features */}
            {activeTab === "features" && (software.tags ?? []).length > 0 && (
              <div className="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 hover:border-orange-200 transition-colors">
                <h2 className="text-xl lg:text-2xl font-bold text-slate-900 mb-6">
                  Key Features
                </h2>
                <div className="grid sm:grid-cols-2 gap-3">
                  {software.tags!.map((tag, index) => (
                    <div
                      key={index}
                      className="flex items-start gap-3 p-3 rounded-xl bg-slate-50 hover:bg-orange-50 transition-colors border border-slate-200 hover:border-orange-200 transform hover:-translate-y-1 hover:scale-105"
                    >
                      <CheckCircle className="w-5 h-5 text-orange-500 mt-0.5 flex-shrink-0" />
                      <span className="text-slate-800 font-medium text-sm lg:text-base">
                        {tag}
                      </span>
                    </div>
                  ))}
                </div>
              </div>
            )}

            {/* Requirements */}
            {activeTab === "requirements" && (
              <div className="space-y-6">
                {/* System Requirements */}
                {(software.requirements ?? []).length > 0 && (
                  <div className="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 hover:border-orange-200 transition-colors">
                    <div className="flex items-center gap-3 mb-4">
                      <div className="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <Cpu className="w-5 h-5 text-blue-600" />
                      </div>
                      <h3 className="text-lg lg:text-xl font-bold text-slate-900">
                        System Requirements
                      </h3>
                    </div>
                    <ul className="space-y-3">
                      {software.requirements!.map((req, idx) => (
                        <li
                          key={idx}
                          className="flex items-center gap-3 text-slate-700"
                        >
                          <div className="w-2 h-2 bg-orange-500 rounded-full flex-shrink-0"></div>
                          <span className="text-sm lg:text-base">{req}</span>
                        </li>
                      ))}
                    </ul>
                  </div>
                )}

                {/* Supported Platforms */}
                {(software.platforms ?? []).length > 0 && (
                  <div className="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 hover:border-orange-200 transition-colors">
                    <div className="flex items-center gap-3 mb-4">
                      <div className="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <Monitor className="w-5 h-5 text-green-600" />
                      </div>
                      <h3 className="text-lg lg:text-xl font-bold text-slate-900">
                        Supported Platforms
                      </h3>
                    </div>
                    <ul className="space-y-3">
                      {software.platforms!.map((platform, idx) => (
                        <li
                          key={idx}
                          className="flex items-center gap-3 text-slate-700"
                        >
                          <div className="w-2 h-2 bg-green-500 rounded-full flex-shrink-0"></div>
                          <span className="text-sm lg:text-base">
                            {platform}
                          </span>
                        </li>
                      ))}
                    </ul>
                  </div>
                )}
              </div>
            )}
          </div>

          {/* Right Column */}
          <div className="space-y-6">
            {/* Developer Info */}
            {software.developer && (
              <div className="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 hover:border-orange-200 transition-colors">
                <div className="flex items-center gap-3 mb-4">
                  <div className="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <Building className="w-5 h-5 text-purple-600" />
                  </div>
                  <h3 className="text-lg font-semibold text-slate-900">
                    Developer
                  </h3>
                </div>
                <p className="text-slate-700 text-sm lg:text-base">
                  {software.developer}
                </p>
              </div>
            )}

            {/* Version Info */}
            <div className="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 hover:border-orange-200 transition-colors">
              <div className="flex items-center gap-3 mb-4">
                <div className="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center flex-shrink-0">
                  <BarChart3 className="w-5 h-5 text-amber-600" />
                </div>
                <h3 className="text-lg font-semibold text-slate-900">
                  Version Info
                </h3>
              </div>
              <div className="space-y-3">
                <div className="flex justify-between items-center">
                  <span className="text-slate-600 text-sm lg:text-base">
                    Current Version
                  </span>
                  <span className="font-semibold text-slate-900 text-sm lg:text-base">
                    v{software.version}
                  </span>
                </div>
                {software.updated_at && (
                  <div className="flex justify-between items-center">
                    <span className="text-slate-600 text-sm lg:text-base">
                      Last Updated
                    </span>
                    <span className="font-medium text-slate-900 text-sm lg:text-base">
                      {formatDistanceToNow(new Date(software.updated_at))} ago
                    </span>
                  </div>
                )}
                {software.size && (
                  <div className="flex justify-between items-center">
                    <span className="text-slate-600 text-sm lg:text-base">
                      File Size
                    </span>
                    <span className="font-medium text-slate-900 text-sm lg:text-base">
                      {software.size}
                    </span>
                  </div>
                )}
              </div>
            </div>

            {/* Support Info */}
            <div className="bg-gradient-to-br from-orange-500 to-amber-500 rounded-2xl p-6 text-white shadow-lg">
              <h3 className="text-lg font-semibold mb-3">Need Help?</h3>
              <p className="text-orange-100 text-sm mb-4">
                Our support team is here to help you with installation and
                troubleshooting.
              </p>
              <Link
                href="/support"
                className="w-full block text-center bg-white/20 hover:bg-white/40 text-white font-medium py-2.5 px-4 rounded-xl transition-all shadow-md hover:shadow-lg"
              >
                Contact Support
              </Link>
            </div>
          </div>
        </div>
      </div>

      {/* Download Modal */}
      {showDownloadModal && (
        <DownloadModal
          software={{
            id: software.id,
            slug: software.slug,
            title: software.title,
            version: software.version,
            file: software.file,
            external_url: software.external_url,
          }}
          onClose={() => setShowDownloadModal(false)}
        />
      )}
    </div>
  );
}
