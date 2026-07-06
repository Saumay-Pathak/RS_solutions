"use client";
import { useEffect, useState } from "react";
import Layout from "@/components/layout/Layout";
import { getFaqs, type FaqItem } from "@/services/faqService";

const FaqsPage = () => {
  const [faqs, setFaqs] = useState<FaqItem[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchData = async () => {
      try {
        setLoading(true);
        const res = await getFaqs(1);
        const items = Array.isArray(res?.data) ? res.data : [];
        // Only active FAQs, sort by sort_order asc
        const filtered = items
          .filter((f) => f.status)
          .sort((a, b) => (a.sort_order ?? 0) - (b.sort_order ?? 0));
        setFaqs(filtered);
      } catch (err) {
        console.error("Failed to load FAQs", err);
        setFaqs([]);
      } finally {
        setLoading(false);
      }
    };
    fetchData();
  }, []);

  return (
    <Layout>
      <main className="bg-white">
        <section className="max-w-5xl mx-auto my-10">
          <div className="text-center">
            <h1 className="section-title-long text-3xl font-bold">Frequently Asked Questions</h1>
            <p className="section-subtitle text-sm">Answers to common questions to help you quickly find what you need.</p>
          </div>

          {loading ? (
            <div className="mt-8">
              <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-orange-500"></div>
            </div>
          ) : faqs.length === 0 ? (
            <p className="mt-8 text-gray-700">No FAQs available at the moment.</p>
          ) : (
            <div className="mt-8 divide-y divide-gray-200 border border-gray-200 rounded-xl">
              {faqs.map((faq, idx) => (
                <details key={faq.id} className="group" open={idx===0}>
                  <summary className={`list-none px-5 py-4 flex items-center justify-between cursor-pointer"`}>
                    <span className="text-gray-900 font-medium">{faq.question}</span>
                    <span className="ml-4 flex items-center justify-center w-8 h-8 rounded-full border border-gray-300 text-gray-500 transition-all group-open:rotate-180 group-open:border-orange-400 group-open:text-orange-500">
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
                    {faq.answer}
                  </div>
                </details>
              ))}
            </div>
          )}
        </section>
      </main>
    </Layout>
  );
};

export default FaqsPage;