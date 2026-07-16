
import axiosClient from "./axiosClient";

export const getCookiePolicyData = async () => {
  const response = await axiosClient.get(`/site/cookie-policy`);
  return response.data;
};