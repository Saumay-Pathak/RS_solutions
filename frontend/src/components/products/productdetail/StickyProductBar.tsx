"use client";

import { useEffect, useState, type ReactElement } from "react";
import ProductEnquiryButton from "@/components/common/ProductEnquiryButton";

type Props = {
  title: string;
  categoryName?: string;
  catalogueDoc?: string;
  productTitle?: string;
  DownloadCatalogueButton?: (props: { productTitle: string; catalogueDoc: string }) => ReactElement;
};

export default function StickyProductBar({
  title,
  categoryName,
  catalogueDoc,
  productTitle,
  DownloadCatalogueButton,
}: Props) {
  const [showBar, setShowBar] = useState(false);

  useEffect(() => {
    const onScroll = () => {
      const y = window.scrollY || 0;
      setShowBar(y > 280);
    };
    onScroll();
    window.addEventListener("scroll", onScroll, { passive: true });
    return () => window.removeEventListener("scroll", onScroll);
  }, []);

  return (
    <div
      className={`fixed top-0 inset-x-0 z-[60] transition-transform duration-300 ease-out ${
        showBar ? "translate-y-0" : "-translate-y-full"
      }`}>
      <div className="bg-white/95 backdrop-blur supports-[backdrop-filter]:bg-white/80 border-b border-gray-200">
        <div className="max-w-7xl mx-auto px-6 py-3 flex items-center gap-4">
          <div className="flex-1 min-w-0">
            <div className="flex items-center gap-2">
              {categoryName && (
                <span className="inline-flex items-center text-orange-700 bg-orange-50 border border-orange-200 rounded-full px-2 py-0.5 text-[11px] font-medium">
                  {categoryName}
                </span>
              )}
              <h2 className="text-sm md:text-base font-semibold text-gray-900 truncate">
                {title}
              </h2>
            </div>
          </div>

          <div className="flex items-center gap-2">
            <ProductEnquiryButton />
            {catalogueDoc && productTitle && DownloadCatalogueButton && (
              <DownloadCatalogueButton
                productTitle={productTitle}
                catalogueDoc={catalogueDoc}
              />
            )}
          </div>
        </div>
      </div>
    </div>
  );
}