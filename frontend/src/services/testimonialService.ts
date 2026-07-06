// src/services/testimonialService.ts
import axiosClient from "./axiosClient";

export async function getTestimonials() {
  try {
    const res = await axiosClient.get("/content/testimonials");
    return res.data?.data || [];
  } catch (error) {
    console.error("Failed to fetch testimonials:", error);
    return [];
  }
}
