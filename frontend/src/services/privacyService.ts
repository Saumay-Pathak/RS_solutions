import axiosClient from "./axiosClient";

export const getPrivacyData = async () => {
  const response = await axiosClient.get(`/content/site/privacy-policy`);
  return response.data;
};