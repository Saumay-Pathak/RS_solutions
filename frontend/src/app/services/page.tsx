import { Metadata } from "next";
import AdvancedBreadcrumb from "@/components/common/Bredacrumb";
import Layout from "@/components/layout/Layout";
import ServicesSections from "@/components/sections/ServicesSections";

export const metadata: Metadata = {
  title: "Services | Realtime Biometrics",
  description: "Explore our comprehensive digital solutions and services.",
};

export default function ServicesPage() {
  const breadcrumbItems = [
    { label: "Home", href: "/" },
    { label: "Services", href: "/services" },
  ];

  return (
    <Layout>
      <div className="bg-white">
        <AdvancedBreadcrumb items={breadcrumbItems} />
        <div className="py-8">
          <ServicesSections />
        </div>
      </div>
    </Layout>
  );
}
