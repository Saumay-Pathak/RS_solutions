import { Metadata } from "next";
import DOMPurify from "./dompurifire";
import Layout from "@/components/layout/Layout";
import { getDisclaimerData } from "@/services/disclaimerService";

export const metadata: Metadata = {
  title: "Disclaimer",
};

const DisclaimerPage = async () => {
  let decodedContent = "";

  try {
    const res = await getDisclaimerData();
    if (res.success && res.data) {
      const encodedContent = res.data.content;
      decodedContent = decodeURIComponent(
        encodedContent
          .replace(/\"/g, '"')
          .replace(/\\/g, "")
          .replace(/\n/g, "")
          .replace(/\r/g, "")
      );
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
            Disclaimer
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

export default DisclaimerPage;
