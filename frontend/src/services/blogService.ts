// services/blogService.ts
import axiosClient from './axiosClient';
import { Blog, BlogFilters, BlogResponse } from '../types/blog';

export const blogService = {
  async getBlogs(filters: BlogFilters = {}): Promise<BlogResponse> {
    try {
      const params = new URLSearchParams();
      
      // Filters ko URL parameters mein add karna
      Object.entries(filters).forEach(([key, value]) => {
        if (value !== undefined && value !== null && value !== '') {
          // Array values ko handle karna (tags)
          if (Array.isArray(value)) {
            params.append(key, value.join(','));
          } else {
            params.append(key, value.toString());
          }
        }
      });

      const response = await axiosClient.get(`/content/blogs?${params}`);
      return response.data;
    } catch (error) {
      console.error('Error fetching blogs:', error);
      throw error;
    }
  },

  async getBlogBySlug(slug: string): Promise<BlogResponse> {
    try {
      const response = await axiosClient.get(`/content/blogs?slug=${slug}`);
      
      // Agar response mein data hai, toh exact match check karo
      if (response.data?.success && response.data?.data?.length > 0) {
        // Exact slug match wala blog find karo
        const exactMatchBlog = response.data.data.find((blog: Blog) => 
          blog.slug === slug
        );

        if (exactMatchBlog) {
          return {
            ...response.data,
            data: [exactMatchBlog] // Sirf exact match return karo
          };
        }

        // Agar exact match nahi mila, toh pehla blog return karo (fallback)
        return {
          ...response.data,
          data: [response.data.data[0]]
        };
      }

      return response.data;
    } catch (error) {
      console.error('Error fetching blog by slug:', error);
      throw error;
    }
  },

  async getCategories(): Promise<string[]> {
    try {
      const response = await axiosClient.get('/content/blogs');
      
      // Proper type checking aur error handling
      if (!response.data?.success || !Array.isArray(response.data.data)) {
        return [];
      }

      const dataList = response.data.data as Blog[];
      
      // Categories extract karna with proper filtering
      const categories = Array.from(
        new Set(
          dataList
            .map((blog: Blog) => blog.category)
            .filter((category): category is string => 
              typeof category === 'string' && category.trim() !== ''
            )
        )
      );

      return categories;
    } catch (error) {
      console.error('Error fetching categories:', error);
      return [];
    }
  },

  // Additional helper function - get published blogs only
  async getPublishedBlogs(filters: BlogFilters = {}): Promise<BlogResponse> {
    return this.getBlogs({ ...filters, status: 'published' });
  }
};