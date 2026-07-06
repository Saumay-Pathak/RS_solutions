const siteUrl =
  process.env.NEXT_PUBLIC_SITE_URL?.replace(/\/+$/, "") ||
  "https://realtimebiometrics.com";

export async function GET() {
  return Response.json(
    [
      {
        url: `${siteUrl}/.well-known/mcp/server-card.json`,
      },
    ],
    {
      status: 200,
      headers: {
        "Cache-Control": "public, max-age=3600, stale-while-revalidate=86400",
      },
    }
  );
}
