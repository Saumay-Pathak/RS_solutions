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
      authorization_endpoint: `${siteUrl}/oauth2/authorize`,
      token_endpoint: `${siteUrl}/oauth2/token`,
      response_types_supported: ["code"],
      subject_types_supported: ["public"],
      id_token_signing_alg_values_supported: ["EdDSA"],
    },
    {
      status: 200,
      headers: {
        "Cache-Control": "public, max-age=3600, stale-while-revalidate=86400",
      },
    }
  );
}
