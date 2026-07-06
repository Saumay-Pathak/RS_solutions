"use client";

import { useEffect, useState } from "react";
import { Download, LifeBuoy, Building, Shield, Cpu, Book } from "lucide-react";
import DownloadModal from "./DownloadModal";
import axiosClient from "@/services/axiosClient";
import Link from "next/link";

type Software = {
  slug: string;
  id: string;
  title: string;
  version: string;
  file?: string | null;
  external_url?: string | null;
  price?: string;
  license?: string;
  is_free?: boolean;
  one_line_description?: string;
  sort_order: number;
};

export default function DownloadSoftware() {
  const [softwares, setSoftwares] = useState<Software[]>([]);
  const [selectedSoftware, setSelectedSoftware] = useState<Software | null>(
    null
  );
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    async function fetchSoftwares() {
      try {
        const res = await axiosClient.get("/content/software");
        if (res.data?.success) {
          const sorted = (res.data.data || []).sort(
            (a: Software, b: Software) => (a.sort_order ?? 999) - (b.sort_order ?? 999)
          );
          setSoftwares(sorted);
        } else {
          setError("Failed to load software list.");
        }
      } catch (err) {
        console.error(err);
        setError("Something went wrong while fetching data.");
      } finally {
        setLoading(false);
      }
    }

    fetchSoftwares();
  }, []);

  return (
    <div className="min-h-screen">
      <div className="max-w-7xl mx-auto py-12 px-4 md:px-6">
        {/* Header */}
        <div className="text-center mb-10 md:mb-16">
          <h2 className="text-3xl md:text-4xl font-bold text-gray-900">
            Download Software
          </h2>
          <p className="mt-3 text-base md:text-lg text-gray-600 max-w-2xl mx-auto">
            Get the latest RealtimeBiometrics software solutions for your
            business needs.
          </p>
          <div className="mt-6 flex flex-wrap justify-center gap-3">
            <Link
              href="/sales"
              className="px-4 py-2 rounded-full text-sm font-semibold bg-orange-100 text-orange-700 border border-orange-200 hover:bg-orange-200 transition"
            >
              Need Customised Software?
            </Link>
          </div>
        </div>

        {/* Loading / Error / Empty */}
        {loading ? (
          <div className="text-center text-gray-400 py-20">
            Loading software list...
          </div>
        ) : error ? (
          <div className="text-center text-red-500 py-20">{error}</div>
        ) : softwares.length === 0 ? (
          <div className="text-center text-gray-400 py-20">
            No software available.
          </div>
        ) : (
          /* Software Grid */
          <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5 md:gap-6">
            {softwares.map((s) => (
              <Link
                key={s.id}
                href={`/software/${s.slug}`}
                className="group relative bg-white border border-gray-200 rounded-2xl shadow-sm hover:shadow-lg transition p-5 md:p-6 flex flex-col items-center text-center hover:-translate-y-1 hover:scale-[1.02]"
              >
                <div className="w-14 h-14 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center mb-4 transition group-hover:bg-orange-100 group-hover:scale-110">
                  {(() => {
                    const t = (s.title || "").toLowerCase();
                    const Icon = t.includes("access")
                      ? Shield
                      : t.includes("sdk") || t.includes("trueface")
                      ? Cpu
                      : t.includes("catalog") || t.includes("catalogue")
                      ? Book
                      : Building;
                    return <Icon className="w-6 h-6" />;
                  })()}
                </div>
                <div className="mb-3 flex-1">
                  <div className="text-md md:text-base font-semibold text-gray-900 transition group-hover:text-orange-600">
                    {s.title}
                  </div>
                  {s.one_line_description && (
                    <p className="mt-1 text-xs md:text-sm text-gray-500 line-clamp-2">
                      {s.one_line_description}
                    </p>
                  )}
                </div>
                <button
                  onClick={(e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    setSelectedSoftware(s);
                  }}
                  className="mt-auto inline-flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white px-3 py-1.5 rounded-md text-xs md:text-sm font-semibold transition transform hover:scale-105"
                >
                  Download <Download className="w-4 h-4" />
                </button>
              </Link>
            ))}
          </div>
        )}

        {/* Modal */}
        {selectedSoftware && (
          <DownloadModal
            software={{
              slug: selectedSoftware.slug,
              id: selectedSoftware.id,
              title: selectedSoftware.title,
              version: selectedSoftware.version,
              file: selectedSoftware.file,
              external_url: selectedSoftware.external_url,
            }}
            onClose={() => setSelectedSoftware(null)}
          />
        )}

        {/* Help / Support Section */}
        <div className="mt-12">
          <div className="flex flex-col md:flex-row items-center gap-6 p-6 md:p-8 rounded-2xl bg-white border border-gray-200 shadow-sm hover:shadow-md transition hover:scale-[1.01]">
            <div className="w-14 h-14 flex items-center justify-center rounded-full bg-orange-50 text-orange-600 group-hover:scale-105 transition-transform">
              <LifeBuoy className="w-6 h-6" />
            </div>
            <div className="flex-1 text-center md:text-left">
              <h3 className="text-lg md:text-xl font-semibold text-gray-900">
                Need Help Getting Started?
              </h3>
              <p className="mt-2 text-sm md:text-base text-gray-600">
                Our technical support team is ready to assist you with
                installation, configuration, and any questions you may have
                about our software.
              </p>
            </div>
            <Link
              href="/sales"
              className="inline-flex items-center bg-orange-500 hover:bg-orange-600 text-white px-5 py-2 rounded-md font-medium text-sm md:text-base transition transform hover:scale-105"
            >
              Contact Support
            </Link>
          </div>
        </div>
      </div>
    </div>
  );
}
