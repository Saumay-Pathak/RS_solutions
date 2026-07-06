"use client";

import React from "react";
import Image from "next/image";
import { motion, Variants } from "framer-motion";
import { baseUri } from "@/services/constant";

type Product = {
  image?: string | null;
  name?: string | null;
};

interface CardProps {
  it?: Product;
}

/* ----------------------------------------
   Motion variants
---------------------------------------- */
const cardVariants: Variants = {
  hidden: { opacity: 0, y: 20 },
  visible: {
    opacity: 1,
    y: 0,
    transition: { duration: 0.35, ease: "easeOut" },
  },
};

const Card: React.FC<CardProps> = ({ it }) => {
  return (
    <motion.div
      variants={cardVariants}
      initial="hidden"
      animate="visible"
      whileHover={{ y: -6 }}
      whileTap={{ scale: 0.98 }}
      className="
        group mx-1 md:mx-3 rounded-xl bg-white overflow-hidden
        border border-gray-200
        transition-all duration-300
        hover:shadow-xl hover:border-orange-400
      "
    >
      {/* Image Section */}
      <div className="relative bg-gray-100 h-56 md:h-72 flex items-center justify-center p-6">
        {it?.image ? (
          <motion.div
            whileHover={{ scale: 1.06 }}
            className="relative h-full w-full flex items-center justify-center"
          >
            <Image
              src={`${baseUri}${it.image}`}
              alt={it?.name ?? "Product image"}
              fill
              unoptimized
              className="
                object-contain rounded-xl
                mix-blend-multiply
                transition-transform duration-300
              "
            />
          </motion.div>
        ) : (
          <div className="text-gray-400 text-xs">No image</div>
        )}

        {/* Quick View Icon */}
        <span
          className="
            absolute top-3 right-3 h-9 w-9 rounded-full
            bg-white text-gray-700
            border border-gray-200
            grid place-items-center shadow-sm
            opacity-0 group-hover:opacity-100
            transition-all duration-300
            group-hover:text-orange-600
            group-hover:border-orange-400
            group-hover:bg-orange-50
          "
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 24 24"
            fill="currentColor"
            className="h-5 w-5"
          >
            <path d="M12 5c-7 0-10 7-10 7s3 7 10 7 10-7 10-7-3-7-10-7zm0 11a4 4 0 1 1 0-8 4 4 0 0 1 0 8z" />
          </svg>
        </span>
      </div>

      {/* Divider */}
      <div className="h-px w-full bg-gray-200" />

      {/* Content */}
      <div className="p-4 md:p-6">
        <h3 className="text-base md:text-lg font-semibold text-[#1E1410] mb-3 line-clamp-1">
          {it?.name}
        </h3>

        {/* CTA */}
        <motion.button
          whileHover={{ scale: 1.02 }}
          whileTap={{ scale: 0.96 }}
          className="
            w-full inline-flex items-center justify-center
            bg-orange-500 text-white rounded-lg
            py-2.5 text-sm font-medium
            transition-colors duration-300
            hover:bg-orange-600
            focus:outline-none focus:ring-2 focus:ring-orange-400
          "
        >
          Read More
          <svg
            xmlns="http://www.w3.org/2000/svg"
            className="h-4 w-4 ml-2"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              strokeLinecap="round"
              strokeLinejoin="round"
              strokeWidth={2}
              d="M14 5l7 7m0 0l-7 7m7-7H3"
            />
          </svg>
        </motion.button>
      </div>
    </motion.div>
  );
};

export default Card;
