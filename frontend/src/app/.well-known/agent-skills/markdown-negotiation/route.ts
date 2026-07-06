const siteUrl =
  process.env.NEXT_PUBLIC_SITE_URL?.replace(/\/+$/, "") ||
  "https://realtimebiometrics.com";

export async function GET() {
  const body = [
    "# Markdown Negotiation",
    "",
    "When a request includes `Accept: text/markdown`, this site can respond with a Markdown representation of the requested page.",
    "",
    "## How to use",
    "",
    "```bash",
    `curl ${siteUrl}/ -H \"Accept: text/markdown\"`,
    "```",
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
