// app/layout.tsx
import type { Metadata } from "next";
import { Montserrat, Geist_Mono, Lato } from "next/font/google";
import "./globals.css";
import FloatingButtons from "@/components/common/FloatingButtons";
import PopupModal from "@/components/common/PopupModal";
import AnalyticsProvider from "@/components/layout/AnalyticsProvider";
import GlobalPreloader from "@/components/common/GlobalPreloader";
import { Suspense } from "react";
import { GoogleAnalytics } from "@next/third-parties/google";

// Use Montserrat for the primary sans font
const montserrat = Montserrat({
  variable: "--font-montserrat",
  subsets: ["latin"],
});

const geistMono = Geist_Mono({
  variable: "--font-geist-mono",
  subsets: ["latin"],
});

const lato = Lato({
  variable: "--font-lato",
  subsets: ["latin"],
  weight: ["300", "400", "700"],
});

const siteUrl =
  process.env.NEXT_PUBLIC_SITE_URL?.replace(/\/+$/, "") ||
  "https://realtimebiometrics.com";

export const metadata: Metadata = {
  metadataBase: new URL(siteUrl),
  title: {
    default: "R S Solutions - Realtime Biometrics",
    template: "%s | R S Solutions - Realtime Biometrics",
  },
  description: "Advanced security systems for homes and businesses",
  alternates: { canonical: "/" },
  openGraph: {
    title: "R S Solutions - Realtime Biometrics",
    description: "Advanced security systems for homes and businesses",
    url: siteUrl,
    siteName: "R S Solutions - Realtime Biometrics",
    type: "website",
  },
  twitter: {
    card: "summary_large_image",
    title: "R S Solutions - Realtime Biometrics",
    description: "Advanced security systems for homes and businesses",
  },
  robots: {
    index: true,
    follow: true,
    googleBot: {
      index: true,
      follow: true,
      "max-image-preview": "large",
      "max-snippet": -1,
      "max-video-preview": -1,
    },
  },
  verification: {
    google: "K1I3dzLw-Cm3WGFqWD3IF5xDJX8T5FXIR0G-OYKP1xQ",
  },
};

export default async function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return (
    <html lang="en">
      <body
        suppressHydrationWarning
        className={`${montserrat.variable} ${geistMono.variable} ${lato.variable} antialiased`}
      >
        <GoogleAnalytics gaId="G-FQ4LRPFW2F" />
        <AnalyticsProvider />
        <Suspense fallback={null}>
          <GlobalPreloader />
        </Suspense>
        {children}
        <FloatingButtons />
        <PopupModal />
      </body>
    </html>
  );
}
