import { NextResponse, type NextRequest } from "next/server";

// Allow-list paths when maintenance mode is ON
const ALLOWED_PATHS = [
  "/sales",
  "/maintenance",
  "/sitemap.xml",
  "/robots.txt",
  "/.well-known",
];

// Asset prefixes to skip
const ASSET_PREFIXES = [
  "/_next",
  "/static",
  "/favicon.ico",
  "/images",
  "/media",
  "/css",
  "/reference",
];

const acceptsMarkdown = (req: NextRequest) => {
  const accept = req.headers.get("accept") || "";
  return accept.toLowerCase().includes("text/markdown");
};

const withAgentDiscoveryHeaders = (res: NextResponse) => {
  const links = [
    '</.well-known/api-catalog>; rel="api-catalog"',
    '</.well-known/openapi.json>; rel="service-desc"',
    '</reference/get_banks.md>; rel="service-doc"; type="text/markdown"',
    '</sitemap.xml>; rel="describedby"; type="application/xml"',
  ];
  res.headers.set("Link", links.join(", "));
  res.headers.set("Vary", "Accept");
  return res;
};

const markdownForPath = (req: NextRequest) => {
  const url = req.nextUrl;
  const path = url.pathname || "/";
  const base =
    process.env.NEXT_PUBLIC_SITE_URL?.replace(/\/+$/, "") ||
    `${url.protocol}//${url.host}`;
  const fullUrl = `${base}${path}`;

  const lines = [
    "# Realtime Biometrics",
    "",
    `URL: ${fullUrl}`,
    "",
    "## Key Links",
    "",
    `- Sitemap: ${base}/sitemap.xml`,
    `- Robots: ${base}/robots.txt`,
    `- API Catalog: ${base}/.well-known/api-catalog`,
    `- OpenAPI: ${base}/.well-known/openapi.json`,
    `- API Docs (Markdown): ${base}/reference/get_banks.md`,
  ];

  if (path !== "/") {
    lines.push("", "## Notes", "", "This is a Markdown representation for agent consumption.");
  }

  const body = `${lines.join("\n")}\n`;
  const tokenCount = body.trim().split(/\s+/).filter(Boolean).length;

  return { body, tokenCount };
};

export async function middleware(req: NextRequest) {
  const { pathname } = req.nextUrl;

  // Skip assets and API routes
  if (
    pathname.startsWith("/api") ||
    ASSET_PREFIXES.some((p) => pathname.startsWith(p))
  ) {
    return NextResponse.next();
  }

  if (req.method === "GET" && acceptsMarkdown(req)) {
    const { body, tokenCount } = markdownForPath(req);
    const res = new NextResponse(body, {
      status: 200,
      headers: {
        "Content-Type": "text/markdown; charset=utf-8",
        "Vary": "Accept",
        "x-markdown-tokens": String(tokenCount),
      },
    });
    if (pathname === "/") return withAgentDiscoveryHeaders(res);
    return res;
  }

  // Allow specific pages during maintenance
  if (ALLOWED_PATHS.some((p) => pathname.startsWith(p))) {
    const res = NextResponse.next();
    if (pathname === "/") return withAgentDiscoveryHeaders(res);
    return res;
  }

  // Check maintenance status from backend
  const base = process.env.NEXT_PUBLIC_API_BASE_URL || "https://app.realtimebiometrics.net/api";
  try {
    const res = await fetch(`${base}/site/header`, { cache: "no-store" });
    const data = await res.json();
    const maintenance = Boolean(data?.data?.status?.maintenance_mode);

    if (maintenance) {
      const url = req.nextUrl.clone();
      url.pathname = "/maintenance";
      return NextResponse.rewrite(url);
    }
  } catch {
    // On failure, do not block navigation
    const res = NextResponse.next();
    if (pathname === "/") return withAgentDiscoveryHeaders(res);
    return res;
  }

  const res = NextResponse.next();
  if (pathname === "/") return withAgentDiscoveryHeaders(res);
  return res;
}

export const config = {
  matcher: "/:path*",
};
