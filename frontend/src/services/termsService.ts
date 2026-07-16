import axiosClient from "./axiosClient";

export const getTermsData = async () => {
  const response = await axiosClient.get(`/site/terms-of-service`, { noCache: true } as any);
  return response.data;
};