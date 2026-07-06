// app/career/page.tsx
import React from "react";
import Layout from "@/components/layout/Layout";
import Title from "@/components/common/Title";
import CareerList from "@/components/career/CareerList";
import AdvancedBreadcrumb from "@/components/common/Bredacrumb";

const CareerPage = async () => {
  const breadcrumbItems = [
    { label: "Home", href: "/" },
    { label: "Careers", href: "/careers" }, // Current page
  ];
  
  return (
    <Layout>
      <AdvancedBreadcrumb items={breadcrumbItems} />
      <Title title="Careers Opportunities" subtitle="Realtime Biometrics Career Openings" />
      <div className="min-h-screen bg-gray-50 py-8">
        <div className="container mx-auto px-4">
          {/* Career List Component with Pagination */}
          <CareerList />
        </div>
      </div>
    </Layout>
  );
};

export default CareerPage;