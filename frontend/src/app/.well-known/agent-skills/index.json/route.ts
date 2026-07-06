import { createHash } from "crypto";

const siteUrl =
  process.env.NEXT_PUBLIC_SITE_URL?.replace(/\/+$/, "") ||
  "https://realtimebiometrics.com";

const sha256Hex = (text: string) => createHash("sha256").update(text).digest("hex");

const skillDoc = (path: string) => `${siteUrl}${path}`;

const linkHeadersBody = () =>
  [
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

const markdownNegotiationBody = () =>
  [
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

const apiCatalogBody = () =>
  [
    "# API Catalog",
    "",
    "This site publishes an API catalog at `/.well-known/api-catalog` (RFC 9727) using `application/linkset+json`.",
    "",
    "## URL",
    "",
    `${siteUrl}/.well-known/api-catalog`,
    "",
  ].join("\n");

const webBotAuthBody = () =>
  [
    "# Web Bot Auth",
    "",
    "This site publishes a JWKS for HTTP message signatures at:",
    "",
    `${siteUrl}/.well-known/http-message-signatures-directory`,
    "",
  ].join("\n");

const contentSignalsBody = () =>
  [
    "# Content Signals",
    "",
    "This site declares AI usage preferences via a `Content-Signal:` directive in `robots.txt`.",
    "",
    "Example:",
    "",
    "```txt",
    "Content-Signal: ai-train=no, search=yes, ai-input=no",
    "```",
    "",
  ].join("\n");

export async function GET() {
  const skills = [
    {
      name: "link-headers",
      type: "capability",
      description: "Advertise machine-readable resources using RFC 8288 Link headers on the homepage.",
      url: skillDoc("/.well-known/agent-skills/link-headers"),
      sha256: sha256Hex(linkHeadersBody()),
    },
    {
      name: "markdown-negotiation",
      type: "capability",
      description: "Return a Markdown representation when Accept: text/markdown is requested.",
      url: skillDoc("/.well-known/agent-skills/markdown-negotiation"),
      sha256: sha256Hex(markdownNegotiationBody()),
    },
    {
      name: "api-catalog",
      type: "capability",
      description: "Publish an API catalog at /.well-known/api-catalog (application/linkset+json).",
      url: skillDoc("/.well-known/agent-skills/api-catalog"),
      sha256: sha256Hex(apiCatalogBody()),
    },
    {
      name: "web-bot-auth",
      type: "capability",
      description: "Publish a JWKS at /.well-known/http-message-signatures-directory for request signing verification.",
      url: skillDoc("/.well-known/agent-skills/web-bot-auth"),
      sha256: sha256Hex(webBotAuthBody()),
    },
    {
      name: "content-signals",
      type: "capability",
      description: "Declare AI usage preferences in robots.txt via Content-Signal.",
      url: skillDoc("/.well-known/agent-skills/content-signals"),
      sha256: sha256Hex(contentSignalsBody()),
    },
  ];

  const index = {
    $schema: "https://agentskills.io/schemas/agent-skills-index.v0.2.0.schema.json",
    skills,
  };

  return Response.json(index, {
    status: 200,
    headers: {
      "Cache-Control": "public, max-age=3600, stale-while-revalidate=86400",
    },
  });
}
