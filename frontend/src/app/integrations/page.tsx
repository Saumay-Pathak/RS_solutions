import Layout from "@/components/layout/Layout";
import AdvancedBreadcrumb from "@/components/common/Bredacrumb";
import Link from "next/link";
import Image from "next/image";
import {
  getIntegrationModules,
  type IntegrationModule,
} from "@/services/integrationService";
import { baseUri } from "@/services/constant";

export const metadata = {
  title: "3rd Party Software Integration",
  description: "Explore integration modules like Slack, Stripe, and more.",
};

export default async function IntegrationsPage() {
  let modules: IntegrationModule[] = [];

  try {
    const res = await getIntegrationModules();
    modules = Array.isArray(res?.data)
      ? res.data.sort((a, b) => (a.sort_order ?? 0) - (b.sort_order ?? 0))
      : [];
  } catch {
    modules = [];
  }

  const breadcrumbItems = [
    { label: "Home", href: "/" },
    { label: "3rd Party Software Integration", href: "/integrations" },
  ];

  return (
    <Layout>
      <AdvancedBreadcrumb items={breadcrumbItems} />

      {/* ---------- HEADER ---------- */}
      <section className="bg-white pt-6 pb-10">
        <div className="max-w-7xl mx-auto px-4 text-center">
          <h1 className="text-2xl sm:text-3xl font-bold text-[#1E1410]">
            3rd Party Software Integration
          </h1>
          <p className="mt-3 text-sm md:text-base text-gray-600 max-w-2xl mx-auto">
            Browse available integration modules and explore detailed API
            documentation to seamlessly connect with your existing tools.
          </p>
        </div>
      </section>

      {/* ---------- CONTENT ---------- */}
      <section>
        <div className="max-w-7xl mx-auto px-4 pb-10">
          {modules.length === 0 ? (
            <div className="max-w-xl mx-auto mt-10 rounded-2xl border border-gray-200 bg-white p-8 text-center shadow-sm">
              <p className="text-gray-600">
                No integration modules are available at the moment.
              </p>
            </div>
          ) : (
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
              {modules.map((m) => {
                const img = m.cover_image
                  ? `${baseUri}${m.cover_image}`
                  : "/images/image.png";

                return (
                  <Link
                    key={m.id}
                    href={`/integrations/${m.slug}`}
                    className="group block rounded-2xl border border-gray-200 bg-white overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300"
                  >
                    {/* Image */}
                    <div className="relative aspect-[16/9] overflow-hidden bg-gray-100">
                      <Image
                        src={img}
                        alt={m.title}
                        width={800}
                        height={450}
                        unoptimized
                        className="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105"
                      />
                      <div className="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors" />
                    </div>

                    {/* Content */}
                    <div className="p-6">
                      <h3 className="text-lg md:text-xl font-semibold text-[#1E1410]">
                        {m.title}
                      </h3>

                      {m.description && (
                        <p className="mt-2 text-sm text-gray-600 line-clamp-3">
                          {m.description}
                        </p>
                      )}

                      <div className="mt-4 inline-flex items-center text-sm font-medium text-orange-600">
                        View API details
                        <span className="ml-1 transition-transform group-hover:translate-x-1">
                          →
                        </span>
                      </div>
                    </div>
                  </Link>
                );
              })}
            </div>
          )}
        </div>
      </section>
    </Layout>
  );
}
