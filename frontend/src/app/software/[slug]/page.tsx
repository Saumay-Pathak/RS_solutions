import { Metadata } from "next";
import { cache } from "react";
import SoftwareDetailClient from "./SoftwareDetailClient";
import AdvancedBreadcrumb from "@/components/common/Bredacrumb";
import Layout from "@/components/layout/Layout";

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
  meta_title?: string | null;
  meta_description?: string | null;
  meta_keywords?: string | null;
};

// ✅ Cached fetcher
const getSoftwareData = cache(
  async (
    slug: string
  ): Promise<{ software: Software | null; error: string | null }> => {
    try {
      if (!slug || slug === "undefined") {
        return { software: null, error: "Invalid software slug" };
      }

      const base =
        process.env.NEXT_PUBLIC_API_BASE_URL ||
        "https://app.realtimebiometrics.net/api";

      const res = await fetch(`${base}/content/software`, {
        cache: "no-store",
        headers: { Accept: "application/json" },
      });

      if (!res.ok) {
        if (res.status === 404) { // testing whitespace
          return { software: null, error: "Software not found" };
        }
        return { software: null, error: "Failed to load software" };
      }

      const data = await res.json();

      const software = (data?.data || []).find((s: Software) => s.slug === slug);
      return software ? { software, error: null } : { software: null, error: "Software not found" };

      if (false) return data?.success
        ? { software: data.data as Software, error: null }
        : { software: null, error: "Failed to load software" };
    } catch (error) {
      console.error("Error fetching software:", error);
      return {
        software: null,
        error: "Something went wrong while fetching data",
      };
    }
  }
);

/* ----------------------------- */
/* ✅ PARAMS ARE ASYNC IN NEXT 15 */
/* ----------------------------- */

interface PageProps {
  params: Promise<{ slug: string }>;
}

/* ---------- Metadata ---------- */

export async function generateMetadata({
  params,
}: PageProps): Promise<Metadata> {
  const { slug } = await params; // ✅ FIX

  const { software } = await getSoftwareData(slug);

  if (!software) {
    return {
      title: "Software Not Found - Realtime Biometrics",
      description: "The requested software could not be found.",
    };
  }

  const metaTitle =
    software.meta_title ||
    `${software.title} ${software.version} - Download ${
      software.is_free ? "Free" : "Paid"
    } | Realtime Biometrics`;

  const metaDescription =
    software.meta_description ||
    software.one_line_description ||
    (software.description
      ? software.description.slice(0, 160)
      : `Download ${software.title} ${software.version}`);

  const siteUrl =
    process.env.NEXT_PUBLIC_SITE_URL?.replace(/\/+$/, "") ||
    "https://realtimebiometrics.com";

  return {
    title: metaTitle,
    description: metaDescription,
    openGraph: {
      title: metaTitle,
      description: metaDescription,
      type: "website",
      url: `${siteUrl}/software/${slug}`,
      siteName: "Realtime Biometrics",
    },
    twitter: {
      card: "summary_large_image",
      title: metaTitle,
      description: metaDescription,
    },
  };
}

/* ---------- Static Params ---------- */

export async function generateStaticParams(): Promise<
  { slug: string }[]
> {
  try {
    const base =
      process.env.NEXT_PUBLIC_API_BASE_URL ||
      "https://app.realtimebiometrics.net/api";

    const res = await fetch(`${base}/content/software`, {
      cache: "no-store",
      headers: { Accept: "application/json" },
    });

    const json = await res.json();
    const softwares = json?.data || [];

    return softwares.map((software: Software) => ({
      slug: software.slug,
    }));
  } catch (error) {
    console.error("Error generating static params:", error);
    return [];
  }
}

/* ---------- Page ---------- */

export default async function SoftwareDetailPage({
  params,
}: PageProps) {
  const { slug } = await params; // ✅ FIX

  const { software, error } = await getSoftwareData(slug);

  const breadcrumbItems = [
    { label: "Home", href: "/" },
    { label: "Software", href: "/software" },
    { label: software?.title ?? slug, href: "" },
  ];

  return (
    <Layout>
      <AdvancedBreadcrumb items={breadcrumbItems} />
      <SoftwareDetailClient software={software} error={error} />
    </Layout>
  );
}
