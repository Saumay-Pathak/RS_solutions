"use client";

import React, { useEffect, useRef } from "react";
import type { Category } from "./CatalogClient";
import Card from "./Card";
import Link from "next/link";
import { motion, Variants } from "framer-motion";

export default function SectionList({
  categories,
  onActiveChange,
}: {
  categories: Category[];
  activeIndex?: number;
  onActiveChange: (i: number) => void;
}) {
  const containerRef = useRef<HTMLDivElement | null>(null);
  const sectionRefs = useRef<(HTMLElement | null)[]>([]);
  const programmaticScroll = useRef(false);
  const scrollEndTimeout = useRef<number | null>(null);

  /* ----------------------------------------
     Scroll to section (from sidebar / mobile)
  ---------------------------------------- */
  useEffect(() => {
    const handler = (e: Event) => {
      const title = (e as CustomEvent)?.detail?.title;
      if (!title) return;

      const catId = `category-${title.replace(/[^a-zA-Z0-9]/g, "-")}`;
      const el = document.getElementById(catId);
      if (!el) return;

      programmaticScroll.current = true;

      const offsetTop = el.getBoundingClientRect().top + window.scrollY - 90;

      window.scrollTo({ top: offsetTop, behavior: "smooth" });
    };

    window.addEventListener("scroll-to-section", handler as EventListener);
    return () =>
      window.removeEventListener("scroll-to-section", handler as EventListener);
  }, []);

  /* ----------------------------------------
     Detect end of programmatic scroll
  ---------------------------------------- */
  useEffect(() => {
    const handleScroll = () => {
      if (!programmaticScroll.current) return;

      if (scrollEndTimeout.current) {
        clearTimeout(scrollEndTimeout.current);
      }

      scrollEndTimeout.current = window.setTimeout(() => {
        programmaticScroll.current = false;
      }, 250);
    };

    window.addEventListener("scroll", handleScroll);

    return () => {
      window.removeEventListener("scroll", handleScroll);
      if (scrollEndTimeout.current) clearTimeout(scrollEndTimeout.current);
    };
  }, []);

  /* ----------------------------------------
     Intersection Observer for active category
  ---------------------------------------- */
  useEffect(() => {
    const observer = new IntersectionObserver(
      (entries) => {
        if (programmaticScroll.current) return;

        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            const idx = entry.target.getAttribute("data-idx");
            if (idx) onActiveChange(Number(idx));
          }
        });
      },
      {
        root: null,
        rootMargin: "-45% 0px -50% 0px",
        threshold: 0,
      }
    );

    categories.forEach((_, i) => {
      const el = sectionRefs.current[i];
      if (el) observer.observe(el);
    });

    return () => observer.disconnect();
  }, [categories, onActiveChange]);

  /* ----------------------------------------
     Motion variants
  ---------------------------------------- */
  const sectionVariants: Variants = {
    hidden: { opacity: 0, y: 30 },
    visible: {
      opacity: 1,
      y: 0,
      transition: { duration: 0.35, ease: "easeOut" },
    },
  };

  return (
    <div ref={containerRef} className="space-y-14 pr-2">
      {categories.map((cat, i) => {
        const catId = `category-${cat.title.replace(/[^a-zA-Z0-9]/g, "-")}`;
        return (
          <motion.section
            key={cat.title}
            id={catId}
            data-idx={i}
            ref={(el) => {
              sectionRefs.current[i] = el;
            }}
          variants={sectionVariants}
          initial="hidden"
          whileInView="visible"
          viewport={{ once: true, margin: "-100px" }}
          className="
            relative rounded-xl border border-gray-200
            bg-white shadow-sm
          "
        >
          {/* Sticky Category Header */}
          <div className="z-10 bg-white/95 backdrop-blur border-b border-gray-200 px-4 md:px-6 py-3 rounded-t-xl">
            <h3 className="text-lg md:text-2xl font-semibold text-gray-900">
              {cat.title}
            </h3>
          </div>

          {/* Products Grid */}
          <div className="px-4 md:px-6 py-6">
            <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
              {cat.items.map((it, idx) => (
                <motion.div
                  key={it.id}
                  initial={{ opacity: 0, y: 20 }}
                  whileInView={{ opacity: 1, y: 0 }}
                  viewport={{ once: true }}
                  transition={{
                    duration: 0.25,
                    delay: idx * 0.04,
                  }}
                >
                  <Link href={`/products/${it.slug}`}>
                    <Card it={it} />
                  </Link>
                </motion.div>
              ))}
            </div>
          </div>
        </motion.section>
        );
      })}
    </div>
  );
}
