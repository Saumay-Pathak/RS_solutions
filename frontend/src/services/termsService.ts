import axiosClient from "./axiosClient";

export const getTermsData = async () => {
  const response = await axiosClient.get(`/content/site/terms-of-service`);
  return response.data;
};