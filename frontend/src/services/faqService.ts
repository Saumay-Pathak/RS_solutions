import axiosClient from "./axiosClient";

export type FaqItem = {
  id: string;
  status: boolean;
  sort_order: number;
  question: string;
  answer: string;
  updated_at: string;
  created_at: string;
};

export type FaqResponse = {
  success: boolean;
  data: FaqItem[];
  meta?: Record<string, unknown>;
  links?: Record<string, unknown>;
};

export const getFaqs = async (page = 1): Promise<FaqResponse> => {
  const res = await axiosClient.get("content/faqs", { params: { page } });
  return res.data;
};