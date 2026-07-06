import { NextRequest, NextResponse } from "next/server";
import { getServiceBySlug } from "@/services/serviceService";
import { checkRateLimit, rateLimitHeaders } from "@/lib/rateLimit";

export async function GET(
  req: NextRequest,
  context: { params: Promise<{ slug?: string }> }
) {
  const rl = checkRateLimit(req, { limit: 120, windowMs: 60_000, scope: "services" });
  if (!rl.allowed) {
    return NextResponse.json(
      { success: false, error: "Too many requests" },
      { status: 429, headers: rateLimitHeaders(120, rl.remaining, rl.reset) }
    );
  }
  const { slug } = await context.params;

  if (!slug || slug === "undefined") {
    return NextResponse.json(
      { success: false, error: "Missing or invalid slug" },
      { status: 400 }
    );
  }

  const { service, error } = await getServiceBySlug(slug);

  if (!service) {
    return NextResponse.json(
      { success: false, error: error || "Service not found" },
      { status: 404 }
    );
  }

  return NextResponse.json(
    { success: true, data: service },
    {
      status: 200,
      headers: {
        "Cache-Control": "public, max-age=60",
        ...rateLimitHeaders(120, rl.remaining, rl.reset),
      },
    }
  );
}
