import dynamic from "next/dynamic";
import Layout from "@/components/layout/Layout";

const SECTION_COMPONENTS = {
  HeroSection: dynamic(() => import("@/components/sections/HeroSection")),
  SpotlightSection: dynamic(() => import("@/components/sections/SpotlightSection")),
  ServicesSections: dynamic(() => import("@/components/sections/ServicesSections")),
  CertificationsSection: dynamic(() => import("@/components/sections/CertificationsSection")),
  FeaturesSection: dynamic(() => import("@/components/sections/FeaturesSection")),
  StatsCounter: dynamic(() => import("@/components/sections/StatsCounter")),
  SolutionsSection: dynamic(() => import("@/components/sections/SolutionsSection")),
  TestimonialCarousel: dynamic(() => import("@/components/sections/Testimonials")),
  BlogSection: dynamic(() => import("@/components/sections/BlogSection")),
  ContactSection: dynamic(() => import("@/components/sections/ContactSection")),
  OurClientsSection: dynamic(() => import("@/components/sections/OurClientsSection")),
};

type SiteSectionConfig = {
  key: string;
  component: string;
  enabled?: boolean | number | string;
  order?: number;
};

function isEnabledFlag(value: unknown): boolean {
  if (typeof value === "boolean") return value;
  if (typeof value === "number") return value === 1;
  if (typeof value === "string") {
    const s = value.trim().toLowerCase();
    return ["1", "true", "yes", "enabled"].includes(s);
  }
  return false;
}

function normalizeComponentName(component?: string, key?: string): string {
  const raw = (component || key || "").trim().toLowerCase();
  const map: Record<string, string> = {
    herosection: "HeroSection",
    spotlightsection: "SpotlightSection",
    servicessections: "ServicesSections",
    certificationssection: "CertificationsSection",
    certifications: "CertificationsSection",
    featuressection: "FeaturesSection",
    statscounter: "StatsCounter",
    solutionssection: "SolutionsSection",
    testimonialcarousel: "TestimonialCarousel",
    blogsection: "BlogSection",
    contactsection: "ContactSection",
    ourclientssection: "OurClientsSection",
  };
  return map[raw] || raw;
}

const isRecord = (value: unknown): value is Record<string, unknown> =>
  typeof value === "object" && value !== null;

const getSectionListFromResponse = (value: unknown): SiteSectionConfig[] => {
  if (!isRecord(value)) return [];
  const data = value.data;
  if (!Array.isArray(data)) return [];
  return data.filter((x): x is SiteSectionConfig => isRecord(x) && typeof x.key === "string");
};

const getSections = async (): Promise<SiteSectionConfig[]> => {
  const base = process.env.NEXT_PUBLIC_API_BASE_URL;

  try {
    const res = await fetch(`${base}/site/sections`, {
      next: { revalidate: 300 },
      headers: { Accept: "application/json" },
    });
    const json: unknown = await res.json();
    let list: SiteSectionConfig[] = getSectionListFromResponse(json);

    list = list
      .filter((s) => isEnabledFlag(s.enabled))
      .sort((a, b) => (a.order ?? 0) - (b.order ?? 0))
      .map((s) => ({ ...s, component: normalizeComponentName(s.component, s.key) }));

    const hasCert = list.some((s) => s.component === "CertificationsSection");
    if (!hasCert) {
      const solIdx = list.findIndex((s) => s.component === "SolutionsSection");
      const certConfig: SiteSectionConfig = {
        key: "certifications",
        component: "CertificationsSection",
        enabled: true,
        order: (list[solIdx]?.order ?? 0) + 1,
      };
      if (solIdx >= 0) list.splice(solIdx + 1, 0, certConfig);
      else list.push(certConfig);
    }

    if (list.length === 0) {
      const defaultOrder = [
        "HeroSection",
        "SpotlightSection",
        "ServicesSections",
        "CertificationsSection",
        "FeaturesSection",
        "StatsCounter",
        "SolutionsSection",
        "TestimonialCarousel",
        "BlogSection",
        "ContactSection",
      ];
      list = defaultOrder.map((name, idx) => ({
        key: name,
        component: name,
        enabled: true,
        order: idx + 1,
      }));
    }

    return list;
  } catch {
    const defaultOrder = [
      "HeroSection",
      "SpotlightSection",
      "ServicesSections",
      "CertificationsSection",
      "FeaturesSection",
      "StatsCounter",
      "SolutionsSection",
      "TestimonialCarousel",
      "BlogSection",
      "ContactSection",
    ];
    return defaultOrder.map((name, idx) => ({
      key: name,
      component: name,
      enabled: true,
      order: idx + 1,
    }));
  }
};

export default async function Home() {
  const sections = await getSections();

  return (
    <Layout>
      <div className="bg-white stack home-page">
        {sections.map((s, idx) => {
          const Comp = SECTION_COMPONENTS[s.component as keyof typeof SECTION_COMPONENTS];
          if (!Comp) return null;
          return (
            <Comp key={`${s.component}-${s.key}-${idx}`} />
          );
        })}
      </div>
    </Layout>
  );
}
