const keys = [
  {
    kty: "OKP",
    crv: "Ed25519",
    x: "addNzLJixQufErop0hqzvHqc8K2rzVov0V_9fq-VHPA",
    kid: "realtime-ed25519-1",
    use: "sig",
    alg: "EdDSA",
  },
] as const;

export async function GET() {
  return Response.json(
    { keys },
    {
      status: 200,
      headers: {
        "Cache-Control": "public, max-age=3600, stale-while-revalidate=86400",
      },
    }
  );
}
