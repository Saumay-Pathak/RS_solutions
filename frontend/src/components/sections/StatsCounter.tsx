"use client";
import { useEffect, useRef, useState } from "react";

type StatItem = {
  label: string;
  value: number;
  suffix?: string;
};

type CounterApiItem = {
  label?: string;
  key?: string;
  value?: string | number;
};

const defaultStats: StatItem[] = [
  { label: "Current Clients", value: 180, suffix: "+" },
  { label: "Years Of Experience", value: 10, suffix: "+" },
  { label: "Awards Winning", value: 35, suffix: "+" },
  { label: "Our Solutions", value: 10, suffix: "+" },
];

export default function StatsCounter({
  stats = defaultStats,
  durationMs = 3000,
  title = "RealTime by the Numbers",
  subtitle = "Our impact at a glance",
}: {
  stats?: StatItem[];
  durationMs?: number;
  title?: string;
  subtitle?: string;
}) {
  const containerRef = useRef<HTMLDivElement | null>(null);
  const [visible, setVisible] = useState(false);
  const [dataStats, setDataStats] = useState<StatItem[]>(stats);
  const [displayValues, setDisplayValues] = useState<number[]>(stats.map(() => 0));
  const [animated, setAnimated] = useState(false);

  useEffect(() => {
    if (!containerRef.current) return;
    const io = new IntersectionObserver(
      (entries) => {
        const entry = entries[0];
        if (entry.isIntersecting) {
          setVisible(true);
        }
      },
      { threshold: 0.3 }
    );
    io.observe(containerRef.current);
    return () => io.disconnect();
  }, []);

  // Fetch counters dynamically from API
  useEffect(() => {
    let cancelled = false;
    const load = async () => {
      try {
        const res = await fetch(`${process.env.NEXT_PUBLIC_API_BASE_URL}/site/counters`);
        const json = await res.json();
        const list = Array.isArray(json?.data) ? json.data : [];
        const mapped: StatItem[] = list.map((item: CounterApiItem) => {
          const raw = String(item?.value ?? "");
          const numMatch = raw.match(/\d+/);
          const num = numMatch ? parseInt(numMatch[0], 10) : 0;
          const suffix = raw.replace(/\d+/g, "").trim();
          return {
            label: String(item?.label ?? item?.key ?? ""),
            value: isNaN(num) ? 0 : num,
            suffix,
          };
        });
        if (!cancelled && mapped.length > 0) {
          setDataStats(mapped);
          setDisplayValues(mapped.map(() => 0));
          setAnimated(false); // allow animation with new data
        }
      } catch (e) {
        console.error("Failed to load counters:", e);
      }
    };
    load();
    return () => {
      cancelled = true;
    };
  }, []);

  useEffect(() => {
    if (!visible || animated) return;
    setAnimated(true);

    const startTime = performance.now();
    const targets = dataStats.map((s) => s.value);

    // Scale duration per counter so large numbers animate with smaller visual jumps
    const baseDuration = Math.max(1200, durationMs); // ensure minimum duration
    const durations = dataStats.map((s) => {
      const v = Math.max(0, s.value);
      // Scale based on order of magnitude (log), capped for sanity
      const factor = Math.min(3, 1 + Math.log10(v + 1) * 0.7);
      return baseDuration * factor;
    });

    // Stagger starts for nicer rhythm
    const offsets = dataStats.map((_, idx) => idx * 150);

    const prefersReduced = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
    if (prefersReduced) {
      setDisplayValues(targets);
      return;
    }

    const step = (now: number) => {
      let allDone = true;
      const next = targets.map((target, i) => {
        const elapsed = now - startTime - offsets[i];
        if (elapsed <= 0) {
          allDone = false;
          return 0;
        }
        const progress = Math.min(1, elapsed / durations[i]);
        if (progress < 1) allDone = false;
        // easeOutCubic for smooth finish
        const eased = 1 - Math.pow(1 - progress, 3);
        return Math.round(target * eased);
      });

      setDisplayValues(next);
      if (!allDone) requestAnimationFrame(step);
    };

    requestAnimationFrame(step);
  }, [visible, animated, dataStats, durationMs]);

  return (
    <section className="bg-white pt-4 pb-6">
      <div ref={containerRef} className="w-[85%] mx-auto px-6 md:px-8">
        {/* Section heading */}
        <div className="text-center mb-3 md:mb-4">
          <h2 className="section-title">{title}</h2>
          {subtitle && <p className="section-subtitle mt-2">{subtitle}</p>}
        </div>

        <div className="grid grid-cols-2 md:grid-cols-4 gap-6 md:gap-8">
          {dataStats.map((stat, idx) => {
            const value = displayValues[idx] ?? stat.value;
            return (
              <div key={stat.label} className="flex flex-col items-center text-center">
                <div className="text-4xl md:text-5xl font-semibold text-orange-500 tracking-wider">
                  [{value}
                  {stat.suffix || ""}]
                </div>
                <div className="mt-2 text-gray-900 font-semibold">{stat.label}</div>
              </div>
            );
          })}
        </div>
      </div>
    </section>
  );
}
