"use client";

import { JSX, useEffect, useState } from "react";
import Link from "next/link";
import axiosClient from "@/services/axiosClient";
import axios from "axios";
import Script from "next/script";
import {
  IconMail,
  IconPhone,
  IconHome,
  IconBuildingFactory,
  IconBrandFacebook,
  IconBrandTwitter,
  IconBrandLinkedin,
  IconBrandInstagram,
  IconBrandYoutube,
  IconBrandWhatsapp,
} from "@tabler/icons-react";
import Image from "next/image";

type FooterData = {
  branding: {
    footer_logo_url: string;
    footer_description: string;
    footer_copyright: string;
  };
  contact: {
    email: string;
    phone: string;
    address: string;
  };
  social_media: Record<string, string | null>;
  quick_links: Record<
    "company" | "products" | "support" | "legal",
    { title: string; url: string }[]
  >;
};

type FooterContactInfo = {
  manufacturing_name?: string;
  manufacturing_address?: string;
  manufacturing_city?: string;
  manufacturing_state?: string;
  manufacturing_country?: string;
  manufacturing_postal_code?: string;
};

const Footer = () => {
  const [data, setData] = useState<FooterData | null>(null);
  const [status, setStatus] = useState<
    "idle" | "loading" | "success" | "error"
  >("idle");
  const [email, setEmail] = useState<string>("");
  const [apps, setApps] = useState<{
    smart_app_link?: string;
    attendance_app_link?: string;
  }>({});
  const [contactInfo, setContactInfo] = useState<FooterContactInfo | null>(
    null
  );
  const [visitorCount, setVisitorCount] = useState<number | null>(null);

  const normalizeHref = (url: string) => {
    const u = url || "";
    if (/^(https?:|mailto:|tel:)/i.test(u)) return u;
    return u.startsWith("/") ? u : `/${u}`;
  };

  useEffect(() => {
    const fetchFooter = async () => {
      try {
        const res = await axiosClient.get("/site/footer");
        setData(res.data.data);
      } catch (error) {
        console.error("Footer API Error:", error);
      }
    };
    fetchFooter();
  }, []);

  useEffect(() => {
    const fetchHeaderFooterApps = async () => {
      try {
        const res = await axiosClient.get("/site/header-footer");
        setApps(res.data?.data?.apps || {});
      } catch (error) {
        console.warn("Header-Footer Apps API Error:", error);
      }
    };
    fetchHeaderFooterApps();
  }, []);

  useEffect(() => {
    const fetchContactInfo = async () => {
      try {
        const res = await axiosClient.get("/content/contact-info");
        setContactInfo(res.data?.data || null);
      } catch (error) {
        console.warn("Footer Contact Info API Error:", error);
      }
    };
    fetchContactInfo();
  }, []);

  useEffect(() => {
    const fetchVisitorCount = async () => {
      try {
        const res = await axios.get(
          `${process.env.NEXT_PUBLIC_API_BASE_URL}/analytics/visit-count`
        );
        if (res.data.success) {
          setVisitorCount(res.data.data.total_visits);
        }
      } catch (error) {
        console.warn("Visitor Count API Error:", error);
      }
    };
    fetchVisitorCount();
  }, []);

  const handleSubmitNewsLatter = async (e: React.FormEvent) => {
    e.preventDefault();
    setStatus("loading");
    try {
      const response = await axios.post(
        `https://markvisitor.com/app/dev/website/subscribe.php`,
        { email }
      );
      if (response.data.status === "subscribed") {
        setStatus("success");
        setEmail("");
      }
    } catch (error) {
      console.log(error);
      setStatus("error");
    }
  };

  if (!data) return null;

  const { branding, contact, social_media, quick_links } = data;
  const productLinks = [
    ...(quick_links?.products || []).filter((link) => {
      const title = link.title?.toLowerCase().trim();
      return title !== "downloads" && title !== "download";
    }),
    { title: "3rd Party Software Integration", url: "/integrations" },
  ];

  const socialColors: Record<string, string> = {
    facebook: "#1877F2",
    twitter: "#1DA1F2",
    linkedin: "#0A66C2",
    instagram: "#E4405F",
    youtube: "#FF0000",
    whatsapp: "#25D366",
  };

  const socialIcons: Record<string, JSX.Element> = {
    facebook: <IconBrandFacebook size={20} stroke={1.5} />,
    twitter: <IconBrandTwitter size={20} stroke={1.5} />,
    linkedin: <IconBrandLinkedin size={20} stroke={1.5} />,
    instagram: <IconBrandInstagram size={20} stroke={1.5} />,
    youtube: <IconBrandYoutube size={20} stroke={1.5} />,
    whatsapp: <IconBrandWhatsapp size={20} stroke={1.5} />,
  };

  const manufacturingFullAddress = [
    contactInfo?.manufacturing_address,
    contactInfo?.manufacturing_city,
    contactInfo?.manufacturing_state,
    contactInfo?.manufacturing_country,
    contactInfo?.manufacturing_postal_code,
  ]
    .filter(Boolean)
    .join(", ");

  return (
    <>
    <footer className="bg-[#2B2B2B] text-white">
      <div className="container mx-auto px-4 py-12">
        <div className="grid grid-cols-1 md:grid-cols-4 gap-8 md:gap-12">
          {/* Branding + Apps */}
          <div className="space-y-6">
            <div className="relative h-14 w-64">
              <Image
                src={branding.footer_logo_url || "/logo.png"}
                alt="Footer Logo"
                fill
                className="object-contain"
                style={{ filter: "invert(1) hue-rotate(180deg)" }}
                loading="lazy"
              />
            </div>
            <p className="text-gray-400 text-sm leading-relaxed">
              {branding.footer_description}
            </p>

          {/* Mobile Apps */}
          <div className="mt-4 space-y-2">
            <h4 className="font-semibold text-sm text-gray-300">
              Our Mobile Apps
            </h4>
            <div className="flex flex-col gap-2">
              <Link
                href={
                  apps.smart_app_link ||
                  "https://play.google.com/store/apps/details?id=com.realtimecamsmarthome"
                }
                className="flex items-center bg-[#181210] border border-[#3A2D2A] px-3.5 py-1.5 rounded-lg hover:bg-orange-500/10 transition w-full sm:w-auto max-w-[190px]"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 512 512"
                    className="h-5 w-5 flex-shrink-0"
                  >
                    <path fill="#00e5ff" d="M26.4 8.7C20.6 14.5 17 23.5 17 34.6v442.8c0 11.1 3.6 20.1 9.4 25.9L28.1 506 280 254.1V250v-4.1L28.1 6z"/>
                    <path fill="#ffeb3b" d="M363.3 337.4l-83.3-83.3v-8.2l83.3-83.3 2.1 1.2 98.4 56c28 15.9 28 42.1 0 58l-98.4 56z"/>
                    <path fill="#ff2d55" d="M280 250L28.1 501.9c9.2 9.8 24.3 11 41.2 1.4l294-165.9z"/>
                    <path fill="#00e676" d="M280 250L69.3 10.7C52.4 1.1 37.3 2.3 28.1 12.1L280 250z"/>
                  </svg>
                  <div className="ml-2.5 text-left leading-tight">
                    <div className="text-[9px] text-gray-400 font-semibold uppercase tracking-wider">REALTIME MOBILE</div>
                    <div className="font-bold text-orange-500 text-[11px] mt-0.5">
                      SMART APP
                    </div>
                  </div>
                </Link>
                <Link
                  href={
                    apps.attendance_app_link ||
                    "https://play.google.com/store/apps/details?id=com.RealtimeBiometrics.realtime"
                  }
                  className="flex items-center bg-[#181210] border border-[#3A2D2A] px-3.5 py-1.5 rounded-lg hover:bg-orange-500/10 transition w-full sm:w-auto max-w-[190px]"
                >
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 512 512"
                    className="h-5 w-5 flex-shrink-0"
                  >
                    <path fill="#00e5ff" d="M26.4 8.7C20.6 14.5 17 23.5 17 34.6v442.8c0 11.1 3.6 20.1 9.4 25.9L28.1 506 280 254.1V250v-4.1L28.1 6z"/>
                    <path fill="#ffeb3b" d="M363.3 337.4l-83.3-83.3v-8.2l83.3-83.3 2.1 1.2 98.4 56c28 15.9 28 42.1 0 58l-98.4 56z"/>
                    <path fill="#ff2d55" d="M280 250L28.1 501.9c9.2 9.8 24.3 11 41.2 1.4l294-165.9z"/>
                    <path fill="#00e676" d="M280 250L69.3 10.7C52.4 1.1 37.3 2.3 28.1 12.1L280 250z"/>
                  </svg>
                  <div className="ml-2.5 text-left leading-tight">
                    <div className="text-[9px] text-gray-400 font-semibold uppercase tracking-wider">REALTIME MOBILE</div>
                    <div className="font-bold text-orange-500 text-[11px] mt-0.5">
                      ATTENDANCE APP
                    </div>
                  </div>
                </Link>
              </div>
            </div>
          </div>

          {/* Quick Links */}
          <div className="grid grid-cols-2 gap-6 md:gap-8">
            <div>
              <h4 className="font-semibold text-lg mb-3">Company</h4>
              <ul className="space-y-2 text-gray-400 text-sm">
                {quick_links.company.map((link) => (
                  <li key={link.url}>
                    <Link
                      href={link.url}
                      className="hover:text-orange-500 transition-colors"
                    >
                      {link.title}
                    </Link>
                  </li>
                ))}
              </ul>
            </div>
            <div>
              <h4 className="font-semibold text-lg mb-3">Products</h4>
              <ul className="space-y-2 text-gray-400 text-sm">
                {productLinks.map((link) => (
                  <li key={link.url}>
                    <Link
                      href={link.url}
                      className="hover:text-orange-500 transition-colors"
                    >
                      {link.title}
                    </Link>
                  </li>
                ))}
              </ul>
            </div>
          </div>

          <div className="grid grid-cols-2 gap-6 md:gap-8">
            <div>
              <h4 className="font-semibold text-lg mb-3">Support</h4>
              <ul className="space-y-2 text-gray-400 text-sm">
                {Array.isArray(quick_links.support) &&
                quick_links.support.length > 0 ? (
                  quick_links.support.map((link) => {
                    const isApiRef =
                      link.title?.toLowerCase() === "api reference";
                    const isContactSupport =
                      link.title?.toLowerCase() === "contact support";
                    const isDocumentation =
                      link.title?.toLowerCase() === "documentation";

                    const displayTitle = isApiRef ? "FAQs" : link.title;

                    let displayUrl = normalizeHref(link.url);
                    if (isApiRef) displayUrl = "/faqs";
                    else if (isContactSupport) displayUrl = "/contact";
                    else if (isDocumentation) displayUrl = "/integrations";

                    return (
                      <li key={link.url}>
                        <Link
                          href={displayUrl}
                          className="text-white/55 hover:text-white text-sm"
                        >
                          {displayTitle}
                        </Link>
                      </li>
                    );
                  })
                ) : (
                  <li>
                    <Link
                      href="/faqs"
                      className="text-white/55 hover:text-white text-sm"
                    >
                      FAQs
                    </Link>
                  </li>
                )}
              </ul>
            </div>
            <div>
              <h4 className="font-semibold text-lg mb-3">Legal</h4>
              <ul className="space-y-2 text-gray-400 text-sm">
                {quick_links.legal.map((link) => (
                  <li key={link.url}>
                    <Link
                      href={link.url}
                      className="hover:text-orange-500 transition-colors"
                    >
                      {link.title}
                    </Link>
                  </li>
                ))}
              </ul>
            </div>
          </div>

          {/* Contact */}
          <div className="space-y-4">
            <h4 className="font-semibold text-lg mb-2">Contact</h4>
            <ul className="space-y-2 text-gray-400 text-sm">
              <li className="flex items-center gap-2 hover:text-orange-500 transition-colors">
                <IconMail size={18} className="text-gray-400" />
                <a href={`mailto:${contact.email}`}>{contact.email}</a>
              </li>
              <li className="flex items-center gap-2 hover:text-orange-500 transition-colors">
                <IconPhone size={18} className="text-gray-400" />
                <a href={`tel:${contact.phone}`}>{contact.phone}</a>
              </li>
              <li className="flex items-start gap-2">
                <IconHome size={18} className="text-gray-400 mt-1" />
                <span>{contact.address}</span>
              </li>
              {contactInfo?.manufacturing_address && (
                <li className="flex items-start gap-2">
                  <IconBuildingFactory
                    size={20}
                    className="text-gray-400 mt-1"
                  />
                  <span>{manufacturingFullAddress}</span>
                </li>
              )}
            </ul>
          </div>
        </div>

        {/* Footer Bottom */}
        <div className="mt-10 border-t border-gray-700 pt-6 flex flex-col sm:flex-row justify-between items-center gap-4">
          <div className="flex flex-col sm:flex-row items-center gap-2 sm:gap-6">
            <p className="text-gray-500 text-sm">
              {(branding?.footer_copyright || "© 2026 RS Solutions. All rights reserved.")
                .replace(/R S Solutions\s*-\s*Realtime Biometrics/gi, "RS Solutions")
                .replace(/RealTime Biometrics/gi, "RS Solutions")
                .replace(/Realtime Biometrics/gi, "RS Solutions")
                .replace(/RealtimeBiometrics/gi, "RS Solutions")
                .replace(/R\s*S\s*Solutions/gi, "RS Solutions")}
            </p>
            {visitorCount !== null && (
              <div className="flex items-center gap-2 text-gray-500 text-sm bg-gray-800/50 px-3 py-1 rounded-full border border-gray-700">
                <span className="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                <span>Total Visitors:</span>
                <span className="font-mono font-bold text-gray-300">
                  {visitorCount.toLocaleString()}
                </span>
              </div>
            )}
          </div>

          <div className="flex flex-col sm:flex-row items-center gap-4 w-full sm:w-auto">
            {/* Social Icons */}
            <div className="flex gap-3">
              {Object.entries(social_media).map(([key, url]) =>
                url ? (
                  <a
                    key={key}
                    href={url}
                    target="_blank"
                    rel="noopener noreferrer"
                    className="group relative flex items-center justify-center w-10 h-10 rounded-full bg-[#1C1310] border border-[#4F423D] transition-all duration-300 overflow-hidden hover:border-transparent hover:shadow-lg hover:-translate-y-1"
                  >
                    <div 
                      className="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                      style={{ backgroundColor: socialColors[key] || "#ea580c" }}
                    />
                    <div className="relative z-10 text-gray-400 group-hover:text-white transition-colors duration-300 flex items-center justify-center">
                      {socialIcons[key] || <IconBrandFacebook size={20} stroke={1.5} />}
                    </div>
                  </a>
                ) : null
              )}
            </div>

            {/* Newsletter Subscribe */}
            <form
              onSubmit={handleSubmitNewsLatter}
              className="flex gap-2 w-full sm:w-auto"
            >
              <input
                type="email"
                placeholder="Your email"
                value={email}
                onChange={(e) => setEmail(e.target.value)}
                required
                className="px-3 py-2 rounded-md bg-[#1C1310] border border-[#4F423D] text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-500 w-full sm:w-auto"
              />
              <button
                type="submit"
                disabled={status === "loading"}
                className={`px-4 py-2 rounded-md text-sm font-medium transition-colors ${
                  status === "loading"
                    ? "bg-orange-400 cursor-not-allowed"
                    : "bg-orange-600 hover:bg-orange-700"
                }`}
              >
                {status === "loading" ? "Subscribing..." : "Subscribe"}
              </button>
            </form>
          </div>
        </div>
      </div>
    </footer>
      <Script
        async
        type="module"
        src="https://interfaces.zapier.com/assets/web-components/zapier-interfaces/zapier-interfaces.esm.js"
        strategy="afterInteractive"
      />
      <div
        dangerouslySetInnerHTML={{
          __html:
            "<zapier-interfaces-chatbot-embed is-popup='true' chatbot-id='cmlyqvfm3001c79iovaclbhz1'></zapier-interfaces-chatbot-embed>",
        }}
      />
    </>
  );
};

export default Footer;
