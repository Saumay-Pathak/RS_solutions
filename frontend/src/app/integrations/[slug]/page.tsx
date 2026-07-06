import Layout from "@/components/layout/Layout";
import AdvancedBreadcrumb from "@/components/common/Bredacrumb";
import Image from "next/image";
import Link from "next/link";
import { baseUri } from "@/services/constant";
import type { IntegrationModule } from "@/services/integrationService";

type Props = { params: { slug: string } };

export async function generateMetadata({ params }: Props) {
  const { slug } = await params;
  return {
    title: `Integration - ${slug}`,
    description: "Integration module API documentation",
  };
}

export default async function IntegrationDetailPage({ params }: Props) {
  const { slug } = await params;
  const { getIntegrationModuleBySlug, sanitizeUrl } = await import(
    "@/services/integrationService"
  );
  let integration: IntegrationModule | null = null;
  try {
    const res = await getIntegrationModuleBySlug(slug);
    integration = res?.data as IntegrationModule;
  } catch {
    integration = null;
  }

  const breadcrumbItems = [
    { label: "Home", href: "/" },
    { label: "3rd Party Software Integration", href: "/integrations" },
    { label: integration?.title || slug, href: `/integrations/${slug}` },
  ];

  const cover = integration?.cover_image
    ? `${baseUri}${integration.cover_image}`
    : "/images/image.png";
  const keyFeatures = integration?.key_features ?? [];
  const apiFeatures = integration?.api_features ?? [];
  const apis = integration?.apis ?? [];
  const demo = integration?.demo_credentials ?? null;
  const relatedSlug = integration?.slug ?? "";
  const downloadUrl = sanitizeUrl(integration?.download_url ?? null);

  return (
    <Layout>
      <AdvancedBreadcrumb items={breadcrumbItems} />
      <section className="bg-white py-8 md:py-12">
        <div className="container mx-auto px-4">
          {/* Header */}
          <div className="grid md:grid-cols-3 gap-6 mb-6">
            <div className="md:col-span-2">
              <h1 className="text-2xl md:text-3xl font-semibold text-[#1E1410]">
                {integration?.title || "Integration"}
              </h1>
              {integration?.description && (
                <p className="text-gray-700 mt-3">{integration.description}</p>
              )}
              <div className="flex flex-wrap gap-2 mt-3">
                {integration?.production_base_url && (
                  <span className="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-xs">
                    Prod: {sanitizeUrl(integration.production_base_url)}
                  </span>
                )}
                {integration?.staging_base_url && (
                  <span className="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-xs">
                    Staging: {sanitizeUrl(integration.staging_base_url)}
                  </span>
                )}
              </div>
              {downloadUrl && (
                <div className="mt-4">
                  <a
                    href={downloadUrl}
                    target="_blank"
                    rel="noopener noreferrer"
                    download
                    className="inline-flex items-center rounded-lg bg-orange-600 text-white px-3 py-2 text-sm hover:bg-orange-700"
                  >
                    Download
                  </a>
                </div>
              )}
            </div>
            <div>
              <div className="relative aspect-[16/9] rounded-lg overflow-hidden border border-gray-200 bg-gray-50">
                <Image
                  src={cover}
                  alt={integration?.title || "Integration"}
                  width={800}
                  height={450}
                  unoptimized
                  className="object-cover w-full h-full"
                />
              </div>
            </div>
          </div>

          {/* Key Features */}
          {keyFeatures.length > 0 && (
            <section className="mb-6">
              <h2 className="text-xl font-semibold text-[#1E1410] mb-2">
                Key Features
              </h2>
              <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">
                {keyFeatures.map((f, i) => (
                  <div
                    key={i}
                    className="rounded-xl border border-gray-200 bg-white p-3"
                  >
                    <p className="text-gray-800 text-sm">{f}</p>
                  </div>
                ))}
              </div>
            </section>
          )}

          {/* API Features */}
          {apiFeatures.length > 0 && (
            <section className="mb-6">
              <h2 className="text-xl font-semibold text-[#1E1410] mb-2">
                API Features
              </h2>
              <ul className="grid grid-cols-1 md:grid-cols-2 gap-2">
                {apiFeatures.map((f, i) => (
                  <li
                    key={i}
                    className="rounded-xl border border-gray-200 bg-white p-3 text-gray-800 text-sm"
                  >
                    {f}
                  </li>
                ))}
              </ul>
            </section>
          )}

          {/* Demo Credentials */}
          {demo != null && (
            <section className="mb-6">
              <h2 className="text-xl font-semibold text-[#1E1410] mb-2">
                Demo Credentials
              </h2>
              <div className="rounded-xl border border-gray-200 bg-white p-4">
                {demo.username && (
                  <p className="text-sm text-gray-800">
                    <span className="font-medium">Username:</span>{" "}
                    {demo.username}
                  </p>
                )}
                {demo.password && (
                  <p className="text-sm text-gray-800">
                    <span className="font-medium">Password:</span>{" "}
                    {demo.password}
                  </p>
                )}
                {demo.notes && (
                  <p className="text-sm text-gray-600 mt-2">{demo.notes}</p>
                )}
              </div>
            </section>
          )}

          {/* API Endpoints */}
          {apis.length > 0 && (
            <section className="mb-8">
              <h2 className="text-xl font-semibold text-[#1E1410] mb-2">
                APIs
              </h2>
              <div className="space-y-4">
                {apis.map((api, i) => (
                  <div
                    key={i}
                    className="rounded-xl border border-gray-200 bg-white p-4"
                  >
                    <div className="flex flex-wrap items-center gap-2 mb-2">
                      <span className="px-2 py-1 rounded bg-gray-100 text-gray-700 text-xs">
                        {api.method}
                      </span>
                      {api.type && (
                        <span className="px-2 py-1 rounded bg-gray-100 text-gray-700 text-xs">
                          {api.type}
                        </span>
                      )}
                      <span className="text-sm font-medium text-[#1E1410]">
                        {api.name}
                      </span>
                    </div>
                    {api.description && (
                      <p className="text-sm text-gray-600 mb-2">
                        {api.description}
                      </p>
                    )}
                    {api.base_url && (
                      <p className="text-sm">
                        <span className="font-medium">URL:</span>{" "}
                        {sanitizeUrl(api.base_url)}
                      </p>
                    )}
                    {!!api.headers && (
                      <div className="mt-2">
                        <p className="text-sm font-medium">Headers</p>
                        <pre className="mt-1 bg-gray-50 border border-gray-200 rounded p-3 text-xs overflow-x-auto">
                          {JSON.stringify(api.headers, null, 2)}
                        </pre>
                      </div>
                    )}
                    {api.body != null && (
                      <div className="mt-2">
                        <p className="text-sm font-medium">Body</p>
                        <pre className="mt-1 bg-gray-50 border border-gray-200 rounded p-3 text-xs overflow-x-auto">
                          {JSON.stringify(api.body, null, 2)}
                        </pre>
                      </div>
                    )}
                  </div>
                ))}
              </div>
            </section>
          )}

          <div className="flex items-center justify-between">
            <Link
              href="/integrations"
              className="text-sm text-orange-600 hover:text-orange-700"
            >
              ← Back to integrations
            </Link>
            {relatedSlug && (
              <Link
                href={`/solutions/${relatedSlug}`}
                className="text-sm text-gray-600 hover:text-gray-800 hidden"
              >
                Related
              </Link>
            )}
          </div>
        </div>
      </section>
    </Layout>
  );
}
