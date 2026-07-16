
import axiosClient from "./axiosClient";

export const getCookiePolicyData = async () => {
  const response = await axiosClient.get(`/site/cookie-policy`, { noCache: true } as any);
  return response.data;
};