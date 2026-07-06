"use client";

import React, { useEffect, useRef, useState } from "react";
import { gsap } from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";
import { ScrollToPlugin } from "gsap/ScrollToPlugin";
import { getSolutions } from "@/services/solutionServices";
import Image from "next/image";
import { baseUri } from "@/services/constant";
import Link from "next/link";

if (typeof window !== "undefined") {
  gsap.registerPlugin(ScrollTrigger, ScrollToPlugin);
}

type Solution = {
  title: string;
  description?: string;
  // add other fields returned by getSolutions() as needed
  image?: string | null;
  featured_image?: string | null;
  slug?: string;
};

export default function RealtimeScrollCards() {
  const containerRef = useRef<HTMLDivElement>(null);
  const headerRef = useRef<HTMLDivElement>(null);
  const cardRefs = useRef<HTMLDivElement[]>([]);
  const [activeCard, setActiveCard] = useState<number>(0);
  const [solutions, setSolutions] = useState<Solution[]>([]);

  // ✅ Fetch API Data
  useEffect(() => {
    async function fetchData() {
      try {
        const response = await getSolutions();
        setSolutions(response.data || []);
      } catch (error) {
        console.error("Error fetching solutions:", error);
      }
    }
    fetchData();
  }, []);

  // ✅ Scroll Animation
  useEffect(() => {
    if (!containerRef.current || solutions.length === 0) return;

    const ctx = gsap.context(() => {
      cardRefs.current.forEach((card, i) => {
        if (!card) return;

        if (i === 0) {
          gsap.set(card, { position: "sticky", top: "40vh", zIndex: 1 });
        }

        ScrollTrigger.create({
          trigger: card,
          start: "top center", // Changed from "top bottom" to "top center"
          end: "top 40%",      // Changed from "top center" to "top 20%"
          scrub: true,
          onEnter: () => {
            setActiveCard(i);
            // Center the corresponding tab in the sticky header on scroll
            const header = headerRef.current;
            const tabs = header?.querySelectorAll<HTMLButtonElement>("button");
            const activeTab = tabs?.[i];
            if (activeTab) {
              activeTab.scrollIntoView({ behavior: "smooth", inline: "center", block: "nearest" });
            }
            // Apply blur effect to lower cards only when card reaches the top center
            cardRefs.current.forEach((c, index) => {
              gsap.set(c, { zIndex: index <= i ? index + 1 : index });
              
              // Apply blur to cards below the current one (previous cards)
              if (index < i) {
                gsap.to(c, { 
                  filter: "blur(2px)", 
                  opacity: 0.7, 
                  duration: 0.5,
                  ease: "power2.inOut"
                });
              } else {
                gsap.to(c, { 
                  filter: "blur(0px)", 
                  opacity: 1, 
                  duration: 0.5,
                  ease: "power2.inOut"
                });
              }
            });
            
            // Ensure the current card has proper z-index
            gsap.set(card, { zIndex: i + 5 });
            gsap.to(card, { opacity: 1, scale: 1, duration: 0.3 });
          },
          onLeave: () => {
            gsap.set(card, {
              position: "sticky",
              top: `40vh`,
              zIndex: i + 1,
            });
          },
          onLeaveBack: () => {
            gsap.set(card, { position: "relative", zIndex: 1, top: 0 });
            
            // Reset blur for ALL cards when scrolling back up
            cardRefs.current.forEach((c) => {
              gsap.to(c, { 
                filter: "blur(0px)", 
                opacity: 1, 
                duration: 0.5,
                ease: "power2.inOut"
              });
            });
          },
        });
      });
    }, containerRef);

    return () => ctx.revert();
  }, [solutions]);

  // ✅ Smooth Scroll + Focus
  const handleScrollTo = (index: number) => {
    const targetCard = cardRefs.current[index];
    if (!targetCard) return;

    setActiveCard(index);

    // Reset blur for ALL cards when clicking on any button
    cardRefs.current.forEach((c) => {
      gsap.to(c, { 
        filter: "blur(0px)", 
        opacity: 1, 
        duration: 0.5,
        ease: "power2.inOut"
      });
    });

    cardRefs.current.forEach((c, i) => {
      gsap.set(c, { zIndex: i === index ? 30 : i + 1 });
    });

    gsap.to(window, {
      duration: 0.8,
      scrollTo: { y: targetCard, offsetY: 270 },
      ease: "power2.inOut",
      onComplete: () => { gsap.set(targetCard, { zIndex: 30 }); },
    });

    gsap.fromTo(
      targetCard,
      { opacity: 0.8 },
      { opacity: 1, duration: 0.5, ease: "back.out(1.7)" }
    );
  };

  return (
    <section ref={containerRef} className="bg-white py-10 lg:py-0 relative isolate w-full">
      {/* ✅ Sticky Header (Dynamic Buttons) */}
      <div
        ref={headerRef}
        className="sticky w-full max-w-7xl top-42 lg:top-38 bg-white sm:rounded-full border-black/50 sm:border-1 py-2 px-2 md:px-4 flex flex-nowrap items-center gap-2 mx-auto mb-6 overflow-x-auto overscroll-x-contain scroll-smooth snap-x snap-mandatory no-scrollbar z-[60]"
      >
        {solutions.map((card, index) => (
          <button
            key={index}
            onClick={() => handleScrollTo(index)}
            className={`px-4 lg:px-5 py-2 lg:py-3 w-full rounded-full text-[13px]  md:text-[18px] text-nowrap font-thin transition-all snap-start ${activeCard === index
                ? "bg-[#EFAF00] text-black"
                : "bg-[#F9F9F9] text-black"
              }`}
          >
            {card.title}
          </button>
        ))}
      </div>

      {/* ✅ Dynamic Cards Section */}
      <div
        className="relative w-full max-w-7xl mx-auto px-4 z-10"
        style={{ minHeight: `${solutions.length * 50}vh` }}
        // Responsive minHeight using CSS media query in inline style
      >
        {solutions.map((card, i) => (
          <div
            key={i}
            ref={(el) => {
              if (el) cardRefs.current[i] = el;
            }}
            className={`relative ${i > 0 ? "mt-[50vh] md:mt-[100vh]" : ""}`}
            data-index={i}
          >
            <div className="bg-white rounded-xl shadow-sm border border-[#D9D9D9] p-6 md:p-8 max-w-6xl mx-auto min-h-[32rem] md:min-h-[40rem]">
              {/* Top row: Number + Title */}
              <div className="flex items-center mb-3">
                <span className="text-orange-500 font-semibold text-3xl mr-3 bg-gray-100 rounded-md w-15 h-15 flex items-center justify-center">
                  {i + 1}
                </span>
                <h2 className="section-title section-title--left">
                  {card.title}
                </h2>
              </div>

              {/* Image (if available) */}
              {(() => {
                const src = card.image || card.featured_image
                  ? `${baseUri}${card.image || card.featured_image}`
                  : null;
                return src ? (
                  <div className="mt-2 mb-4 relative w-full h-[clamp(220px,30vw,380px)] md:h-[clamp(320px,24vw,520px)] rounded-lg overflow-hidden bg-gray-50">
                    <Image
                      src={src}
                      alt={card.title}
                      fill
                      unoptimized
                      className="object-cover object-center"
                    />
                  </div>
                ) : null;
              })()}

              {/* Description */}
              <p className="text-gray-700 text-sm lg:text-lg mt-2 leading-relaxed">
                {card.description}
              </p>

              {/* CTA to detailed page */}
              {card.slug && (
                <div className="mt-4">
                  <Link
                    href={`/solutions/${card.slug}`}
                    className="inline-flex items-center gap-2 text-orange-600 hover:text-orange-500 font-medium"
                  >
                    View details
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      viewBox="0 0 24 24"
                      fill="none"
                      stroke="currentColor"
                      strokeWidth="2"
                      className="w-4 h-4"
                    >
                      <path strokeLinecap="round" strokeLinejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                  </Link>
                </div>
              )}
            </div>
          </div>

        ))}
      </div>
    </section>
  );
}
