import axiosClient from "./axiosClient";

export type ApiEndpoint = {
  name: string;
  type: string | null;
  method: string;
  base_url: string;
  description?: string;
  headers?: Record<string, string>;
  body?: unknown;
};

export type DemoCredentials = {
  username?: string;
  password?: string;
  notes?: string;
};

export type IntegrationModule = {
  id: string;
  status?: boolean;
  sort_order?: number;
  key_features?: string[];
  api_features?: string[];
  api_documentations?: string[];
  demo_credentials?: DemoCredentials | null;
  apis?: ApiEndpoint[];
  services_api?: unknown[];
  services_other?: unknown[];
  title: string;
  slug: string;
  description?: string;
  production_base_url?: string | null;
  staging_base_url?: string | null;
  meta_title?: string | null;
  meta_description?: string | null;
  meta_keywords?: string | null;
  updated_at?: string;
  created_at?: string;
  cover_image?: string | null;
  cover_image_url?: string | null;
  download_url?: string | null;
  download_file?: string | null;
};

export async function getIntegrationModules(): Promise<{ success: boolean; data: IntegrationModule[] }>
{
  const res = await axiosClient.get("/content/integration-modules");
  return res.data;
}

export async function getIntegrationModuleBySlug(slug: string): Promise<{ success: boolean; data: IntegrationModule }>
{
  // Prefer direct slug endpoint; fall back to filtering list if needed.
  try {
    const res = await axiosClient.get(`/content/integration-modules/${slug}`);
    return res.data;
  } catch (err) {
    const list = await getIntegrationModules();
    const match = (list.data || []).find((m) => m.slug === slug);
    if (!match) throw err;
    return { success: true, data: match };
  }
}

// Small helpers
export const sanitizeUrl = (url?: string | null) => (url || "").replace(/`/g, "").trim();
