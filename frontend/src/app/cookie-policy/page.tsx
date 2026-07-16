import { Metadata } from "next";
import DOMPurify from "./dompurifire";
import { getCookiePolicyData } from "@/services/cookiePolicyService";
import Layout from "@/components/layout/Layout";

export const metadata: Metadata = {
  title: "Cookie Policy",
};

export const dynamic = "force-dynamic";

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
    // If decode fails, fallback to the cleaned raw HTML string
    return raw
      .replace(/\\u003C/g, "<")
      .replace(/\\u003E/g, ">")
      .replace(/\\u0026/g, "&")
      .replace(/\\n/g, "")
      .replace(/\\"/g, '"')
      .replace(/\\\\/g, "\\")
      .replace(/\r/g, "");
  }
}

const CookiePolicyPage = async () => {
  let decodedContent = "";

  try {
    const res = await getCookiePolicyData();
    if (res.success && res.data) {
      decodedContent = safeDecodeContent(res.data.content);
    } else {
      decodedContent = "<p>No content found.</p>";
    }
  } catch (error) {
    console.error("Failed to fetch cookie policy data:", error);
    decodedContent = "<p>Error loading content. Please try again later.</p>";
  }

  const sanitizedContent = DOMPurify.sanitize(decodedContent);

  return (
    <Layout>
      <section className="bg-gray-50 py-12">
        <div className="max-w-5xl mx-auto bg-white shadow-md rounded-xl px-6 md:px-12 lg:px-28 py-10">
          <h1 className="text-2xl sm:text-3xl font-bold section-title text-gray-900 text-center mb-8">
            Cookies Policy
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
};

export default CookiePolicyPage;
