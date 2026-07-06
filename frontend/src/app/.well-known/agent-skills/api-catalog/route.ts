const siteUrl =
  process.env.NEXT_PUBLIC_SITE_URL?.replace(/\/+$/, "") ||
  "https://realtimebiometrics.com";

export async function GET() {
  const body = [
    "# API Catalog",
    "",
    "This site publishes an API catalog at `/.well-known/api-catalog` (RFC 9727) using `application/linkset+json`.",
    "",
    "## URL",
    "",
    `${siteUrl}/.well-known/api-catalog`,
    "",
  ].join("\n");

  return new Response(body, {
    status: 200,
    headers: {
      "Content-Type": "text/markdown; charset=utf-8",
      "Cache-Control": "public, max-age=3600, stale-while-revalidate=86400",
    },
  });
}
