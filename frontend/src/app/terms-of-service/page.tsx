import { getTermsData } from "@/services/termsService";
import { Metadata } from "next";
import DOMPurify from "./dompurifire";
import Layout from "@/components/layout/Layout";

// ✅ SEO Metadata
export const metadata: Metadata = {
  title: "Terms of Service | RS Solutions",
  description:
    "Read RS Solutions' terms of service for using our solutions and services.",
};

export const dynamic = "force-dynamic";

// ✅ Fetch terms data (server-side)
async function getTermsOfService() {
  try {
    const res = await getTermsData();
    return res.data;
  } catch (error) {
    console.error("Terms of Service Fetch Error:", error);
    return null;
  }
}

// ✅ Safe decode function (same logic as disclaimer/privacy)
function safeDecodeContent(raw: string = ""): string {
  try {
    if (!raw) return "";
    const cleaned = raw
      .replace(/\\u003C/g, "<")
      .replace(/\\u003E/g, ">")
      .replace(/\\u0026/g, "&")
      .replace(/\\n/g, "")
      .replace(/\\"/g, '"')
      .replace(/\\\\/g, "\\")
      .replace(/\r/g, "");
    return decodeURIComponent(cleaned);
  } catch (err) {
    console.error("Decode Error:", err);
    return raw;
  }
}

// ✅ Page component
export default async function TermsOfServicePage() {
  const terms = await getTermsOfService();

  if (!terms) {
    return (
      <section className="min-h-screen flex flex-col items-center justify-center bg-gray-50 text-center px-6">
        <h1 className="text-3xl font-bold text-gray-800 mb-3">
          Terms of Service
        </h1>
        <p className="text-gray-500">
          Unable to load content. Please try again later.
        </p>
      </section>
    );
  }

  let decodedContent = safeDecodeContent(terms.content);
  if (decodedContent) {
    decodedContent = decodedContent
      .replace(/R S Solutions\s*-\s*Realtime Biometrics/gi, "RS Solutions")
      .replace(/Realtime\s*Biometrics/gi, "RS Solutions")
      .replace(/RealtimeBiometrics/gi, "RS Solutions")
      .replace(/R\s*S\s*Solutions/gi, "RS Solutions");
  }
  const sanitizedContent = DOMPurify.sanitize(decodedContent || "");

  return (
    <Layout>
      <section className="bg-gray-50 py-12">
        <div className="max-w-5xl mx-auto bg-white shadow-md rounded-xl px-6 md:px-12 lg:px-28 py-10">
          <h1 className="text-2xl sm:text-3xl font-bold section-title text-gray-900 text-center mb-8">
            {terms.title}
          </h1>

          <article
            className="prose prose-gray max-w-none text-gray-800 mt-6
            prose-h2:text-2xl prose-h2:font-semibold
            prose-h3:text-xl prose-h3:font-medium
            prose-a:text-blue-600 hover:prose-a:underline
            prose-strong:font-semibold
            prose-li:marker:text-gray-700
            prose-img:rounded-lg"
            suppressHydrationWarning
            dangerouslySetInnerHTML={{ __html: sanitizedContent }}
          />
        </div>
      </section>
    </Layout>
  );
}
