"use client";

import axiosClient from "@/services/axiosClient";
import { baseUri } from "@/services/constant";
import Image from "next/image";
import { useEffect, useState } from "react";

type PopupData = {
  title: string;
  type: "modal";
  content: string;
  image?: string;
  video_url?: string | null;
  button_text?: string | null;
  button_url?: string | null;
  position: "center" | "top" | "bottom";
  size: "small" | "medium" | "large";
  show_after: number;
  show_frequency?: "always" | "once_per_day";
  styles?: Record<string, string>;
};

const PopupModal = () => {
  const [popup, setPopup] = useState<PopupData | null>(null);
  const [visible, setVisible] = useState(false);

  useEffect(() => {
    const fetchPopup = async () => {
      try {
        const res = await axiosClient.get("/content/popups");
        const json = res.data;

        if (json.success && json.data?.length > 0) {
          const popupData = json.data[0];

          // Show only once per day if configured
          if (popupData.show_frequency === "once_per_day") {
            const lastShown = localStorage.getItem("popupLastShown");
            const today = new Date().toDateString();
            if (lastShown === today) return; // already shown today
            localStorage.setItem("popupLastShown", today);
          }

          setPopup(popupData);

          const delay = (popupData.show_after || 10) * 1000;
          setTimeout(() => setVisible(true), delay);
        }
      } catch (error) {
        console.error("Error fetching popup:", error);
      }
    };

    fetchPopup();
  }, []);

  if (!popup || !visible) return null;

  const handleClose = () => setVisible(false);

  const modalSize =
    popup.size === "small"
      ? "max-w-sm"
      : popup.size === "medium"
      ? "max-w-md"
      : "max-w-2xl";

  const positionClass =
    popup.position === "top"
      ? "items-start mt-10"
      : popup.position === "bottom"
      ? "items-end mb-10"
      : "items-center";

  return (
    <div className="fixed inset-0 z-[100] flex justify-center bg-black/25 bg-opacity-50 backdrop-blur-sm transition-opacity duration-300">
      <div
        className={`flex ${positionClass} justify-center w-full`}
        onClick={handleClose}>
        <div
          onClick={(e) => e.stopPropagation()}
          className={`relative ${modalSize} bg-white rounded-xl shadow-lg p-6 text-center`}
          style={{
            backgroundColor: popup.styles?.["background-color"],
            color: popup.styles?.color,
            borderColor: popup.styles?.["border-color"],
            borderRadius: popup.styles?.["border-radius"],
            borderWidth: popup.styles?.["border-color"] ? "1px" : "0px",
          }}>
          {/* Close Button */}
          <button
            onClick={handleClose}
            className="absolute top-2 right-2 text-gray-500 hover:text-black">
            ✕
          </button>

          {/* Image */}
          {popup.image && (
            <div className="mb-4 flex justify-center">
              <Image
                src={`${baseUri}${popup.image}`}
                alt="Popup"
                className="rounded-md max-h-48 object-contain"
                width={300}
                height={200}
              />
            </div>
          )}

          {/* Title */}
          <h2 className="text-xl font-bold mb-2">{popup.title}</h2>

          {/* Content */}
          <p className="text-sm mb-4">{popup.content}</p>

          {/* Button */}
          {popup.button_text && (
            <a
              href={popup.button_url || "#"}
              target="_blank"
              rel="noopener noreferrer"
              className="inline-block bg-orange-500 text-white px-6 py-2 rounded-md hover:bg-orange-600 transition-all">
              {popup.button_text}
            </a>
          )}
        </div>
      </div>
    </div>
  );
};

export default PopupModal;
