"use client";

import { useState } from "react";
import SendRequirementModal from "./SendRequirementModal";

export default function ProductEnquiryButton({
  productName,
}: {
  productName?: string;
}) {
  const [open, setOpen] = useState(false);

  return (
    <>
      <button
        onClick={() => setOpen(true)}
        className="bg-[#EA5921] text-white hover:bg-orange-600 items-center gap-2 text-sm px-2 lg:px-4 py-2 rounded-[5px] shadow-sm transition cursor-pointer hover:bg-orange-700"
      >
        Enquire Now
      </button>

      <SendRequirementModal
        isOpen={open}
        onClose={() => setOpen(false)}
        productName={productName}
      />
    </>
  );
}
