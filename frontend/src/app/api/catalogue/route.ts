import { NextRequest } from "next/server";
import { baseUri } from "@/services/constant";
import { checkRateLimit, rateLimitHeaders } from "@/lib/rateLimit";

// Force Node.js runtime to ensure Buffer is available
export const runtime = "nodejs";

export async function GET(req: NextRequest) {
  try {
    const rl = checkRateLimit(req, { limit: 240, windowMs: 60_000, scope: "catalogue" });
    if (!rl.allowed) {
      return new Response(JSON.stringify({ error: "Too many requests" }), {
        status: 429,
        headers: { "Content-Type": "application/json", ...rateLimitHeaders(240, rl.remaining, rl.reset) },
      });
    }
    const { searchParams } = new URL(req.url);
    const doc = searchParams.get("doc");
    const title = searchParams.get("title") || "catalogue";

    if (!doc) {
      return new Response(JSON.stringify({ error: "Missing 'doc' query parameter" }), {
        status: 400,
        headers: { "Content-Type": "application/json" },
      });
    }

    // Sanitize and normalize path
    const safeDoc = doc
      .replace(/^https?:\/\//, "")
      .replace(/^\/+/, "")
      .replace(/\s+/g, "");

    if (safeDoc.includes("..")) {
      return new Response(JSON.stringify({ error: "Invalid document path" }), {
        status: 400,
        headers: { "Content-Type": "application/json" },
      });
    }

    const pdfUrl = `${baseUri.replace(/\/+$/, "")}/${safeDoc}`;

    const res = await fetch(pdfUrl, { cache: "force-cache" });
    if (!res.ok) {
      return new Response(JSON.stringify({ error: "Catalogue not found" }), {
        status: res.status === 404 ? 404 : 502,
        headers: { "Content-Type": "application/json" },
      });
    }

    const arrayBuffer = await res.arrayBuffer();
    const contentType = res.headers.get("content-type") || "application/pdf";
    const filename = safeDoc.split("/").pop() || `${title.replace(/\s+/g, "_")}_catalogue.pdf`;

    return new Response(arrayBuffer, {
      status: 200,
      headers: {
        "Content-Type": contentType,
        "Content-Disposition": `inline; filename="${filename}"`,
        "Cache-Control": "public, max-age=600, stale-while-revalidate=120",
        ...rateLimitHeaders(240, rl.remaining, rl.reset),
      },
    });
  } catch (err) {
    console.error("Catalogue proxy error:", err);
    return new Response(JSON.stringify({ error: "Internal server error" }), {
      status: 500,
      headers: { "Content-Type": "application/json" },
    });
  }
}
