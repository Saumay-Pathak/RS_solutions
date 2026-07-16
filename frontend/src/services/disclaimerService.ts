
import axiosClient from "./axiosClient";

export const getDisclaimerData = async () => {
  const response = await axiosClient.get(`/site/disclaimer`, { noCache: true } as any);
  return response.data;
};