// app/blog/page.tsx
import React from "react";
import Layout from "@/components/layout/Layout";
import Title from "@/components/common/Title";
import BlogList from "@/components/blog/BlogList";
import AdvancedBreadcrumb from "@/components/common/Bredacrumb";
// Server component jo initial data fetch karta hai
const BlogPage = async () => {
  const breadcrumbItems = [
    { label: "Home", href: "/" },
    { label: "Blog", href: "/blog" }, // Current page
  ];
  return (
    <Layout>
      <AdvancedBreadcrumb items={breadcrumbItems} />
      <Title title="Realtime Newsroom" subtitle="Newsrooms" />
      <section className="bg-gray-50 py-8">
        <div className="container mx-auto px-4">
          {/* Blog List Component */}
          <BlogList />
        </div>
      </section>
    </Layout>
  );
};

export default BlogPage;
