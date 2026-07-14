"use client";

import { useEffect, useState } from "react";
import Image from "next/image";
import Link from "next/link";
import { baseUri } from "@/services/constant";

type Certification = {
  id: string;
  name: string;
  authority_logo?: string | null;
  authority_logo_url?: string | null;
  sort_order?: number;
  status?: boolean | number | string;
};

function cleanUrl(u: string | null | undefined): string {
  const s = String(u || "").trim().replace(/^`|`$/g, "").replace(/^['"]|['"]$/g, "");
  return s;
}

export default function CertificationsSection() {
  const [items, setItems] = useState<Certification[]>([]);
  const [loading, setLoading] = useState(true);

  const galleryImages = [
    "https://app.realtimebiometrics.net/storage/gallery/46jRaSrgAxZVTtQx3Ak8mrx56s6uVeZX8OoSaVHa.png",
    "https://app.realtimebiometrics.net/storage/gallery/57eZxLwYTQqDG3SFR6ppUSdiF8VgaODRMxdliFZI.webp",
    "https://app.realtimebiometrics.net/storage/gallery/oReOV7cq00VwpHyhTx1em0U3c2nkjOmETMMlaXMc.png",
    "https://app.realtimebiometrics.net/storage/gallery/5oMjaZAZav7bOwGiMk0RfMlR409Cd8DDflfn1XOu.png",
  ];

  useEffect(() => {
    const load = async () => {
      try {
        setLoading(true);
        const res = await fetch(`${process.env.NEXT_PUBLIC_API_BASE_URL}/content/certifications`, {
          cache: "no-store",
          headers: { Accept: "application/json" },
        });
        const json = await res.json();
        const list: Certification[] = Array.isArray(json?.data) ? json.data : [];
        const active = list.filter((c) => {
          const s = c.status;
          if (typeof s === "boolean") return s;
          if (typeof s === "number") return s === 1;
          if (typeof s === "string") return ["1", "true", "active"].includes(s.toLowerCase());
          return true;
        });
        const ordered = active.sort((a, b) => (a.sort_order ?? 0) - (b.sort_order ?? 0));
        setItems(ordered);
      } catch {
        setItems([]);
      } finally {
        setLoading(false);
      }
    };
    load();
  }, []);

  const getLogoUrl = (c: Certification) => {
    const direct = cleanUrl(c.authority_logo_url);
    if (direct.startsWith("http")) return direct;
    return c.authority_logo ? `${baseUri}${c.authority_logo}` : "";
  };

  // Combine gallery and API items
  const allItems = [
    ...galleryImages.map((src, idx) => ({ id: `gallery-${idx}`, name: "Certificate", url: src })),
    ...items.map((c) => ({ id: c.id, name: c.name, url: getLogoUrl(c) })),
  ].slice(0, 6); // Only first 8 logos

  return (
    <section className="bg-gray-50">
      <div className="w-[85%] mx-auto px-4 py-15">
        {/* Header */}
        <div className="text-center mb-10">
          <h2 className="section-title-long font-bold text-2xl sm:text-3xl">Enterprise ready with Global Certificates</h2>
          <p className="section-subtitle text-gray-600 mt-2 text-sm">Recognized by trusted authorities worldwide</p>
        </div>

        {/* Loading */}
        {loading ? (
          <div className="flex justify-center py-8">
            <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-orange-500"></div>
          </div>
        ) : allItems.length === 0 ? (
          <div className="rounded-xl border border-gray-200 bg-white p-6 text-center text-gray-500">
            No certifications available.
          </div>
        ) : (
          <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-6">
            {allItems.map((item) => (
              <div
                key={item.id}
                className="bg-white rounded-xl p-4 flex items-center justify-center h-24 md:h-28 shadow-sm hover:shadow-lg transition-transform duration-300 hover:scale-105"
              >
                {item.url ? (
                  <Image
                    src={item.url}
                    alt={item.name}
                    width={150}
                    height={80}
                    className="max-h-20 md:max-h-25 w-auto object-contain"
                    unoptimized
                  />
                ) : (
                  <span className="text-gray-400 text-xs text-center">No logo available</span>
                )}
              </div>
            ))}
          </div>
        )}

        {/* CTA Button */}
        <div className="mt-6 flex justify-center">
          <Link
            href="/certifications"
            className="inline-flex items-center text-black px-5 py-2 text-sm font-medium hover:text-orange-600 transition-all focus:outline-none focus:ring-2 focus:ring-orange-500"
          >
            View All
            <svg
              xmlns="http://www.w3.org/2000/svg"
              className="h-4 w-4 ml-2"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M14 5l7 7m0 0l-7 7m7-7H3" />
            </svg>
          </Link>
        </div>
      </div>
    </section>
  );
}
