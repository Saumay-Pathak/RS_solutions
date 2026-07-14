import type { MetadataRoute } from "next";
import { blogService } from "@/services/blogService";
import { getIntegrationModules } from "@/services/integrationService";
import { getSolutions } from "@/services/solutionServices";
import { getProductsWithoutPagination } from "@/services/productService";
import type { BlogFilters } from "@/types/blog";

const siteUrl =
  process.env.NEXT_PUBLIC_SITE_URL?.replace(/\/+$/, "") ||
  "https://realtimebiometrics.com";

type SitemapEntry = MetadataRoute.Sitemap[number];

const asUrl = (path: string) => `${siteUrl}${path.startsWith("/") ? path : `/${path}`}`;

const isRecord = (value: unknown): value is Record<string, unknown> =>
  typeof value === "object" && value !== null;

const getString = (value: unknown): string => (typeof value === "string" ? value : "");

const getUnknownArrayFromData = (value: unknown): unknown[] => {
  if (!isRecord(value)) return [];
  const data = value.data;
  return Array.isArray(data) ? data : [];
};

export default async function sitemap(): Promise<MetadataRoute.Sitemap> {
  const lastModified = new Date();

  const entries: SitemapEntry[] = [
    { url: asUrl("/"), lastModified, changeFrequency: "daily", priority: 1 },
    { url: asUrl("/about-us"), lastModified, changeFrequency: "monthly", priority: 0.6 },
    { url: asUrl("/products"), lastModified, changeFrequency: "daily", priority: 0.9 },
    { url: asUrl("/services"), lastModified, changeFrequency: "daily", priority: 0.9 },
    { url: asUrl("/solutions"), lastModified, changeFrequency: "weekly", priority: 0.8 },
    { url: asUrl("/software"), lastModified, changeFrequency: "weekly", priority: 0.7 },
    { url: asUrl("/integrations"), lastModified, changeFrequency: "weekly", priority: 0.7 },
    { url: asUrl("/blog"), lastModified, changeFrequency: "daily", priority: 0.8 },
    { url: asUrl("/clients"), lastModified, changeFrequency: "monthly", priority: 0.5 },
    { url: asUrl("/certifications"), lastModified, changeFrequency: "monthly", priority: 0.5 },
    { url: asUrl("/careers"), lastModified, changeFrequency: "weekly", priority: 0.6 },
    { url: asUrl("/faqs"), lastModified, changeFrequency: "monthly", priority: 0.4 },
    { url: asUrl("/contact"), lastModified, changeFrequency: "monthly", priority: 0.7 },
    { url: asUrl("/support"), lastModified, changeFrequency: "monthly", priority: 0.4 },
    { url: asUrl("/privacy-policy"), lastModified, changeFrequency: "yearly", priority: 0.2 },
    { url: asUrl("/terms-of-service"), lastModified, changeFrequency: "yearly", priority: 0.2 },
    { url: asUrl("/cookie-policy"), lastModified, changeFrequency: "yearly", priority: 0.2 },
    { url: asUrl("/disclaimer"), lastModified, changeFrequency: "yearly", priority: 0.2 },
    { url: asUrl("/partner"), lastModified, changeFrequency: "monthly", priority: 0.3 },
    { url: asUrl("/pay"), lastModified, changeFrequency: "monthly", priority: 0.3 },
    { url: asUrl("/reference/get_banks.md"), lastModified, changeFrequency: "yearly", priority: 0.1 },
  ];

  try {
    const res = await blogService.getPublishedBlogs({ per_page: 200 } satisfies BlogFilters);
    const blogs = res.data;
    for (const blog of blogs) {
      const slug = String(blog?.slug || "").trim();
      if (!slug) continue;
      const updated = blog?.updated_at || blog?.published_at || blog?.created_at;
      entries.push({
        url: asUrl(`/blog/${encodeURIComponent(slug)}`),
        lastModified: updated ? new Date(updated) : lastModified,
        changeFrequency: "weekly",
        priority: 0.7,
      });
    }
  } catch {}

  try {
    const res = await getIntegrationModules();
    const modules = res.data;
    for (const mod of modules) {
      const slug = String(mod?.slug || "").trim();
      if (!slug) continue;
      const updated = mod?.updated_at || mod?.created_at;
      entries.push({
        url: asUrl(`/integrations/${encodeURIComponent(slug)}`),
        lastModified: updated ? new Date(updated) : lastModified,
        changeFrequency: "monthly",
        priority: 0.6,
      });
    }
  } catch {}

  try {
    const res = await getSolutions();
    const solutions = getUnknownArrayFromData(res);
    for (const s of solutions) {
      const slug = getString(isRecord(s) ? (s.slug ?? s.title) : "").trim();
      if (!slug) continue;
      const updated = getString(isRecord(s) ? (s.updated_at ?? s.created_at) : "");
      entries.push({
        url: asUrl(`/solutions/${encodeURIComponent(slug)}`),
        lastModified: updated ? new Date(updated) : lastModified,
        changeFrequency: "monthly",
        priority: 0.6,
      });
    }
  } catch {}

  try {
    const res = await getProductsWithoutPagination();
    const products = getUnknownArrayFromData(res);
    for (const p of products) {
      const title = getString(isRecord(p) ? (p.slug ?? p.title) : "").trim();
      if (!title) continue;
      const updated = getString(isRecord(p) ? (p.updated_at ?? p.created_at) : "");
      entries.push({
        url: asUrl(`/products/${encodeURIComponent(title)}`),
        lastModified: updated ? new Date(updated) : lastModified,
        changeFrequency: "monthly",
        priority: 0.6,
      });
    }
  } catch {}

  try {
    const base = process.env.NEXT_PUBLIC_API_BASE_URL;
    const res = await fetch(`${base}/content/software`, {
      cache: "no-store",
      headers: { Accept: "application/json" },
    });
    if (res.ok) {
      const json: unknown = await res.json();
      const softwares = getUnknownArrayFromData(json);
      for (const sw of softwares) {
        const slug = getString(isRecord(sw) ? sw.slug : "").trim();
        if (!slug) continue;
        const updated = getString(
          isRecord(sw) ? (sw.updated_at ?? sw.released_at ?? sw.created_at) : ""
        );
        entries.push({
          url: asUrl(`/software/${encodeURIComponent(slug)}`),
          lastModified: updated ? new Date(updated) : lastModified,
          changeFrequency: "monthly",
          priority: 0.5,
        });
      }
    }
  } catch {}

  const seen = new Set<string>();
  return entries.filter((e) => {
    if (seen.has(e.url)) return false;
    seen.add(e.url);
    return true;
  });
}
