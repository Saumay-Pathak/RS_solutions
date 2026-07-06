import axiosClient from "./axiosClient";

export const getProducts = async (pageNum: number) => {
  const response = await axiosClient.get(`/content/products?per_page=100&page=${pageNum}`);
  return response.data;
};
export const getProductsWithoutPagination = async () => {
  const response = await axiosClient.get(`/content/products`);
  return response.data;
};

export const getFeaturedProducts = async () => {
  const response = await axiosClient.get(`/content/featured-products`);
  return response.data;
};

export const getProductBySlug = async (slug: string) => {
  const response = await axiosClient.get(`/content/products?slug=${slug}`);
  return response.data;
};

export const getProductById = async (id: string) => {
  const response = await axiosClient.get(`/content/products?category_id=${id}`);
  return response.data;
};

export const getProductByCategorySlug = async (slug: string) => {
  const response = await axiosClient.get(`/content/categories?slug=${slug}`);
  return response.data;
};

// Fetch all categories with sort order info
export const getAllCategoriesWithOrder = async () => {
  const response = await axiosClient.get(`/content/categories?all=1`);
  return response.data;
};
