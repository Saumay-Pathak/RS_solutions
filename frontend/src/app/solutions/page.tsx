"use client";

import React from "react";
import Layout from "@/components/layout/Layout";
import Title from "@/components/common/Title";
import SolutionsGrid from "@/components/sections/SolutionsGrid";
import AdvancedBreadcrumb from "@/components/common/Bredacrumb";

// Static data moved outside the component
const breadcrumbItems = [
  { label: "Home", href: "/" },
  { label: "Solutions", href: "/solutions" },
];

const SolutionsPage = () => {
  return (
    <Layout>
      <main className="min-h-screen">
        <AdvancedBreadcrumb items={breadcrumbItems} />

        <section className="max-w-7xl mx-auto px-4">
          <Title
            title="Solutions We Offer"
            subtitle="Discover software and systems tailored for attendance, payroll, access control, and visitor management."
          />

          <div className="mt-8">
            <SolutionsGrid />
          </div>
        </section>
      </main>
    </Layout>
  );
};

export default SolutionsPage;
