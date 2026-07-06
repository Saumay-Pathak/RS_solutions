import { NextRequest, NextResponse } from "next/server";
import { checkRateLimit, rateLimitHeaders } from "@/lib/rateLimit";


const SAMPLE = [
  {
    title: "Biometrics Attendance",
    items: Array.from({ length: 10 }).map((_, i) => ({
      id: `b-${i + 1}`,
      name: `Realtime C${100 + i}`,
      image: `https://realtimebiometrics.com/upload/1202240329_td1d.jpg`,
    })),
  },
  {
    title: "Standalone Access Control",
    items: Array.from({ length: 8 }).map((_, i) => ({
      id: `s-${i + 1}`,
      name: `Realtime S${200 + i}`,
      image: `https://realtimebiometrics.com/upload/2302251128_rs%2070f.jpg`,
    })),
  },
  {
    title: "Long Range AI Face Recognition Attendance System",
    items: Array.from({ length: 7 }).map((_, i) => ({
      id: `l-${i + 1}`,
      name: `Realtime F${300 + i}`,
      image: `https://realtimebiometrics.com/upload/1202240329_td1d.jpg`,
    })),
  },
  {
    title: "RFID Attendance Machines",
    items: Array.from({ length: 6 }).map((_, i) => ({
      id: `r-${i + 1}`,
      name: `Realtime R${400 + i}`,
      image: `https://realtimebiometrics.com/upload/0801251213_pro%20304f+.png`,
    })),
  },
  {
    title: "WiFi & Cloud-Based Attendance Systems",
    items: Array.from({ length: 6 }).map((_, i) => ({
      id: `w-${i + 1}`,
      name: `Realtime W${500 + i}`,
      image: `https://realtimebiometrics.com/upload/1202240317_pro%201700F.jpg`,
    })),
  },
  {
    title: "Smart Door Lock Systems",
    items: Array.from({ length: 5 }).map((_, i) => ({
      id: `d-${i + 1}`,
      name: `Realtime DL${600 + i}`,
      image: `https://realtimebiometrics.com/upload/2302251128_rs%2070f.jpg`,
    })),
  },
  {
    title: "Portable Time Attendance Devices",
    items: Array.from({ length: 4 }).map((_, i) => ({
      id: `p-${i + 1}`,
      name: `Realtime P${700 + i}`,
      image: `https://realtimebiometrics.com/upload/0801251201_c101+.png`,
    })),
  },
  {
    title: "Temperature & Mask Detection Terminals",
    items: Array.from({ length: 4 }).map((_, i) => ({
      id: `t-${i + 1}`,
      name: `Realtime TM${800 + i}`,
      image: `https://realtimebiometrics.com/upload/2805220119_RS%209n.png`,
    })),
  },
];



export async function GET(req: NextRequest) {
  const rl = checkRateLimit(req, { limit: 240, windowMs: 60_000, scope: "categories" });
  if (!rl.allowed) {
    return NextResponse.json(
      { success: false, error: "Too many requests" },
      { status: 429, headers: rateLimitHeaders(240, rl.remaining, rl.reset) }
    );
  }
  return NextResponse.json(SAMPLE, { headers: rateLimitHeaders(240, rl.remaining, rl.reset) });
}
