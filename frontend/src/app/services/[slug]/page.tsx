import { Metadata } from "next";
import { cache } from "react";
import Layout from "@/components/layout/Layout";
import AdvancedBreadcrumb from "@/components/common/Bredacrumb";
import ServiceDetailClient from "@/components/sections/ServiceDetailClient";
import { getServiceBySlug } from "@/services/serviceService";

type PageProps = {
  params: Promise<{ slug: string }>;
};

export const metadata: Metadata = {
  title: "Service Details | Realtime Biometrics",
  description: "Explore detailed information about our services.",
};

const getServiceData = cache(async (slug: string) => {
  if (!slug || slug === "undefined") {
    return { service: null, error: "Invalid service slug" };
  }
  return await getServiceBySlug(slug);
});

export default async function ServiceDetailPage({ params }: PageProps) {
  const { slug } = await params;

  const { service, error } = await getServiceData(slug);
  const breadcrumbItems = [
    { label: "Home", href: "/" },
    { label: "Services", href: "/services" },
    { label: service?.title || slug },
  ];

  return (
    <Layout>
      <AdvancedBreadcrumb items={breadcrumbItems} />
      <ServiceDetailClient service={service} error={error} />
    </Layout>
  );
}