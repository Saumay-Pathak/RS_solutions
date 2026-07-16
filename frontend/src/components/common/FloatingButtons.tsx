"use client";

import { useEffect, useRef, useState } from "react";
import Link from "next/link";
import { motion, AnimatePresence, Variants } from "framer-motion";
import {
  IconBrandWhatsapp,
  IconHeadset,
  IconPhoneCall,
  IconMessageCircleQuestion,
} from "@tabler/icons-react";

const actionVariants: Variants = {
  hidden: { opacity: 0, y: 20, scale: 0.9 },
  visible: (i: number) => ({
    opacity: 1,
    y: 0,
    scale: 1,
    transition: {
      delay: i * 0.06,
      type: "spring",
      stiffness: 420,
      damping: 24,
    },
  }),
  exit: { opacity: 0, y: 20, scale: 0.9 },
};

export default function AskRiaActions() {
  const [mounted, setMounted] = useState(false);
  const [open, setOpen] = useState(false);
  const fabRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    setMounted(true);
  }, []);

  /* --------- Auto close on outside tap --------- */
  useEffect(() => {
    function handleOutsideClick(e: MouseEvent | TouchEvent) {
      if (fabRef.current && !fabRef.current.contains(e.target as Node)) {
        setOpen(false);
      }
    }

    if (open) {
      document.addEventListener("mousedown", handleOutsideClick);
      document.addEventListener("touchstart", handleOutsideClick);
    }

    return () => {
      document.removeEventListener("mousedown", handleOutsideClick);
      document.removeEventListener("touchstart", handleOutsideClick);
    };
  }, [open]);

  if (!mounted) {
    return null;
  }

  return (
    <>
      {/* ================= MOBILE FAB ================= */}
      <div ref={fabRef} className="fixed bottom-6 right-6 z-50 lg:hidden">
        <AnimatePresence>
          {open && (
            <motion.div
              initial="hidden"
              animate="visible"
              exit="exit"
              className="mb-4 flex flex-col items-center gap-3"
            >
              {/* WhatsApp */}
              <motion.div custom={0} variants={actionVariants}>
                <Link
                  href="https://wa.me/918080892888?text=Hi%2C%20I%20need%20help%20with%20RS%20Solutions%20Biomatric%20Devices."
                  target="_blank"
                  aria-label="Chat on WhatsApp"
                  className="flex h-14 w-14 items-center justify-center
                             rounded-full bg-white shadow-lg"
                >
                  <IconBrandWhatsapp size={26} className="text-green-500" />
                </Link>
              </motion.div>

              {/* Call */}
              <motion.div custom={1} variants={actionVariants}>
                <Link
                  href="tel:8080892888"
                  aria-label="Call RIA"
                  className="flex h-14 w-14 items-center justify-center
                             rounded-full bg-white shadow-lg"
                >
                  <IconHeadset size={24} className="text-orange-500" />
                </Link>
              </motion.div>

              {/* Request Callback */}
              <motion.div custom={2} variants={actionVariants}>
                <Link
                  href="/sales"
                  aria-label="Request a Callback"
                  className="flex h-14 w-14 items-center justify-center
                             rounded-full bg-white shadow-lg"
                >
                  <IconPhoneCall size={24} className="text-blue-600" />
                </Link>
              </motion.div>
            </motion.div>
          )}
        </AnimatePresence>

        {/* Main FAB */}
        <motion.button
          whileTap={{ scale: 0.9 }}
          whileHover={{ scale: 1.05 }}
          onClick={() => setOpen(!open)}
          aria-expanded={open}
          aria-label="Ask RIA"
          className="flex h-16 w-16 items-center justify-center rounded-full
                     bg-orange-500 text-white shadow-xl"
        >
          <motion.div
            animate={{ rotate: open ? 45 : 0 }}
            transition={{ type: "spring", stiffness: 300, damping: 20 }}
          >
            <IconMessageCircleQuestion size={30} />
          </motion.div>
        </motion.button>
      </div>

      {/* ================= DESKTOP SIDEBAR ================= */}
      <div className="fixed right-4 top-1/2 z-50 hidden -translate-y-1/2 flex-col gap-4 lg:flex">
        <SidebarAction
          href="https://wa.me/918080892888?text=Hi%2C%20I%20need%20help%20with%20RS%20Solutions%20Biomatric%20Devices."
          label="WhatsApp"
        >
          <IconBrandWhatsapp size={40} className="text-green-500" />
        </SidebarAction>

        <SidebarAction href="tel:8080892888" label="Call">
          <IconHeadset size={30} className="text-orange-500" />
        </SidebarAction>

        <SidebarAction href="/sales" label="Request Callback">
          <IconPhoneCall size={30} className="text-blue-600" />
        </SidebarAction>
      </div>
    </>
  );
}

/* ================= DESKTOP SIDEBAR ITEM ================= */

function SidebarAction({
  href,
  label,
  children,
}: {
  href: string;
  label: string;
  children: React.ReactNode;
}) {
  return (
    <motion.div whileHover={{ scale: 1.3 }}>
      <Link
        href={href}
        aria-label={label}
        className="group relative flex h-14 w-14 items-center justify-center
                   rounded-full bg-white shadow-lg border border-gray-300"
      >
        {children}
        <span
          className="pointer-events-none absolute right-full mr-3
                     rounded bg-black px-2 py-1 text-xs text-white
                     opacity-0 transition group-hover:opacity-100"
        >
          {label}
        </span>
      </Link>
    </motion.div>
  );
}
