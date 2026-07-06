const siteUrl =
  process.env.NEXT_PUBLIC_SITE_URL?.replace(/\/+$/, "") ||
  "https://realtimebiometrics.com";

export async function GET() {
  const issuer = siteUrl;
  const jwksUri = `${siteUrl}/.well-known/jwks.json`;

  return Response.json(
    {
      issuer,
      jwks_uri: jwksUri,
      service_documentation: `${siteUrl}/reference/get_banks.md`,
      grant_types_supported: ["authorization_code", "client_credentials"],
      token_endpoint_auth_methods_supported: ["client_secret_post", "client_secret_basic"],
    },
    {
      status: 200,
      headers: {
        "Cache-Control": "public, max-age=3600, stale-while-revalidate=86400",
      },
    }
  );
}
