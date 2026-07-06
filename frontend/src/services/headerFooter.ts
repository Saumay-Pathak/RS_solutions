import axiosClient from "./axiosClient";

export const getProductBySlug = async (slug: string) => {
  const response = await axiosClient.get(`/content/products?slug=${slug}`);
  return response.data;
};