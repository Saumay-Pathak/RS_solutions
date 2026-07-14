const apiBase = process.env.NEXT_PUBLIC_API_BASE_URL || "";
const defaultStorage = apiBase
  ? apiBase.replace(/\/api\/?$/, "/storage/")
  : "http://localhost:8000/storage/";
const rawStorage = process.env.NEXT_PUBLIC_STORAGE_URL || defaultStorage;

export const baseUri = rawStorage.endsWith("/") ? rawStorage : `${rawStorage}/`;
export const realtimeAppStore = 'https://apps.apple.com/in/app/realtime-attendance/id1077201553';
export const realtimeAppPlayStore = 'https://play.google.com/store/apps/developer?id=REALTIME+BIOMETRICS+INDIA+PRIVATE+LIMITED';