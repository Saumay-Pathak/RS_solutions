const siteUrl =
  process.env.NEXT_PUBLIC_SITE_URL?.replace(/\/+$/, "") ||
  "https://realtimebiometrics.com";

export async function GET() {
  const body = [
    "# Link Headers",
    "",
    "This site advertises machine-readable resources using RFC 8288 Link headers on the homepage response.",
    "",
    "## Homepage Link Relations",
    "",
    `- api-catalog: ${siteUrl}/.well-known/api-catalog`,
    `- service-desc: ${siteUrl}/.well-known/openapi.json`,
    `- service-doc: ${siteUrl}/reference/get_banks.md`,
    `- describedby: ${siteUrl}/sitemap.xml`,
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
