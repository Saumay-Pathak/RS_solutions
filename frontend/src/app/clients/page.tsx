import Layout from "@/components/layout/Layout";
import ClientsPageClient from "./ClientsPageClient";

export const metadata = {
  title: "Our Clients",
  description: "Companies who trust Realtime Biometrics",
};

export default async function ClientsPage() {
  return (
    <Layout>
      <ClientsPageClient />
    </Layout>
  );
}

