const siteUrl =
  process.env.NEXT_PUBLIC_SITE_URL?.replace(/\/+$/, "") ||
  "https://realtimebiometrics.com";

export async function GET() {
  const body = [
    "# Web Bot Auth",
    "",
    "This site publishes a JWKS for HTTP message signatures at:",
    "",
    `${siteUrl}/.well-known/http-message-signatures-directory`,
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
