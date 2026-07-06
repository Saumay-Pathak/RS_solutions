export async function GET(req: Request) {
  const url = new URL(req.url);
  const base = `${url.protocol}//${url.host}`;
  const target = `${base}/.well-known/agent-skills/index.json`;
  return new Response(null, {
    status: 302,
    headers: {
      Location: target,
      "Cache-Control": "public, max-age=3600, stale-while-revalidate=86400",
    },
  });
}
