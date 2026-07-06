import Layout from "@/components/layout/Layout";
import CertificationsClient from "./CertificationsClient";

export const metadata = {
  title: "Certifications",
  description: "Official certifications and recognitions",
};

export default async function CertificationsPage() {
  return (
    <Layout>
      <CertificationsClient />
    </Layout>
  );
}
