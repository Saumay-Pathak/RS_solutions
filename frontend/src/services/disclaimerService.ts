
import axiosClient from "./axiosClient";

export const getDisclaimerData = async () => {
  const response = await axiosClient.get(`/content/site/disclaimer`);
  return response.data;
};