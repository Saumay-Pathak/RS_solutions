"use client";

import { useState } from "react";
import { ArrowDownToLine } from "lucide-react";
import CTAButton from "@/components/common/CTAButton";
import Swal from "sweetalert2";

export default function DownloadCatalogueButton({
  productTitle,
  catalogueDoc,
}: {
  productTitle: string;
  catalogueDoc?: string;
}) {
  const [isDownloading, setIsDownloading] = useState(false);

  const handleDownload = async () => {
    if (!catalogueDoc) {
      Swal.fire({
        icon: "warning",
        title: "No Catalogue Found",
        text: "Catalogue is not available for this product.",
      });
      return;
    }

    try {
      setIsDownloading(true);

      // Use same-origin proxy route to force direct download
      const apiUrl = `/api/catalogue?doc=${encodeURIComponent(
        catalogueDoc.replace(/^\/+/, "")
      )}&title=${encodeURIComponent(productTitle)}`;

      // Navigating to the API route triggers browser download via Content-Disposition
      window.location.href = apiUrl;
    } catch (err) {
      console.error(err);
      Swal.fire({
        icon: "error",
        title: "Download Failed",
        text: "Something went wrong while downloading the catalogue. Please try again.",
      });
    } finally {
      setIsDownloading(false);
    }
  };

  return (
    <CTAButton
      variant="yellow"
      onClick={handleDownload}
      // disabled={isDownloading || !catalogueDoc}
    >
      <span>
        <ArrowDownToLine className="w-[16px]" />
      </span>{" "}
      {isDownloading ? "DOWNLOADING..." : "DOWNLOAD CATALOGUE"}
    </CTAButton>
  );
}
