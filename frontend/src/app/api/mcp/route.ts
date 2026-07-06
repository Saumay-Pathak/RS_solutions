export async function GET() {
  return Response.json(
    {
      ok: false,
      error: "MCP transport not configured",
    },
    {
      status: 501,
      headers: {
        "Content-Type": "application/json; charset=utf-8",
      },
    }
  );
}
