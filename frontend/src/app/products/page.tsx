import AdvancedBreadcrumb from "@/components/common/Bredacrumb";
import Layout from "@/components/layout/Layout";
import CatalogClient from "@/components/products/CatalogClient";

const Page = () => {
  const breadcrumbItems = [
    { label: "Home", href: "/" },
    { label: "Products", href: "/products" },
  ];
  return (
    <Layout>
      <div className="bg-white">
        <AdvancedBreadcrumb items={breadcrumbItems} />
        {/* Header / Hero */}
        <section className="">
          <div className="mx-auto px-4 lg:px-10 py-10 md:py-14 text-center">
            <h1 className="text-2xl md:text-3xl section-title font-bold text-gray-900 mb-2">
              Our Products
            </h1>
            <p className="text-gray-600 text-sm md:text-base mx-auto">
              Explore our complete catalogue of biometric, access control, and
              workplace automation products.
            </p>
          </div>
        </section>
        {/* Catalog */}
        <section className="bg-white">
          <CatalogClient />
        </section>
      </div>
    </Layout>
  );
};

export default Page;
