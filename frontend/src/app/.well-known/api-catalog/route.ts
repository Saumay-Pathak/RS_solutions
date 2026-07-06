import { createHash } from "crypto";

const siteUrl =
  process.env.NEXT_PUBLIC_SITE_URL?.replace(/\/+$/, "") ||
  "https://realtimebiometrics.com";

const buildCatalog = () => {
  const anchor = siteUrl;
  const openApi = `${siteUrl}/.well-known/openapi.json`;
  const docs = `${siteUrl}/reference/get_banks.md`;
  const status = `${siteUrl}/api/categories`;

  return {
    linkset: [
      {
        anchor,
        items: [
          {
            href: `${siteUrl}/.well-known/api-catalog`,
            rel: "api-catalog",
            type: "application/linkset+json",
          },
          {
            href: openApi,
            rel: "service-desc",
            type: "application/vnd.oai.openapi+json",
          },
          {
            href: docs,
            rel: "service-doc",
            type: "text/markdown",
          },
          {
            href: status,
            rel: "status",
            type: "application/json",
          },
        ],
      },
    ],
  };
};

export async function GET() {
  const bodyObj = buildCatalog();
  const body = JSON.stringify(bodyObj);
  const etag = createHash("sha256").update(body).digest("hex");

  return new Response(body, {
    status: 200,
    headers: {
      "Content-Type": "application/linkset+json; charset=utf-8",
      "Cache-Control": "public, max-age=3600, stale-while-revalidate=86400",
      ETag: `"${etag}"`,
    },
  });
}
