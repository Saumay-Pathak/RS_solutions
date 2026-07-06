"use client";

import React, { useEffect, useRef, useState, useCallback } from "react";
import { usePathname, useSearchParams } from "next/navigation";

type Props = {
  duration?: number;
  delay?: number;
};

export default function GlobalPreloader({
  duration = 700,
  delay = 120,
}: Props) {
  const pathname = usePathname();
  const searchParams = useSearchParams();
  const searchString = searchParams?.toString();

  const [visible, setVisible] = useState(false);

  const showTimer = useRef<ReturnType<typeof setTimeout> | null>(null);
  const hideTimer = useRef<ReturnType<typeof setTimeout> | null>(null);

  /**
   * Triggers the global preloader with optional custom duration
   */
  const trigger = useCallback(
    (customDuration?: number) => {
      if (showTimer.current) clearTimeout(showTimer.current);
      if (hideTimer.current) clearTimeout(hideTimer.current);

      // Delay prevents flicker on instant navigations
      showTimer.current = setTimeout(() => {
        setVisible(true);

        hideTimer.current = setTimeout(
          () => setVisible(false),
          customDuration ?? duration
        );
      }, delay);
    },
    [duration, delay]
  );

  // Show on initial page load
  useEffect(() => {
    trigger(800);
  }, [trigger]);

  // Show on route or query changes
  useEffect(() => {
    trigger();
  }, [pathname, searchString, trigger]);

  // Cleanup timers on unmount
  useEffect(() => {
    return () => {
      if (showTimer.current) clearTimeout(showTimer.current);
      if (hideTimer.current) clearTimeout(hideTimer.current);
    };
  }, []);

  return (
    <div
      className={`
        fixed inset-0 z-[9999] flex items-center justify-center
        bg-white/80 backdrop-blur-sm
        transition-opacity duration-300
        ${
          visible
            ? "opacity-100 pointer-events-auto"
            : "opacity-0 pointer-events-none"
        }
      `}
      role="status"
      aria-live="polite"
      aria-busy={visible}
      aria-label="Loading page"
    >
      {/* Spinner */}
      <div className="w-16 h-16 rounded-full border-4 border-orange-500 border-t-transparent animate-spin" />
    </div>
  );
}
