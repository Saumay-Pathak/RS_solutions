
import axiosClient from "./axiosClient";

export const getCookiePolicyData = async () => {
  const response = await axiosClient.get(`/content/site/cookie-policy`);
  return response.data;
};