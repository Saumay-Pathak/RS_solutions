"use client";

import { queueActivity, recordVisit, updateVisit } from "@/services/analytics";
import { useEffect } from "react";

// Define what `recordVisit` returns
interface VisitResponse {
  success?: boolean;
  message?: string;
  _id?: string;
  visit_id?: string;
  [key: string]: unknown; // allow any extra API fields
}

export default function AnalyticsProvider(): null {
  useEffect(() => {
    if (typeof window === "undefined") return;

    try {
      const nav = navigator as unknown as {
        modelContext?: {
          provideContext?: (ctx: unknown) => unknown;
        };
      };
      const provideContext = nav.modelContext?.provideContext;
      if (typeof provideContext === "function") {
        provideContext({
          tools: [
            {
              name: "open_url",
              description: "Navigate the browser to a URL on this site.",
              inputSchema: {
                type: "object",
                properties: { url: { type: "string" } },
                required: ["url"],
                additionalProperties: false,
              },
              execute: async ({ url }: { url: string }) => {
                window.location.href = url;
                return { ok: true };
              },
            },
            {
              name: "get_api_catalog_url",
              description: "Return the API catalog URL for this site.",
              inputSchema: { type: "object", properties: {}, additionalProperties: false },
              execute: async () => {
                return { url: `${window.location.origin}/.well-known/api-catalog` };
              },
            },
          ],
        });
      }
    } catch {}

    let visitId: string | null = null;
    const startTime = Date.now();

    // ✅ 1. Record a visit when page loads
    recordVisit()
      .then((res: VisitResponse | null) => {
        if (!res) return;
        visitId = res._id ?? res.visit_id ?? null;
      })
      .catch((err: unknown) => {
        console.error("Error recording visit:", err);
      });

    // ✅ 2. Update time spent on page before leaving
    const handleUnload = () => {
      if (!visitId) return;
      const timeOnPage = Date.now() - startTime;
      updateVisit(visitId, timeOnPage).catch((err: unknown) =>
        console.error("Error updating visit:", err)
      );
    };
    window.addEventListener("beforeunload", handleUnload);

    // ✅ 3. Track clicks (batched every 4 seconds)
    const handleClick = (e: MouseEvent) => {
      const el = e.target as HTMLElement | null;
      if (el) queueActivity("click", el);
    };
    window.addEventListener("click", handleClick);

    // ✅ 4. Track scroll activity (batched every 4 seconds)
    const handleScroll = () => {
      queueActivity("scroll", document.body);
    };
    window.addEventListener("scroll", handleScroll);

    // ✅ Cleanup listeners
    return () => {
      window.removeEventListener("beforeunload", handleUnload);
      window.removeEventListener("click", handleClick);
      window.removeEventListener("scroll", handleScroll);
    };
  }, []);

  return null;
}
