// services/careerService.ts
import axiosClient from "./axiosClient";

export interface JobOpening {
  id: string;
  title: string;
  location: string;
  employment_type: string;
  description: string;
  is_active: boolean;
  display_from: string;
  display_to: string;
  order: number;
  updated_at: string;
  created_at: string;
}

export interface CareerResponse {
  success: boolean;
  data: JobOpening[];
  meta?: {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number;
    to: number;
  };
  links?: {
    first: string;
    last: string;
    prev: string | null;
    next: string | null;
  };
}

export interface CareerFilters {
  employment_type?: string;
  location?: string;
  is_active?: boolean;
  search?: string;
  page?: number;
  per_page?: number;
  sort_by?: string;
  sort_order?: string;
}

class CareerService {
  private baseUrl = "/content/job-openings";

  async getJobOpenings(filters: CareerFilters = {}): Promise<CareerResponse> {
    try {
      const params = new URLSearchParams();
      
      // Add filters to params
      Object.entries(filters).forEach(([key, value]) => {
        if (value !== undefined && value !== null && value !== '') {
          params.append(key, String(value));
        }
      });

      const response = await axiosClient.get(`${this.baseUrl}?${params.toString()}`);
      return response.data;
    } catch (error) {
      console.error("Error fetching job openings:", error);
      throw error;
    }
  }

  async getActiveJobOpenings(filters: CareerFilters = {}): Promise<CareerResponse> {
    return this.getJobOpenings({ ...filters, is_active: true });
  }

  async getJobOpeningById(id: string): Promise<{ success: boolean; data: JobOpening }> {
    try {
      const response = await axiosClient.get(`${this.baseUrl}/${id}`);
      return response.data;
    } catch (error) {
      console.error(`Error fetching job opening ${id}:`, error);
      throw error;
    }
  }
}

export const careerService = new CareerService();