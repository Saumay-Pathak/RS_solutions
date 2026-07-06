import AdvancedBreadcrumb from "@/components/common/Bredacrumb";
import Layout from "@/components/layout/Layout";
import DownloadSoftware from "@/components/software/DownloadSoftware";
import React from "react";

const page = () => {
  const breadcrumbItems = [
    { label: "Home", href: "/" },
    { label: "Software", href: "/software" },
  ];
  return (
    <Layout>
      <div className="bg-white">
        <AdvancedBreadcrumb items={breadcrumbItems} />
        <DownloadSoftware />
      </div>
    </Layout>
  );
};

export default page;
