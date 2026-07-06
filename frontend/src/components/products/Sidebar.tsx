"use client";

import React, { useEffect, useRef } from "react";
import type { Category } from "./CatalogClient";
import { motion } from "framer-motion";

interface SidebarProps {
  categories: Category[];
  activeIndex: number;
  onSelect: (i: number) => void;
}

export default function Sidebar({
  categories,
  activeIndex,
  onSelect,
}: SidebarProps) {
  const btnRefs = useRef<(HTMLButtonElement | null)[]>([]);
  const desktopContainerRef = useRef<HTMLDivElement | null>(null);
  const mobileContainerRef = useRef<HTMLDivElement | null>(null);

  /* ----------------------------------------
     Keep active item visible (desktop)
  ---------------------------------------- */
  useEffect(() => {
    const activeBtn = btnRefs.current[activeIndex];
    const container = desktopContainerRef.current;

    if (activeBtn && container) {
      const btnRect = activeBtn.getBoundingClientRect();
      const containerRect = container.getBoundingClientRect();

      if (
        btnRect.top < containerRect.top ||
        btnRect.bottom > containerRect.bottom
      ) {
        activeBtn.scrollIntoView({ block: "center", behavior: "smooth" });
      }
    }
  }, [activeIndex]);

  /* ----------------------------------------
     Handle category click
  ---------------------------------------- */
  const handleSelect = (i: number, title: string) => {
    window.dispatchEvent(
      new CustomEvent("scroll-to-section", { detail: { title } })
    );
    onSelect(i);
  };

  return (
    <>
      {/* ================= Desktop Sidebar ================= */}
      <div className="hidden lg:block sticky top-32">
        <h3 className="mb-4 text-sm font-semibold uppercase tracking-wide text-gray-500">
          Categories
        </h3>

        <div
          ref={desktopContainerRef}
          className="max-h-[calc(100vh-180px)] overflow-y-auto pr-2 space-y-1 no-scrollbar"
        >
          {categories.map((c, i) => {
            const isActive = activeIndex === i;

            return (
              <motion.button
                key={c.title}
                ref={(el) => {
                  btnRefs.current[i] = el;
                }}
                onClick={() => handleSelect(i, c.title)}
                initial={{ opacity: 0, x: -10 }}
                animate={{ opacity: 1, x: 0 }}
                transition={{ duration: 0.2, delay: i * 0.02 }}
                className={`
                  relative w-full text-left rounded-lg px-4 py-3
                  transition-colors duration-200
                  ${
                    isActive
                      ? "bg-orange-100 text-gray-900"
                      : "bg-gray-100 text-gray-700 hover:bg-gray-200"
                  }
                `}
              >
                {/* Active indicator */}
                {isActive && (
                  <motion.span
                    layout
                    transition={{ type: "spring", stiffness: 500, damping: 30 }}
                    className="absolute left-0 h-6 w-1 bg-orange-500 rounded-r top-1/2 -translate-y-1/2"
                  />
                )}

                <span className="relative z-10 text-sm font-medium">
                  {c.title}
                </span>
              </motion.button>
            );
          })}
        </div>
      </div>

      {/* ================= Mobile Horizontal Scroll ================= */}
      <div className="lg:hidden fixed bottom-0 left-0 right-0 z-50 bg-white border-t shadow-inner py-2 px-3">
        <div
          ref={mobileContainerRef}
          className="relative flex gap-2 overflow-x-auto px-3 py-1 no-scrollbar"
        >
          {/* Active indicator */}
          <motion.div
            layout
            transition={{ type: "spring", stiffness: 500, damping: 30 }}
            className="absolute top-0 bottom-0 bg-orange-500 rounded-full"
            style={{
              width: btnRefs.current[activeIndex]?.offsetWidth || 0,
              left: btnRefs.current[activeIndex]?.offsetLeft || 0,
            }}
          />

          {categories.map((c, i) => (
            <button
              key={c.title}
              ref={(el) => {
                btnRefs.current[i] = el;
              }}
              onClick={() => handleSelect(i, c.title)}
              className="relative whitespace-nowrap rounded-full px-4 py-1 text-sm font-medium text-gray-700 z-10"
            >
              {c.title}
            </button>
          ))}
        </div>
      </div>
    </>
  );
}
