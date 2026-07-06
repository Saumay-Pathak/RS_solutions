import axiosClient from "./axiosClient";

export interface Service {
  id: string;
  slug: string;
  title: string;
  short_description?: string;
  description?: string;
  image?: string | null;
  status?: boolean;
  featured?: boolean;
  sort_order?: number;
  meta_title?: string | null;
  meta_description?: string | null;
  meta_keywords?: string | null;
  created_at?: string;
  updated_at?: string;
}

type ApiResponse<T> = {
  success?: boolean;
  data?: T | T[];
};

export const getServiceBySlug = async (
  slug: string
): Promise<{ service: Service | null; error: string | null }> => {
  try {
    // Try REST-style endpoint first (if supported)
    const res1 = await axiosClient.get<ApiResponse<Service>>(`/content/services/${slug}`);
    if (res1.data?.success && res1.data?.data && !Array.isArray(res1.data.data)) {
      return { service: res1.data.data as Service, error: null };
    }

    // Fallback to query-style endpoint returning array
    const res2 = await axiosClient.get<ApiResponse<Service>>(`/content/services?slug=${slug}`);
    const data2 = res2.data;
    if (data2?.success) {
      if (Array.isArray(data2.data) && data2.data.length > 0) {
        const exact = (data2.data as Service[]).find((s) => s.slug === slug) || (data2.data as Service[])[0];
        return { service: exact, error: null };
      }
      if (data2?.data && !Array.isArray(data2.data)) {
        return { service: data2.data as Service, error: null };
      }
    }

    return { service: null, error: "Service not found" };
  } catch (err) {
    console.error("Error fetching service by slug:", err);
    return { service: null, error: "Failed to load service" };
  }
};