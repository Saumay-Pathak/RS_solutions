import axios, { type AxiosResponse, type AxiosRequestConfig } from "axios";

const axiosClient = axios.create({
  baseURL: process.env.NEXT_PUBLIC_API_BASE_URL || "https://app.realtimebiometrics.net/api",
  headers: {
    "Content-Type": "application/json",
  },
  withCredentials: false, // agar cookies ya auth chahiye
});

type CacheableAxiosRequestConfig = AxiosRequestConfig & {
  noCache?: boolean;
  cacheTtlMs?: number;
};

type CacheEntry = {
  timestamp: number;
  data?: AxiosResponse<unknown>;
  promise?: Promise<AxiosResponse<unknown>>;
};

const responseCache = new Map<string, CacheEntry>();
const DEFAULT_TTL_MS = 60_000;

const buildCacheKey = (url: string, config?: AxiosRequestConfig) => {
  const params = config?.params ? JSON.stringify(config.params) : "";
  return `${url}|${params}`;
};

const isCacheableUrl = (url: string) => {
  const u = url.replace(/^https?:\/\/[^/]+/, "");
  if (!u) return false;
  if (u.startsWith("/analytics")) return false;
  if (u.startsWith("/content/") || u.startsWith("content/")) return true;
  if (u.startsWith("/site/") || u.startsWith("site/")) return true;
  if (u.startsWith("/header") || u.startsWith("/footer")) return true;
  return false;
};

const originalGet = axiosClient.get.bind(axiosClient);

const isErrWithResponseStatus = (err: unknown): err is { response?: { status?: number } } => {
  if (typeof err !== "object" || err === null) return false;
  const record = err as Record<string, unknown>;
  if (!("response" in record)) return false;
  const response = record.response as unknown;
  if (typeof response !== "object" || response === null) return false;
  return true;
};

axiosClient.get = async function <T = unknown, R = AxiosResponse<T>>(
  url: string,
  config?: CacheableAxiosRequestConfig
): Promise<R> {
  const strUrl = typeof url === "string" ? url : String(url || "");
  const shouldCache = !config?.noCache && isCacheableUrl(strUrl);

  if (!shouldCache) {
    return originalGet(url, config) as Promise<R>;
  }

  const key = buildCacheKey(strUrl, config);
  const now = Date.now();
  const entry = responseCache.get(key);
  const ttl = typeof config?.cacheTtlMs === "number" ? config.cacheTtlMs : DEFAULT_TTL_MS;

  if (entry && entry.data && now - entry.timestamp < ttl && !entry.promise) {
    return entry.data as R;
  }

  if (entry && entry.promise && now - entry.timestamp < ttl) {
    return entry.promise as Promise<R>;
  }

  const promise = originalGet<T, AxiosResponse<T>>(url, config)
    .then((res) => {
      responseCache.set(key, { timestamp: Date.now(), data: res as AxiosResponse<unknown> });
      return res;
    })
    .catch((err: unknown) => {
      if (entry && entry.data && isErrWithResponseStatus(err) && err.response?.status === 429) {
        return entry.data as R;
      }
      throw err;
    });

  responseCache.set(key, { timestamp: now, data: entry?.data, promise: promise as Promise<AxiosResponse<unknown>> });
  return promise as Promise<R>;
};


export default axiosClient;
