"use client";
import { useEffect, useState } from "react";
import { getSolutionBySlug } from "@/services/solutionServices";
import SolutionDetails from "@/components/sections/SolutionDetails";
import Layout from "@/components/layout/Layout";
import { useParams } from "next/navigation";

export interface Solution {
  id: string;
  title: string;
  slug: string;
  short_description: string;
  description: string;
  features: string[];
  benefits: string[];
  technologies: string[];
  status: boolean;
  featured: boolean;
  sort_order: number;
  category: string | null;
  price_range: string | null;
  delivery_time: string | null;
  meta_description: string | null;
  meta_keywords: string | null;
  meta_title: string | null;
  created_at: string;
  updated_at: string;
}

const SolutionDetailPage = () => {
  const [solution, setSolution] = useState<Solution | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const params = useParams();
  const slug = params?.title as string;

  useEffect(() => {
    const fetchSolution = async () => {
      if (!slug) return;

      setLoading(true);
      setError(null);

      try {
        const response = await getSolutionBySlug(slug);
        if (response.data) {
          setSolution(response.data);
        } else {
          setError("Solution not found.");
        }
      } catch (err) {
        console.error("Failed to fetch solution:", err);
        setError("Failed to load solution. Please try again later.");
      } finally {
        setLoading(false);
      }
    };

    fetchSolution();
  }, [slug]);

  return (
    <Layout>
      {loading && (
        <div className="min-h-screen flex items-center justify-center">
          <div className="text-center">
            <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-gray-900 mx-auto"></div>
            <p className="mt-4 text-gray-600">Loading solution details...</p>
          </div>
        </div>
      )}

      {error && (
        <div className="min-h-screen flex items-center justify-center">
          <p className="text-red-500">{error}</p>
        </div>
      )}

      {solution && <SolutionDetails solution={solution} />}
    </Layout>
  );
};

export default SolutionDetailPage;
