// app/products/[title]/page.tsx
import { getProductBySlug } from "@/services/productService";
import Layout from "@/components/layout/Layout";
import ProductImage from "@/components/common/ProductImage";
import SpecsTable from "@/components/products/productdetail/SpecsTable";
import Link from "next/link";
import Image from "next/image";
import { baseUri } from "@/services/constant";
import {
  Fingerprint,
  KeyRound,
  CreditCard,
  Cloud,
  Activity,
  User,
  SunMedium,
} from "lucide-react";
import { IconAirTrafficControl, IconFaceId } from "@tabler/icons-react";
import { notFound } from "next/navigation";
import { ReactNode } from "react";
import ProductEnquiryButton from "@/components/common/ProductEnquiryButton";
import AdvancedBreadcrumb from "@/components/common/Bredacrumb";
import ReadMore from "@/components/common/Readmore";

// ---------------------------- TYPES ------------------------------
type Category = {
  id?: number | string;
  name?: string;
  slug?: string;
};

type ProductCategory = {
  a_plus_content_html: string;
  description: ReactNode;
  category: Category | null;
  title: string;
  images: string[];
  // Features can be either strings or objects coming from API with icon codes
  features: Array<string | { title: string; icon?: string }>;
  specifications: Record<string, string | number>;
  meta_title?: string;
  meta_description?: string;
  catalogue_document?: string;
  datasheet_document?: string;
  connection_diagram_document?: string;
  user_manual_document?: string;
  faqs?: Array<{ question?: string; answer?: string }> | string | null;
};

// ----------------------- METADATA GENERATION ----------------------
export async function generateMetadata({
  params,
}: {
  params: Promise<{ title: string }>;
}) {
  const { title: slug } = await params;

  const res = await getProductBySlug(slug);
  const product: ProductCategory | null = res?.data?.[0] || null;

  if (!product) {
    return {
      title: "Product Not Found",
      description: "Product not found in our catalog.",
    };
  }

  return {
    title: product.meta_title || product.title,
    description: product.meta_description || product.title,
  };
}

// ----------------------------- PAGE -------------------------------
export default async function ProductPage({
  params,
}: {
  params: Promise<{ title: string }>;
}) {
  const { title: slug } = await params;

  const res = await getProductBySlug(slug);
  const product: ProductCategory | null = res?.data?.[0] || null;

  if (!product) return notFound();

  const breadcrumbItems = [
    { label: "Home", href: "/" },
    { label: "Products", href: "/products" },
    {
      label: product?.category?.name ?? "Category",
      href: "/products/category/" + product?.category?.slug,
    },
    { label: product?.title, href: "#" },
  ];

  return (
    <Layout>
      <AdvancedBreadcrumb items={breadcrumbItems} />
      <div className="bg-white">
        <div className="max-w-7xl mx-auto px-6 py-8">
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-10 items-start">
            <div className="lg:sticky lg:top-24">
              <div className="rounded-xl border border-white bg-gray-50 p-3">
                <ProductImage
                  images={product.images || []}
                  alt={product.title}
                />
              </div>
            </div>
            <div className="space-y-5">
              <span className="px-3 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-800 ring-1 ring-orange-200">
                {product?.category?.name}
              </span>

              <h1 className="text-2xl sm:text-3xl font-bold text-gray-900 mt-4">
                {product?.title}
              </h1>
              <div className="flex flex-wrap items-center gap-3 mt-4">
                <ProductEnquiryButton productName={product.title} />
                <Link
                  href="https://wa.me/918080892888"
                  target="_blank"
                  className="inline-flex items-center gap-2 text-sm px-2 lg:px-4 py-2 rounded-[5px] font-medium shadow hover:bg-green-600 bg-green-500 text-white transition"
                >
                  <Image
                    src="/watsapp.png"
                    alt="WhatsApp"
                    width={20}
                    height={20}
                    className="object-contain"
                  />
                  Enquire on WhatsApp
                </Link>
              </div>

              <ReadMore
                text={
                  typeof product?.description === "string"
                    ? product.description
                    : undefined
                }
              />
              {product.features?.length > 0 && (
                <div>
                  <h3 className="text-gray-900 font-semibold mb-3">Features</h3>
                  <div className="grid grid-cols-2 md:grid-cols-4 gap-4 mt-1">
                    {product.features.map((feature, idx) => {
                      const { title: featureTitle, icon: featureIcon } =
                        typeof feature === "string"
                          ? { title: feature, icon: undefined }
                          : feature;
                      const iconNode = featureIcon
                        ? getFeatureIconFromCode(featureIcon) ||
                          getFeatureIconFromLabel(featureTitle)
                        : getFeatureIconFromLabel(featureTitle);
                      return (
                        <div
                          key={idx}
                          className="flex flex-col items-center gap-2 p-4 rounded-lg bg-gray-50 hover:bg-orange-50 transition cursor-pointer border border-gray-200"
                        >
                          <div className="w-12 h-12 flex items-center justify-center rounded-md bg-orange-50 text-orange-600">
                            {iconNode}
                          </div>
                          <span className="text-xs font-light text-gray-800 text-center">
                            {featureTitle}
                          </span>
                        </div>
                      );
                    })}
                  </div>
                </div>
              )}
              {(product.catalogue_document ||
                product.datasheet_document ||
                product.connection_diagram_document ||
                product.user_manual_document) && (
                <div>
                  <h3 className="text-gray-900 font-semibold mb-3">Download</h3>
                  <div className="grid grid-cols-3 md:grid-cols-4 gap-3">
                    {product.catalogue_document && (
                      <a
                        href={
                          (product.catalogue_document?.startsWith("http")
                            ? product.catalogue_document
                            : `${baseUri}${product.catalogue_document}`) as string
                        }
                        target="_blank"
                        rel="noopener noreferrer"
                        className="rounded-lg border border-gray-200 bg-white p-5 flex flex-col items-center gap-3"
                      >
                        <svg
                          xmlns="http://www.w3.org/2000/svg"
                          viewBox="0 0 24 24"
                          fill="currentColor"
                          className="w-10 h-10 text-orange-600"
                        >
                          <path d="M12 3a1 1 0 011 1v9.586l2.293-2.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L11 13.586V4a1 1 0 011-1z" />
                          <path d="M5 20a1 1 0 001 1h12a1 1 0 001-1v-3a1 1 0 112 0v3a3 3 0 01-3 3H6a3 3 0 01-3-3v-3a1 1 0 112 0v3z" />
                        </svg>
                        <span className="text-sm font-medium text-gray-800">
                          Catalogue
                        </span>
                      </a>
                    )}
                    {product.datasheet_document && (
                      <a
                        href={
                          (product.datasheet_document?.startsWith("http")
                            ? product.datasheet_document
                            : `${baseUri}${product.datasheet_document}`) as string
                        }
                        target="_blank"
                        rel="noopener noreferrer"
                        className="rounded-lg border border-gray-200 bg-white p-5 flex flex-col items-center gap-3"
                      >
                        <svg
                          xmlns="http://www.w3.org/2000/svg"
                          viewBox="0 0 24 24"
                          fill="currentColor"
                          className="w-10 h-10 text-orange-600"
                        >
                          <path d="M12 3a1 1 0 011 1v9.586l2.293-2.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L11 13.586V4a1 1 0 011-1z" />
                          <path d="M5 20a1 1 0 001 1h12a1 1 0 001-1v-3a1 1 0 112 0v3a3 3 0 01-3 3H6a3 3 0 01-3-3v-3a1 1 0 112 0v3z" />
                        </svg>
                        <span className="text-sm font-medium text-gray-800">
                          Datasheet
                        </span>
                      </a>
                    )}
                    {product.connection_diagram_document && (
                      <a
                        href={
                          (product.connection_diagram_document?.startsWith(
                            "http"
                          )
                            ? product.connection_diagram_document
                            : `${baseUri}${product.connection_diagram_document}`) as string
                        }
                        target="_blank"
                        rel="noopener noreferrer"
                        className="rounded-lg border border-gray-200 bg-white p-5 flex flex-col items-center gap-3"
                      >
                        <svg
                          xmlns="http://www.w3.org/2000/svg"
                          viewBox="0 0 24 24"
                          fill="none"
                          stroke="currentColor"
                          strokeWidth="2"
                          className="w-10 h-10 text-orange-600"
                        >
                          <circle cx="6" cy="6" r="2" />
                          <circle cx="18" cy="6" r="2" />
                          <circle cx="6" cy="18" r="2" />
                          <circle cx="18" cy="18" r="2" />
                          <path d="M8 6h8M6 8v8M18 8v8M8 18h8" />
                        </svg>
                        <span className="text-sm font-medium text-gray-800">
                          Connection Diagram
                        </span>
                      </a>
                    )}
                    {product.user_manual_document && (
                      <a
                        href={
                          (product.user_manual_document?.startsWith("http")
                            ? product.user_manual_document
                            : `${baseUri}${product.user_manual_document}`) as string
                        }
                        target="_blank"
                        rel="noopener noreferrer"
                        className="rounded-lg border border-gray-200 bg-white p-5 flex flex-col items-center gap-3"
                      >
                        <svg
                          xmlns="http://www.w3.org/2000/svg"
                          viewBox="0 0 24 24"
                          fill="currentColor"
                          className="w-10 h-10 text-orange-600"
                        >
                          <path d="M6 4a2 2 0 00-2 2v12a2 2 0 002 2h9a3 3 0 003-3V6a2 2 0 00-2-2H6zm0 2h10v11a1 1 0 01-1 1H6V6z" />
                          <path d="M8 8h6v2H8V8zm0 4h6v2H8v-2z" />
                        </svg>
                        <span className="text-sm font-medium text-gray-800">
                          User Manual
                        </span>
                      </a>
                    )}
                  </div>
                </div>
              )}
            </div>
          </div>
          {product.specifications &&
            Object.keys(product.specifications).length > 0 && (
              <div className="mt-8">
                <h3 className="text-2xl font-semibold text-gray-900 mb-4">
                  Product Specifications
                </h3>
                <div className="rounded-xl">
                  <SpecsTable specs={product.specifications} />
                </div>
              </div>
            )}

          {(() => {
            const faqList = Array.isArray(product?.faqs) ? product?.faqs : [];
            const hasFaqs = faqList && faqList.length > 0;
            if (!hasFaqs) return null;
            return (
              <div className="mt-8">
                <h3 className="text-2xl font-semibold text-gray-900 mb-4">
                  FAQs
                </h3>
                <div className="divide-y divide-gray-200 border border-gray-200 rounded-xl">
                  {faqList.map((faq, idx) => (
                    <details key={idx} className="group" open={idx === 0}>
                      <summary
                        className={`list-none px-5 py-4 flex items-center justify-between cursor-pointer hover:text-orange-600 transition-colors"}`}
                      >
                        <span className="text-gray-900 font-medium">
                          {faq.question || `Question ${idx + 1}`}
                        </span>
                        <span className="ml-4 flex items-center justify-center w-6 h-6 md:w-8 md:h-8 text-gray-500 transition-all group-open:rotate-180 group-open:text-orange-500">
                          <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            strokeWidth="2"
                            strokeLinecap="round"
                            strokeLinejoin="round"
                            className="w-4 h-4"
                          >
                            <path d="M6 9l6 6 6-6" />
                          </svg>
                        </span>
                      </summary>
                      <div className="px-5 pb-5 text-gray-700 leading-relaxed text-sm md:text-base">
                        {(faq.answer || "").trim()}
                      </div>
                    </details>
                  ))}
                </div>
              </div>
            );
          })()}
        </div>
      </div>
    </Layout>
  );
}

// ------------------------ ICON MAPPERS -------------------------
function getFeatureIconFromLabel(label: string) {
  const l = (label || "").toLowerCase();
  if (l.includes("fingerprint"))
    return <Fingerprint className="w-6 h-6 text-orange-600" />;
  if (l.includes("password") || l.includes("passcode"))
    return <KeyRound className="w-6 h-6 text-orange-600" />;
  if (l.includes("card"))
    return <CreditCard className="w-6 h-6 text-orange-600" />;
  if (l.includes("cloud")) return <Cloud className="w-6 h-6 text-orange-600" />;
  if (l.includes("live") || l.includes("detect"))
    return <Activity className="w-6 h-6 text-orange-600" />;
  if (l.includes("wdr") || l.includes("hdr"))
    return <SunMedium className="w-6 h-6 text-orange-600" />;
  if (l.includes("face")) return <User className="w-6 h-6 text-orange-600" />;
  return <Activity className="w-6 h-6 text-orange-600" />;
}

// Prefer API-provided icon codes when available
function getFeatureIconFromCode(code?: string) {
  const c = (code || "").toLowerCase();
  switch (c) {
    case "tabler-face-id":
      return <IconFaceId className="w-6 h-6 text-orange-600" />;
    case "tabler-air-traffic-control":
      return <IconAirTrafficControl className="w-6 h-6 text-orange-600" />;
    default:
      return null;
  }
}
