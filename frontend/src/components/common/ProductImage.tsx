"use client";

import Image from "next/image";
import React, { useState, MouseEvent } from "react";
import { X, Maximize2, ChevronLeft, ChevronRight } from "lucide-react";
import { baseUri } from "@/services/constant";
import Portal from "./Portal";

interface ProductImagesProps {
  images?: string[];
  alt?: string;
  className?: string;
}

const ProductImages: React.FC<ProductImagesProps> = ({
  images = ["/images/device.png"],
  alt = "product",
  className = "",
}) => {
  const [isOpen, setIsOpen] = useState<boolean>(false);
  const [currentIndex, setCurrentIndex] = useState<number>(0);

  const openFullView = (index: number) => {
    setCurrentIndex(index);
    setIsOpen(true);
  };

  const closeFullView = () => setIsOpen(false);

  const prevImage = (e: MouseEvent<HTMLButtonElement>) => {
    e.stopPropagation();
    setCurrentIndex((prev) => (prev === 0 ? images.length - 1 : prev - 1));
  };

  const nextImage = (e: MouseEvent<HTMLButtonElement>) => {
    e.stopPropagation();
    setCurrentIndex((prev) => (prev === images.length - 1 ? 0 : prev + 1));
  };

  // thumbnails limit (show only 4, rest in +count)
  const visibleThumbnails = images.slice(1, 4);
  const extraCount = images.length - 4;

  return (
    <div
      className={`p-6 rounded-xl bg-white shadow-sm ring-1 ring-gray-100 ${className}`}>
      {/* Main Image */}
      <div
        className="relative rounded-xl overflow-hidden cursor-pointer group"
        onClick={() => openFullView(0)}>
        <Image
          src={`${baseUri}${images[0]}`}
          alt={alt}
          width={700}
          height={480}
          className="w-full h-auto max-h-[420px] object-contain rounded-xl transition-transform duration-300 group-hover:scale-[1.02]"
        />
        <div className="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-all flex items-center justify-center">
          <Maximize2 className="text-white w-6 h-6" />
        </div>
      </div>

      {/* Thumbnail Row */}
      <div className="mt-4 flex gap-3 justify-start flex-wrap">
        {visibleThumbnails.map((img, index) => (
          <div
            key={index + 1}
            className="relative w-20 h-20 border-1 border-[#dbd6d6]  rounded-md overflow-hidden cursor-pointer group"
            onClick={() => openFullView(index + 1)}>
            <Image
              src={`${baseUri}${img}`}
              alt={alt}
              width={80}
              height={80}
              className="object-cover w-full h-full"
            />
            <div className="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-all flex items-center justify-center">
              <Maximize2 className="text-white w-4 h-4" />
            </div>
          </div>
        ))}

        {extraCount > 0 && (
          <div
            className="relative w-20 h-20 rounded-md bg-gray-200 flex items-center justify-center text-gray-700 font-semibold text-sm cursor-pointer hover:bg-gray-300 transition"
            onClick={() => openFullView(3)}>
            +{extraCount}
          </div>
        )}
      </div>

      {/* Full View Modal */}
      {isOpen && (
        <Portal>
          <div
            className="fixed top-0 inset-0 bg-black/90 flex items-center justify-center z-[1000]"
            onClick={closeFullView}>
            <button
              className="absolute top-6 right-6 text-white hover:text-gray-300 transition"
              onClick={closeFullView}>
              <X size={28} />
            </button>

            <button
              className="absolute left-8 text-white hover:text-gray-300"
              onClick={prevImage}>
              <ChevronLeft size={40} />
            </button>

            <div className="max-w-[100%] max-h-[85%] flex items-center justify-center">
              <Image
                src={`${baseUri}${images[currentIndex]}`}
                alt={alt}
                width={900}
                height={600}
                className="rounded-lg object-contain  h-auto max-h-[80vh]"
              />
            </div>

            <button
              className="absolute right-8 text-white hover:text-gray-300"
              onClick={nextImage}>
              <ChevronRight size={40} />
            </button>

            <div className="absolute bottom-6 text-gray-300 text-sm">
              {currentIndex + 1} / {images.length}
            </div>
          </div>
        </Portal>
      )}
    </div>
  );
};

export default ProductImages;
