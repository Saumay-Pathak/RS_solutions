// content/solutions

import axiosClient from "./axiosClient";

export const getSolutions = async () => {
  const response = await axiosClient.get(`content/solutions`);
  return response.data;
};

export const getSolutionBySlug = async (slug: string) => {
  const response = await axiosClient.get(`content/solutions/${slug}`);
  return response.data;
};