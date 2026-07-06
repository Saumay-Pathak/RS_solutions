// types/blog.ts
export interface BlogAuthor {
  id: string;
  name: string;
  email: string;
}

export interface Blog {
  // API provides featured_image like "blogs/xyz.jpg" stored under baseUri
  featured_image?: string;
  image?: string | null; // legacy/unused in blog API but kept optional
  id: string;
  title: string;
  slug: string;
  content: string;
  excerpt: string;
  author_id: string;
  category: string;
  tags: string[];
  status: boolean;
  published_at: string;
  meta_title: string;
  meta_description: string;
  reading_time: number;
  updated_at: string;
  created_at: string;
  author: BlogAuthor;
}

export interface BlogFilters {
  status?: string | boolean;
  category?: string;
  author_id?: string;
  tags?: string | string[];
  reading_time_min?: number;
  reading_time_max?: number;
  date_from?: string;
  date_to?: string;
  year?: number;
  month?: number;
  search?: string;
  sort_by?: string;
  sort_order?: string;
  per_page?: number;
  page?: number;
}

export interface BlogResponse {
  success: boolean;
  data: Blog[];
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