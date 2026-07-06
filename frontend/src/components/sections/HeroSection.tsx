"use client";

import Slider from "../ui/Slider";
import { useEffect, useState, useMemo, useRef, useCallback } from "react";
import { getSliderData, imageLink } from "@/services/heroServices";
import DOMPurify from "dompurify";

// ✅ Types
export interface ApiResponse {
  success: boolean;
  data: SliderData[];
  count: number;
}

export interface SliderData {
  id: string;
  title: string;
  subtitle: string;
  content: string; // contains HTML string (escaped)
  content_file?: string; // path to HTML file relative to storage base
  image_alt: string | null;
  button_text: string | null;
  button_link: string | null;
  button_style: string;
  secondary_button_text: string | null;
  secondary_button_link: string | null;
  secondary_button_style: string;
  order: number;
  is_active: boolean;
  background_color: string;
  text_color: string;
  overlay_opacity: number;
  content_position: "left" | "center" | "right";
  animation_type: "fade" | "slide" | string;
  auto_play_delay: number;
  display_from: string | null;
  display_to: string | null;
  updated_at: string;
  created_at: string;
  image?: string;
}

// Message type from iframe for auto-resizing hero content
type HeroContentHeightMessage = {
  type: "hero-content-height";
  file?: string;
  height?: number;
};

// Type guard to safely narrow unknown postMessage payloads
const isHeroContentHeightMessage = (
  data: unknown
): data is HeroContentHeightMessage => {
  if (typeof data !== "object" || data === null) return false;
  const record = data as Record<string, unknown>;
  return (
    record.type === "hero-content-height" &&
    (record.height === undefined || typeof record.height === "number") &&
    (record.file === undefined || typeof record.file === "string")
  );
};

// ✅ Helper to decode escaped HTML (\u003Cdiv... → <div...)
const decodeHTML = (html: string) => {
  const txt = document.createElement("textarea");
  txt.innerHTML = html;
  return txt.value;
};

// ✅ Extract only <body> inner HTML if a full document is provided
const extractBodyContent = (html: string) => {
  const bodyMatch = html.match(/<body[^>]*>([\s\S]*?)<\/body>/i);
  if (bodyMatch && bodyMatch[1]) {
    return bodyMatch[1];
  }
  // Remove potential <html> and <head> wrappers to avoid invalid nesting
  const cleaned = html
    .replace(/<\/?html[^>]*>/gi, "")
    .replace(/<head[^>]*>[\s\S]*?<\/head>/gi, "");
  return cleaned;
};

// We intentionally avoid extra UI and only render slide content.

const HeroSection = () => {
  const [heroSlides, setHeroSlides] = useState<SliderData[]>([]);
  const [iframeHeights, setIframeHeights] = useState<Record<string, number>>({});
  const iframeRefs = useRef<Record<string, HTMLIFrameElement | null>>({});
  const [viewportWidth, setViewportWidth] = useState<number>(typeof window !== "undefined" ? window.innerWidth : 1024);
  const [viewportHeight, setViewportHeight] = useState<number>(typeof window !== "undefined" ? window.innerHeight : 768);
  const defaultIframeHeight = useMemo(() => {
    const ratio = viewportWidth < 768 ? 0.4 : 0.55;
    const minH = Math.max(220, Math.round(viewportHeight * ratio));
    const buffer = viewportWidth < 768 ? 2 : 6;
    return minH + buffer;
  }, [viewportHeight, viewportWidth]);

  useEffect(() => {
    const fetchHeroSlides = async () => {
      try {
        const data: ApiResponse = await getSliderData();
        if (data.success) {
          console.log("[HeroSection] Fetched hero slides:", data.data);
          setHeroSlides(data.data);
        } else {
          console.error("Failed to load hero slides");
        }
      } catch (error) {
        console.error("Error fetching hero slides:", error);
      }
    };

    fetchHeroSlides();
  }, []);

  useEffect(() => {
    const onResize = () => { setViewportWidth(window.innerWidth); setViewportHeight(window.innerHeight); };
    if (typeof window !== "undefined") {
      window.addEventListener("resize", onResize);
      onResize();
    }
    return () => {
      if (typeof window !== "undefined") {
        window.removeEventListener("resize", onResize);
      }
    };
  }, []);

  // Listen for height messages from iframe content to auto-resize
  useEffect(() => {
    const onMessage = (e: MessageEvent) => {
      const sameOrigin = typeof window !== "undefined" && e.origin === window.location.origin;
      if (!sameOrigin) return;
      const data = e.data;
      if (isHeroContentHeightMessage(data)) {
        const key = String(data.file || "");
        const height = Number(data.height || 0);
        if (key && height > 0) {
          setIframeHeights((prev) => (prev[key] !== height ? { ...prev, [key]: height } : prev));
        }
      }
    };
    window.addEventListener("message", onMessage);
    return () => window.removeEventListener("message", onMessage);
  }, []);

  const measureIframe = useCallback((key: string) => {
    try {
      const el = iframeRefs.current[key];
      if (!el) return;
      const doc = el.contentDocument || el.contentWindow?.document;
      if (!doc) return;
      const body = doc.body as HTMLElement;
      const html = doc.documentElement as HTMLElement;
      const h = Math.max(
        Number(body?.scrollHeight || 0),
        Number(html?.scrollHeight || 0),
        Number(body?.offsetHeight || 0)
      );
      const ratio = viewportWidth < 768 ? 0.4 : 0.55;
      const minH = Math.max(220, Math.round(viewportHeight * ratio));
      const measuredBuffer = viewportWidth < 768 ? 0 : 4;
      const finalH = Math.max(minH, Math.ceil(h)) + measuredBuffer;
      setIframeHeights((prev) => (prev[key] === finalH ? prev : { ...prev, [key]: finalH }));
    } catch {
      
    }
  }, [viewportHeight, viewportWidth]);

  const scheduleMeasure = useCallback((key: string) => {
    measureIframe(key);
    setTimeout(() => measureIframe(key), 250);
    setTimeout(() => measureIframe(key), 1000);
    setTimeout(() => measureIframe(key), 2000);
  }, [measureIframe]);

  const autoPlayInterval = useMemo(() => {
    return heroSlides[0]?.auto_play_delay || 5000;
  }, [heroSlides]);

  // Avoid re-measuring on viewport changes to prevent growth loop
  useEffect(() => {
    Object.keys(iframeRefs.current).forEach((k) => scheduleMeasure(k));
    // Run once on mount
  }, [scheduleMeasure]);

  return (
    <section className="pt-0 mb-5 md:mb-15">
      <Slider
        autoPlay={true}
        autoPlayInterval={autoPlayInterval}
        showArrows={true}
        showDots={true}
        // className="h-[80vh]"
        dotStyle={{
          size: 6,
          activeSize: 10,
          color: "#D1D5DB",
          activeColor: "#EA5921",
          position: "outside",
          containerClass: "mt-0 mb-0"
        }}
        slidesToShow={1}
        responsive={[{ breakpoint: 768, slidesToShow: 1, showDots: true }]}
      >
        {heroSlides.map((slide) => (
          <div key={slide.id} className="overflow-visible">
            {slide.content_file ? (
              <iframe
                src={getContentSrc(slide.content_file)}
                ref={(el) => {
                  const key = getContentKey(slide.content_file);
                  iframeRefs.current[key] = el;
                }}
                onLoad={() => scheduleMeasure(getContentKey(slide.content_file))}
                style={{
                  width: "100%",
                  height: `${iframeHeights[getContentKey(slide.content_file)] ?? defaultIframeHeight}px`,
                  border: "none",
                  display: "block",
                }}
                title={`hero-slide-${slide.id}`}
                scrolling={/^https?:\/\//i.test(String(getContentSrc(slide.content_file))) ? "auto" : "no"}
              />
            ) : (
              slide.content && (
                <div className=""
                  dangerouslySetInnerHTML={{
                    __html: DOMPurify.sanitize(
                      extractBodyContent(decodeHTML(slide.content))
                    ),
                  }}
                />
              )
            )}
          </div>
        ))}
      </Slider>
    </section>
  );
};

// Normalize content_file to relative path expected by /api/hero-content
function normalizeContentFile(raw: string | undefined): string {
  const s = String(raw || "").trim().replace(/^`|`$/g, "").replace(/^['"]|['"]$/g, "");
  const match = s.match(/hero-slides\/html\/[A-Za-z0-9_.\-]+\.html$/);
  if (match) return match[0];
  return s; // fall back; API route will further validate/trim
}

function getContentSrc(raw: string | undefined): string {
  const s = String(raw || "");
  if (/^https?:\/\//i.test(s)) {
    const trimmed = s.replace(/`/g, "").trim();
    // If the URL points to our storage domain, proxy through same-origin for auto-resize injection
    if (trimmed.startsWith(imageLink)) {
      const rel = trimmed.substring(imageLink.length);
      return `/api/hero-content?file=${encodeURIComponent(normalizeContentFile(rel))}`;
    }
    return trimmed; // external URL - keep direct
  }
  return `/api/hero-content?file=${encodeURIComponent(normalizeContentFile(s))}`; // relative path
}

function getContentKey(raw: string | undefined): string {
  const s = String(raw || "");
  return /^https?:\/\//i.test(s) ? s : normalizeContentFile(s);
}

export default HeroSection;
