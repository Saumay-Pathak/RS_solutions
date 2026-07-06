const siteUrl =
  process.env.NEXT_PUBLIC_SITE_URL?.replace(/\/+$/, "") ||
  "https://realtimebiometrics.com";

export async function GET() {
  const doc = {
    openapi: "3.0.3",
    info: {
      title: "Realtime Biometrics Site API",
      version: "1.0.0",
    },
    servers: [{ url: siteUrl }],
    paths: {
      "/api/services/{slug}": {
        get: {
          summary: "Get service by slug",
          parameters: [
            { name: "slug", in: "path", required: true, schema: { type: "string" } },
          ],
          responses: {
            "200": { description: "Service payload" },
            "400": { description: "Missing or invalid slug" },
            "404": { description: "Service not found" },
            "429": { description: "Too many requests" },
          },
        },
      },
      "/api/catalogue": {
        get: {
          summary: "Fetch catalogue PDF (proxy)",
          parameters: [
            { name: "doc", in: "query", required: true, schema: { type: "string" } },
            { name: "title", in: "query", required: false, schema: { type: "string" } },
          ],
          responses: {
            "200": { description: "PDF or upstream content" },
            "400": { description: "Missing or invalid parameters" },
            "404": { description: "Not found" },
            "429": { description: "Too many requests" },
          },
        },
      },
      "/api/hero-content": {
        get: {
          summary: "Fetch hero HTML content (proxy + transform)",
          parameters: [
            { name: "file", in: "query", required: true, schema: { type: "string" } },
          ],
          responses: {
            "200": { description: "HTML content" },
            "400": { description: "Invalid file path" },
          },
        },
      },
      "/api/categories": {
        get: {
          summary: "Get sample categories payload",
          responses: {
            "200": { description: "Categories" },
            "429": { description: "Too many requests" },
          },
        },
      },
    },
  };

  return Response.json(doc, {
    status: 200,
    headers: {
      "Content-Type": "application/vnd.oai.openapi+json; charset=utf-8",
      "Cache-Control": "public, max-age=3600, stale-while-revalidate=86400",
    },
  });
}
