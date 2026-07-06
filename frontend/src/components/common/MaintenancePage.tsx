// app/maintenance/page.tsx
import { realtimeAppPlayStore, realtimeAppStore } from "@/services/constant";
import { fetchContactInfo } from "@/services/contactService";
import { Phone, Mail } from "lucide-react";
import Link from "next/link";
import Image from "next/image";
import { FaFacebook, FaInstagram, FaTwitter, FaLinkedin } from "react-icons/fa";

export const metadata = {
  title: "Under Maintenance | Realtime Biometrics",
  description:
    "Our website is currently under construction. We'll be back very soon with something amazing!",
};

export default async function MaintenancePage() {
  const info = await fetchContactInfo();

  return (
    <div className="min-h-screen flex items-center justify-center bg-gray-100 p-4">
      <div className="bg-white shadow-lg rounded-2xl max-w-xl w-full p-10 text-center">
        {/* Logo */}
        <div className="mx-auto mb-6 flex justify-center">
          <Image src="/images/logo-black.png" alt="Realtime Logo" width={200} height={64} />
        </div>

        {/* Main Title */}
        <h1 className="text-3xl font-extrabold text-gray-900">
          We&apos;re Under Maintenance
        </h1>

        {/* Description */}
        <p className="text-gray-600 mt-3">
          Our new website is being crafted with care. We&apos;re working hard to
          bring you something amazing and will be launching very soon!
        </p>

        {/* Contact Info */}
        <div className="flex flex-col items-center gap-3 mt-6 text-gray-700">
          {info?.enquiry_number && (
            <p className="flex items-center gap-2">
              <Phone size={18} /> {info.enquiry_number}
            </p>
          )}

          {info?.general_email && (
            <p className="flex items-center gap-2">
              <Mail size={18} /> {info.general_email}
            </p>
          )}
        </div>

        {/* Buttons */}
        <div className="mt-6 flex flex-col gap-3">
          <a
            href="https://www.supportrealtime.com/"
            className="bg-orange-600 text-white px-6 py-3 rounded-md font-semibold hover:bg-orange-700 transition">
            Download Softwares
          </a>

          <Link
            href="/sales"
            className="bg-teal-700 text-white px-6 py-3 rounded-md font-semibold hover:bg-teal-800 transition">
            Request a call back
          </Link>
        </div>

        {/* App Download */}
        <div className="mt-10">
          <h3 className="text-lg font-semibold">Download Our App</h3>

          <div className="flex justify-center gap-4 mt-5">
            <a
              href={realtimeAppStore}
              className="flex items-center gap-2 bg-black text-white px-4 py-2 rounded-md hover:opacity-80 transition">
              <Image src="/images/app-store.png" alt="App Store" width={120} height={36} />
              <span className="text-sm text-left leading-tight">
                <span className="block text-[10px]">Download on the</span>
                <span className="font-semibold text-sm">App Store</span>
              </span>
            </a>

            <a
              href={realtimeAppPlayStore}
              className="flex items-center gap-2 bg-black text-white px-4 py-2 rounded-md hover:opacity-80 transition">
              <Image src="/images/gplay.png" alt="Google Play" width={120} height={36} />
              <span className="text-sm text-left leading-tight">
                <span className="block text-[10px]">GET IT ON</span>
                <span className="font-semibold text-sm">Google Play</span>
              </span>
            </a>
          </div>
        </div>

        {/* Social Media */}
        <div className="border-t mt-10 pt-6 flex justify-center gap-5 text-gray-600">
          {info?.social_media_links?.facebook && (
            <a href={info.social_media_links.facebook} target="_blank">
              <FaFacebook className="hover:text-blue-600 text-[25px]" />
            </a>
          )}

          {info?.social_media_links?.twitter && (
            <a href={info.social_media_links.twitter} target="_blank">
              <FaTwitter className="hover:text-blue-400 text-[25px]" />
            </a>
          )}

          {info?.social_media_links?.linkedin && (
            <a href={info.social_media_links.linkedin} target="_blank">
              <FaLinkedin className="hover:text-blue-700 text-[25px]" />
            </a>
          )}

          {info?.social_media_links?.instagram && (
            <a href={info.social_media_links.instagram} target="_blank">
              <FaInstagram className="hover:text-pink-600 text-[25px]" />
            </a>
          )}
        </div>
      </div>
    </div>
  );
}
