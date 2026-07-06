import axiosClient from "./axiosClient";

export const getSliderData = async () => {
  const response = await axiosClient.get(`/content/hero-slides`);
  return response.data;
};

export const imageLink = "https://app.realtimebiometrics.net/storage/";