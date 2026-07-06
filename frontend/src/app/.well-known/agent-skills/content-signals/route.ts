export async function GET() {
  const body = [
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

  return new Response(body, {
    status: 200,
    headers: {
      "Content-Type": "text/markdown; charset=utf-8",
      "Cache-Control": "public, max-age=3600, stale-while-revalidate=86400",
    },
  });
}
