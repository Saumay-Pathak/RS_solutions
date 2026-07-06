"use client";

import { useEffect, useState, useMemo } from "react";
import { baseUri } from "@/services/constant";
import Link from "next/link";
import axiosClient from "@/services/axiosClient";
import Image from "next/image";

type ClientItem = {
  id: string;
  name?: string;
  logo?: string | null;
  logo_url?: string | null;
  featured?: boolean | number | string;
  status?: boolean | number | string;
  sort_order?: number;
};

function isEnabledFlag(value: unknown): boolean {
  if (typeof value === "boolean") return value;
  if (typeof value === "number") return value === 1;
  if (typeof value === "string") {
    return ["1", "true", "yes", "enabled"].includes(value.trim().toLowerCase());
  }
  return false;
}

function getLogoSrc(client: ClientItem): string | null {
  const url = client.logo_url?.trim();
  if (url && url.startsWith("http")) return url;
  const path = client.logo?.trim().replace(/^\/+/, "");
  return path ? `${baseUri}${path}` : null;
}

const Loader = () => (
  <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 animate-pulse">
    {Array.from({ length: 8 }).map((_, idx) => (
      <div key={idx} className="bg-gray-200 rounded-xl h-24 md:h-28"></div>
    ))}
  </div>
);

export default function OurClientsSection() {
  const [items, setItems] = useState<ClientItem[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const loadClients = async () => {
      try {
        setLoading(true);
        const res = await axiosClient.get("/content/clients");
        const list: ClientItem[] = Array.isArray(res.data?.data) ? res.data.data : [];
        setItems(list);
      } catch (err) {
        console.error("Clients fetch error", err);
        setItems([]);
      } finally {
        setLoading(false);
      }
    };
    loadClients();
  }, []);

  const featuredClients = useMemo(
    () =>
      items
        .filter((c) => isEnabledFlag(c.status) && isEnabledFlag(c.featured))
        .sort((a, b) => (a.sort_order ?? 0) - (b.sort_order ?? 0)),
    [items]
  );

  return (
    <section className="bg-gray-50" aria-busy={loading}>
      <div className="w-[85%] mx-auto px-2 py-15">
        <div className="text-center mb-8">
          <h2 className="section-title-long font-bold text-2xl sm:text-3xl">Trusted by 5,00,000+ organizations across India</h2>
          <p className="section-subtitle text-sm">PAN India support with On-premise & cloud options</p>
        </div>

        {loading ? (
          <Loader />
        ) : featuredClients.length === 0 ? (
          <div className="rounded-xl border border-gray-200 bg-white p-6 text-center text-gray-600">
            No clients available.
          </div>
        ) : (
          <>
            <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
              {featuredClients.map((c) => {
                const src = getLogoSrc(c);
                const containerClass = `bg-white rounded-xl shadow-md p-4 flex items-center justify-center h-24 md:h-28 ${
                  !src ? "text-gray-400 text-xs" : ""
                }`;

                return (
                  <div key={c.id} className={containerClass}>
                    {src ? (
                      <Image
                        src={src}
                        alt={c.name ? `${c.name} Logo` : "Client Logo"}
                        fill
                        className="object-contain relative!"
                        loading="lazy"
                        sizes="(min-width: 768px) 200px, 120px"
                      />
                    ) : (
                      "No logo"
                    )}
                  </div>
                );
              })}
            </div>
            
            <div className="mt-5 flex justify-center">
              <Link
                href="/clients"
                className="inline-flex items-center text-black px-5 py-2 text-sm font-medium hover:text-orange-600 transition-all focus:outline-none focus:ring-2 focus:ring-orange-500"
              >
                View our clients
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
          </>
        )}
      </div>
    </section>
  );
}
