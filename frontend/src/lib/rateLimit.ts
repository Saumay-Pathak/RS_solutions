import type { NextRequest } from "next/server";

export type RateLimitOptions = {
  limit: number;
  windowMs: number;
  scope?: string;
};

const getClientIP = (req: NextRequest): string => {
  const xff = req.headers.get("x-forwarded-for") || "";
  const ip = xff.split(",")[0].trim() || req.headers.get("x-real-ip") || "unknown";
  return String(ip);
};

type Bucket = { count: number; start: number };
const globalKey = "__RATE_LIMIT_STORE__";
type GlobalWithStore = typeof globalThis & { [key: string]: Map<string, Bucket> | undefined };
const g = globalThis as GlobalWithStore;
let store = g[globalKey];
if (!store) {
  store = new Map<string, Bucket>();
  g[globalKey] = store;
}

export const checkRateLimit = (req: NextRequest, options: RateLimitOptions) => {
  const ip = getClientIP(req);
  const scope = String(options.scope || "global");
  const key = `${scope}:${ip}`;
  const now = Date.now();
  const windowMs = Math.max(1000, options.windowMs);
  const limit = Math.max(1, options.limit);

  const bucket = store.get(key);
  if (!bucket || now - bucket.start >= windowMs) {
    store.set(key, { count: 1, start: now });
    return { allowed: true, remaining: Math.max(0, limit - 1), reset: windowMs };
  }

  if (bucket.count >= limit) {
    const resetMs = windowMs - (now - bucket.start);
    return { allowed: false, remaining: 0, reset: Math.max(0, resetMs) };
  }

  bucket.count += 1;
  store.set(key, bucket);
  return { allowed: true, remaining: Math.max(0, limit - bucket.count), reset: windowMs - (now - bucket.start) };
};

export const rateLimitHeaders = (limit: number, remaining: number, resetMs: number) => {
  const resetSeconds = Math.ceil(resetMs / 1000);
  return {
    "X-RateLimit-Limit": String(limit),
    "X-RateLimit-Remaining": String(Math.max(0, remaining)),
    "X-RateLimit-Reset": String(resetSeconds),
    "Retry-After": String(resetSeconds),
  } as Record<string, string>;
};
