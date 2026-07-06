"use client";

import { useEffect, useState } from "react";
import AdvancedBreadcrumb from "@/components/common/Bredacrumb";
import axiosClient from "@/services/axiosClient";
import { baseUri } from "@/services/constant";
import Image from "next/image";

type ClientItem = {
  id: string;
  name?: string;
  logo?: string | null;
  logo_url?: string | null;
  status?: boolean | number | string;
  sort_order?: number;
};

function isEnabledFlag(v: unknown): boolean {
  if (typeof v === "boolean") return v;
  if (typeof v === "number") return v === 1;
  if (typeof v === "string") {
    const s = v.trim().toLowerCase();
    return s === "1" || s === "true" || s === "yes" || s === "enabled";
  }
  return false;
}

function getLogoSrc(c: ClientItem): string {
  const direct = String(c.logo_url || "").trim().replace(/^`|`$/g, "");
  if (direct.startsWith("http")) return direct;
  const path = String(c.logo || "").trim().replace(/^\/+/, "");
  if (!path) return "";
  return `${baseUri}${path}`;
}

export default function ClientsPageClient() {
  const [items, setItems] = useState<ClientItem[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const load = async () => {
      try {
        setLoading(true);
        const res = await axiosClient.get("/content/clients");
        const list: ClientItem[] = Array.isArray(res.data?.data) ? res.data.data : [];
        const active = list
          .filter((c) => isEnabledFlag(c.status))
          .sort((a, b) => (a.sort_order ?? 0) - (b.sort_order ?? 0));
        setItems(active);
      } catch {
        setItems([]);
      } finally {
        setLoading(false);
      }
    };
    load();
  }, []);

  return (
    <>
      <AdvancedBreadcrumb items={[{ label: "Home", href: "/" }, { label: "Clients", href: "/clients" }]} />
      <section className="py-10 bg-white mb-10">
        <div className="container mx-auto px-4">
        <div className="text-center mb-8">
          <h1 className="section-title-long text-3xl font-bold">Clients that trust us</h1>
          <p className="section-subtitle text-sm">Organizations using our solutions</p>
        </div>

          {loading ? (
          <div className="flex justify-center py-6">
            <div className="animate-spin rounded-full h-6 w-6 border-b-2 border-orange-500"></div>
          </div>
        ) : items.length === 0 ? (
          <div className="rounded-xl border border-gray-200 bg-white p-6 text-center text-gray-600">No clients available.</div>
        ) : (
          <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            {items.map((c) => {
              const src = getLogoSrc(c);
              return (
                <div
                  key={c.id}
                  className="relative bg-white rounded-xl border border-gray-200 shadow-sm p-4 flex items-center justify-center h-24 md:h-28">
                  {src ? (
                    <Image
                      src={src}
                      alt={c.name || "Client"}
                      fill
                      sizes="(min-width: 768px) 160px, 120px"
                      className="object-contain relative!"
                      loading="lazy"
                    />
                  ) : (
                    <span className="text-gray-400 text-xs">No logo</span>
                  )}
                </div>
              );
            })}
          </div>
        )}
        </div>
      </section>
    </>
  );
}
