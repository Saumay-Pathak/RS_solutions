"use client";

import React, { useEffect, useMemo, useState } from "react";
import { Star } from "lucide-react";
import Image from "next/image";
import Slider from "@/components/ui/Slider";
import { getTestimonials } from "@/services/testimonialService";
import { baseUri } from "@/services/constant";

type Testimonial = {
  id: string;
  name: string;
  position: string;
  company?: string;
  content: string;
  rating?: number;
  featured?: boolean | number;
  status?: boolean | number | string;
  sort_order?: number;
  image?: string;
  created_at?: string;
};

export default function TestimonialCarousel() {
  const [testimonials, setTestimonials] = useState<Testimonial[]>([]);
  const [loading, setLoading] = useState(true);
  useEffect(() => {
    let mounted = true;
    async function fetchTestimonials() {
      try {
        const data = await getTestimonials();
        if (mounted) {
          setTestimonials(Array.isArray(data) ? data : []);
        }
      } catch (err) {
        console.error("Failed to load testimonials", err);
      } finally {
        if (mounted) setLoading(false);
      }
    }

    fetchTestimonials();
    return () => {
      mounted = false;
    };
  }, []);

  const isActive = (s?: boolean | number | string) => {
    if (typeof s === "boolean") return s;
    if (typeof s === "number") return s === 1;
    if (typeof s === "string") {
      return ["1", "true", "active", "enabled"].includes(s.toLowerCase());
    }
    return false;
  };

  const ordered = useMemo(() => {
    return testimonials
      .filter((t) => isActive(t.status))
      .sort((a, b) => {
        const featuredDiff = Number(b.featured) - Number(a.featured);
        if (featuredDiff !== 0) return featuredDiff;

        const orderDiff =
          Number(a.sort_order ?? 0) - Number(b.sort_order ?? 0);
        if (orderDiff !== 0) return orderDiff;

        return (
          new Date(b.created_at ?? 0).getTime() -
          new Date(a.created_at ?? 0).getTime()
        );
      });
  }, [testimonials]);

  const getInitials = (name?: string) =>
    name
      ?.split(" ")
      .map((n) => n[0])
      .slice(0, 2)
      .join("")
      .toUpperCase() ?? "";

  if (!loading && ordered.length === 0) return null;

  return (
    <section className="bg-gray-50">
      <div className="max-w-7xl mx-auto px-4 py-15">
        {/* Header */}
        <div className="text-center mb-10">
          <h2 className="section-title text-2xl sm:text-3xl font-bold">Trusted by Industry Leaders</h2>
          <p className="mt-3 text-sm text-slate-600">
            Real feedback from organizations using Realtime Biometrics
          </p>
        </div>

        {/* Loading */}
        {loading ? (
          <div className="flex justify-center items-center h-40 text-gray-400">
            Loading testimonials…
          </div>
        ) : (
          <Slider
            autoPlay
            autoPlayInterval={5000}
            showArrows={false}
            showDots
            slidesToShow={4}
            responsive={[
              {
                breakpoint: 1200, slidesToShow: 3,
                showDots: false
              },
              {
                breakpoint: 768, slidesToShow: 2,
                showDots: false
              },
              {
                breakpoint: 480, slidesToShow: 1,
                showDots: true
              },
            ]}
            className="pb-4"
            dotStyle={{
              position: "outside",
              size: 7,
              activeSize: 10,
              color: "#E5E7EB",
              activeColor: "#FF6000",
            }}
          >
            {ordered.map((t) => (
              <div key={t.id} className="px-2 h-full">
                <article
                  className="
                    h-full rounded-2xl border border-gray-200 bg-white
                    p-6 md:p-8
                    transition-all duration-300 ease-out
                    hover:shadow-md
                    will-change-transform
                  "
                >
                  {/* Rating */}
                  <div className="flex items-center gap-1 mb-4">
                    {[...Array(5)].map((_, i) => (
                      <Star
                        key={i}
                        className={`w-5 h-5 transition-transform duration-200 ${
                          i < Number(t.rating ?? 0)
                            ? "text-yellow-500"
                            : "text-gray-300"
                        }`}
                        fill="currentColor"
                      />
                    ))}
                  </div>

                  {/* Content */}
                  <p className="text-slate-700 text-sm md:text-base leading-relaxed line-clamp-5">
                    “{t.content}”
                  </p>

                  {/* Footer */}
                  <div className="flex items-center gap-3 mt-6">
                    <div className="w-11 h-11 rounded-full overflow-hidden bg-orange-100 flex items-center justify-center">
                      {t.image ? (
                        <Image
                          src={`${baseUri}${t.image}`}
                          alt={t.name}
                          width={44}
                          height={44}
                          className="object-cover"
                        />
                      ) : (
                        <span className="bg-orange-600 text-white w-full h-full flex items-center justify-center font-semibold">
                          {getInitials(t.name)}
                        </span>
                      )}
                    </div>

                    <div>
                      <p className="font-medium text-slate-900 text-sm md:text-base">
                        {t.name}
                      </p>
                      <p className="text-xs md:text-sm text-slate-500">
                        {t.position}
                      </p>
                    </div>
                  </div>
                </article>
              </div>
            ))}
          </Slider>
        )}

        {/* SEO Review Schema */}
        {!loading && (
          <script
            type="application/ld+json"
            dangerouslySetInnerHTML={{
              __html: JSON.stringify({
                "@context": "https://schema.org",
                "@type": "Product",
                name: "Realtime Biometrics",
                review: ordered.slice(0, 5).map((t) => ({
                  "@type": "Review",
                  reviewBody: t.content,
                  author: {
                    "@type": "Person",
                    name: t.name,
                  },
                  reviewRating: {
                    "@type": "Rating",
                    ratingValue: t.rating ?? 5,
                    bestRating: 5,
                  },
                })),
              }),
            }}
          />
        )}
      </div>
    </section>
  );
}
