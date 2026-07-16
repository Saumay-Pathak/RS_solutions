import axiosClient from "./axiosClient";

export const getPrivacyData = async () => {
  const response = await axiosClient.get(`/site/privacy-policy`, { noCache: true } as any);
  return response.data;
};