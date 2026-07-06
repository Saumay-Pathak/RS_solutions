const siteUrl =
  process.env.NEXT_PUBLIC_SITE_URL?.replace(/\/+$/, "") ||
  "https://realtimebiometrics.com";

export async function GET() {
  return Response.json(
    {
      serverInfo: {
        name: "Realtime Biometrics",
        version: "1.0.0",
      },
      transport: {
        type: "http",
        endpoint: `${siteUrl}/api/mcp`,
      },
      capabilities: {
        tools: [],
      },
    },
    {
      status: 200,
      headers: {
        "Cache-Control": "public, max-age=3600, stale-while-revalidate=86400",
      },
    }
  );
}
