"use client";
import axiosClient from "@/services/axiosClient";

// --------------------- Types ---------------------
export type DeviceInfo = {
  device_type: "mobile" | "desktop";
  browser: string;
  platform: string;
  screen_width: number;
  screen_height: number;
};

export type VisitPayload = {
  session_id: string;
  url: string;
  page_title: string;
  referrer: string | null;
  utm_source?: string | null;
  utm_medium?: string | null;
  utm_campaign?: string | null;
} & DeviceInfo;

export interface VisitResponse {
  success?: boolean;
  message?: string;
  _id?: string;
  visit_id?: string;
  registration_id?: string;
  [key: string]: unknown;
}

export type UpdateVisitPayload = {
  visit_id: string;
  time_on_page: number;
  is_bounce: boolean;
};

export type ActivityPayload = {
  session_id: string;
  action: string;
  element: string | null;
  element_id: string | null;
  element_text: string | null;
  page_url: string;
  coordinates_x: number | null;
  coordinates_y: number | null;
  device_type: string;
};

// --------------------- Constants ---------------------
const API_BASE = `/analytics`;

// --------------------- Helpers ---------------------
function getSessionId(): string {
  if (typeof window === "undefined") return "server";
  let sessionId = sessionStorage.getItem("session_id");
  if (!sessionId) {
    sessionId = "sess_" + Math.random().toString(36).substring(2, 12);
    sessionStorage.setItem("session_id", sessionId);
  }
  return sessionId;
}

function getDeviceInfo(): DeviceInfo {
  if (typeof navigator === "undefined" || typeof window === "undefined") {
    return {
      device_type: "desktop",
      browser: "unknown",
      platform: "unknown",
      screen_width: 0,
      screen_height: 0,
    };
  }

  return {
    device_type: /Mobi|Android/i.test(navigator.userAgent)
      ? "mobile"
      : "desktop",
    browser: navigator.userAgent,
    platform: navigator.platform,
    screen_width: window.innerWidth,
    screen_height: window.innerHeight,
  };
}

function getUTMParams(): {
  utm_source?: string | null;
  utm_medium?: string | null;
  utm_campaign?: string | null;
} {
  if (typeof window === "undefined") return {};
  const params = new URLSearchParams(window.location.search);
  return {
    utm_source: params.get("utm_source"),
    utm_medium: params.get("utm_medium"),
    utm_campaign: params.get("utm_campaign"),
  };
}

// --------------------- API Functions ---------------------

// ✅ Record a New Visit
export async function recordVisit(): Promise<VisitResponse | null> {
  try {
    const session_id = getSessionId();
    const device = getDeviceInfo();
    const utm = getUTMParams();

    const payload: VisitPayload = {
      session_id,
      url: window.location.href,
      page_title: document.title,
      referrer: document.referrer || null,
      ...utm,
      ...device,
    };

    const res = await axiosClient.post(`${API_BASE}/visits`, payload);
    return (res.data?.data as VisitResponse) || (res.data as VisitResponse) || null;
  } catch (err) {
    console.error("❌ Error recording visit:", err);
    return null;
  }
}

// ✅ Update Visit Time on Page Leave
export async function updateVisit(visit_id: string, time_on_page: number): Promise<void> {
  try {
    const payload: UpdateVisitPayload = {
      visit_id,
      time_on_page,
      is_bounce: time_on_page < 5000,
    };
    await axiosClient.put(`${API_BASE}/visits`, payload);
  } catch (err) {
    console.error("❌ Error updating visit:", err);
  }
}

// ✅ Record Single Activity (Click, Scroll, etc.)
export async function recordActivity(
  action: string,
  element?: HTMLElement | null,
  event?: MouseEvent | null
): Promise<void> {
  try {
    const session_id = getSessionId();
    const device_type = getDeviceInfo().device_type;

    const payload: ActivityPayload = {
      session_id,
      action,
      element: element?.tagName?.toLowerCase() || null,
      element_id: element?.id || null,
      element_text: element?.textContent?.substring(0, 100) || null,
      page_url: window.location.href,
      coordinates_x: event ? Math.round(event.clientX) : null,
      coordinates_y: event ? Math.round(event.clientY) : null,
      device_type,
    };

    await axiosClient.post(`${API_BASE}/activities`, payload);
  } catch (err) {
    console.error("❌ Error recording activity:", err);
  }
}

// ✅ Batch Multiple Activities Together
const activityQueue: ActivityPayload[] = [];
let batchTimer: ReturnType<typeof setTimeout> | null = null;

export function queueActivity(
  action: string,
  element?: HTMLElement | null,
  event?: MouseEvent | null
): void {
  const session_id = getSessionId();
  const device_type = getDeviceInfo().device_type;

  const activity: ActivityPayload = {
    session_id,
    action,
    element: element?.tagName?.toLowerCase() || null,
    element_id: element?.id || null,
    element_text: element?.textContent?.substring(0, 100) || null,
    page_url: window.location.href,
    coordinates_x: event ? Math.round(event.clientX) : null,
    coordinates_y: event ? Math.round(event.clientY) : null,
    device_type,
  };

  activityQueue.push(activity);

  if (!batchTimer) {
    batchTimer = setTimeout(sendActivityBatch, 4000); // send every 4 seconds
  }
}

async function sendActivityBatch(): Promise<void> {
  if (activityQueue.length === 0) return;

  const batchToSend = activityQueue.splice(0, 100);

  try {
    await axiosClient.post(`${API_BASE}/activities/batch`, {
      activities: batchToSend,
    });
  } catch (err) {
    console.error("❌ Error sending batch activities:", err);
  } finally {
    batchTimer = null;
  }
}
