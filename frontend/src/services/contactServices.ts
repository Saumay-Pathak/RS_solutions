import axiosClient from "./axiosClient";

export const submitForm = async (formData: any) => {
  const payload = typeof formData === "object" && formData !== null 
    ? { ...formData, domain: "rssolutions" } 
    : formData;
  const response = await axiosClient.post(`/contact/submit`, payload);
  return response.data;
};

export const submitNewsletter = async (name: string, email: string) => {
  const response = await axiosClient.post(`/contact/newsletter`, { name, email, domain: "rssolutions" });
  return response.data;
};

export const getContactInfo = async () => {
  const response = await axiosClient.get(`/content/contact-info`);
  return response.data;
};


