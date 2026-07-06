"use client";
import Link from "next/link";
import Image from "next/image";
import Slider from "@/components/ui/Slider";
import { useEffect, useState } from "react";
import axiosClient from "@/services/axiosClient";
import { baseUri } from "@/services/constant";
import SlickSlider from "react-slick";
import "slick-carousel/slick/slick.css";
import "slick-carousel/slick/slick-theme.css";

type Solution = {
  id: string;
  status: boolean;
  featured: boolean;
  sort_order: number;
  features: string[];
  benefits: string[];
  technologies: string[];
  title: string;
  slug: string;
  short_description: string;
  description: string;
  image?: string | null;
  category: string | null;
  price_range: string | null;
  delivery_time: string | null;
  meta_description: string | null;
  meta_keywords: string | null;
  meta_title: string | null;
  created_at: string;
  updated_at: string;
};

function getImageSrc(image?: string | null): string | null {
  if (!image) return null;
  return image.startsWith("http") ? image : `${baseUri}${image}`;
}

const SolutionsSection = () => {
  const [solutions, setSolutions] = useState<Solution[]>([]);
  const [loading, setLoading] = useState(true);

  const fetchSolutions = async () => {
    try {
      setLoading(true);
      const response = await axiosClient.get("/content/solutions");
      const data = response.data;
      if (data?.success) {
        setSolutions(data?.data || []);
      }
    } catch (err) {
      console.error("Error fetching solutions", err);
    } finally {
      setLoading(false);
    }
  };

  const handleSolutionClick = (slug: string) => {
    if (typeof window !== "undefined") {
      window.open(`/solutions/${slug}`, "_blank");
    }
  };

  useEffect(() => {
    fetchSolutions();
  }, []);

  const displayedSolutions = solutions.slice(0, 6);

  if (loading) {
    return (
      <div className="flex justify-center py-16">
        <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-orange-500"></div>
      </div>
    );
  }

  return (
    <section className="py-12 bg-white">
      <div className="container mx-auto px-6">
        {/* Header */}
        <div className="mb-10 text-center">
          <h2 className="text-2xl sm:text-3xl section-title-long text-black font-bold mb-2">Solutions We Offer</h2>
          <p className="text-stone-900 font-medium uppercase mb-1 tracking-[1px]">
            Biometrics that fix your real problems
          </p>
          <p className="text-sm mx-auto max-w-2xl text-gray-600">
            Our offerings empower governments and enterprises with scalable technologies for identity management, access control, and digital trust.
          </p>
        </div>

        {/* Mobile & tablet slider */}
        <div className="lg:hidden">
          <Slider
            slidesToShow={1}
            showArrows={false}
            showDots
            autoPlay
            autoPlayInterval={4000}
            dotStyle={{
              size: 6,
              activeSize: 10,
              color: "#D1D5DB",
              activeColor: "#EA5921",
              position: "outside",
              containerClass: "bg-transparent",
            }}
            className="-mx-2"
          >
            {solutions.map((solution, idx) => {
              const src = getImageSrc(solution.image);
              return (
                <div key={solution.id} className="px-2">
                  <div
                    onClick={() => handleSolutionClick(solution.slug)}
                    className="group relative h-72 overflow-hidden rounded-xl border border-white bg-white shadow-lg transition-all duration-500 hover:border-orange-500 hover:shadow-2xl hover:shadow-orange-500/10 cursor-pointer"
                  >
                    {src ? (
                      <Image
                        src={src}
                        alt={solution.title}
                        fill
                        sizes="100vw"
                        className="object-cover object-center transition-transform duration-700 group-hover:scale-105"
                        priority={idx < 3}
                      />
                    ) : (
                      <div className="absolute inset-0 bg-gradient-to-br from-[#3a3a3a] via-[#333333] to-[#1f1f1f]" />
                    )}
                    <div className="absolute inset-0 bg-gradient-to-t from-black/55 via-black/30 to-transparent" />
                    <div className="absolute inset-x-0 bottom-0 p-4">
                      <h3 className="text-white text-base font-semibold mb-1 line-clamp-2">{solution.title}</h3>
                      {solution.short_description && (
                        <p className="text-gray-200 text-xs mb-3 line-clamp-3">{solution.short_description}</p>
                      )}
                      <span className="inline-flex items-center bg-orange-500 text-white text-xs px-3 py-1.5 rounded-md font-medium">
                        View details
                        <svg
                          xmlns="http://www.w3.org/2000/svg"
                          className="h-4 w-4 ml-1"
                          fill="none"
                          viewBox="0 0 24 24"
                          stroke="currentColor"
                        >
                          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                      </span>
                    </div>
                  </div>
                </div>
              );
            })}
          </Slider>
        </div>

        {/* Desktop coverflow slider */}
        <div className="hidden lg:block">
          <div className="solutions-coverflow">
            <SlickSlider
              dots
              infinite
              speed={500}
              autoplay={true}
              autoplaySpeed={3000}
              pauseOnHover={true}
              variableWidth
              centerMode
              centerPadding="0px"
              arrows
              focusOnSelect
              responsive={[
                { breakpoint: 1536, settings: { variableWidth: true, centerPadding: "0px" } },
                { breakpoint: 1280, settings: { variableWidth: true, centerPadding: "0px" } },
                { breakpoint: 1024, settings: { variableWidth: true, centerPadding: "0px" } },
              ]}
            >
              {solutions.map((solution, idx) => {
                const src = getImageSrc(solution.image);
                return (
                  <div key={solution.id} className="px-2">
                    <div
                      onClick={() => handleSolutionClick(solution.slug)}
                      className="cover-card group relative h-[360px] w-auto overflow-hidden rounded-2xl border border-white bg-white shadow-lg transition-all duration-500 cursor-pointer"
                    >
                      {src ? (
                        <img
                          src={src}
                          alt={solution.title}
                          className="h-full w-auto object-cover object-center max-w-[600px]"
                        />
                      ) : (
                        <div className="h-full w-[300px] bg-gradient-to-br from-[#3a3a3a] via-[#333333] to-[#1f1f1f]" />
                      )}
                      <div className="absolute inset-0 bg-gradient-to-t from-black/60 via-black/35 to-transparent pointer-events-none" />
                      <div className="absolute inset-x-0 bottom-0 p-4 pointer-events-none">
                        <h3 className="text-white text-base font-semibold mb-1 line-clamp-2">{solution.title}</h3>
                        {solution.short_description && (
                          <p className="text-gray-200 text-xs mb-3 line-clamp-3">{solution.short_description}</p>
                        )}
                        <span className="inline-flex items-center bg-orange-500 text-white text-xs px-3 py-1.5 rounded-md font-medium">
                          View details
                          <svg
                            xmlns="http://www.w3.org/2000/svg"
                            className="h-4 w-4 ml-1"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                          >
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M14 5l7 7m0 0l-7 7m7-7H3" />
                          </svg>
                        </span>
                      </div>
                    </div>
                  </div>
                );
              })}
            </SlickSlider>
          </div>
        </div>

        {/* CTA */}
        <div className="text-center mt-5">
          <Link
            href="/solutions"
            className="inline-flex items-center text-black px-6 py-3 text-sm hover:text-orange-600 transition-all"
          >
            View All Solutions
            <svg
              xmlns="http://www.w3.org/2000/svg"
              className="h-4 w-4 ml-2"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M14 5l7 7m0 0l-7 7m7-7H3" />
            </svg>
          </Link>
        </div>
      </div>
    </section>
  );
};

export default SolutionsSection;
