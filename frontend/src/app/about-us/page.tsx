"use client";
import AdvancedBreadcrumb from "@/components/common/Bredacrumb";
import CertificationsSection from "@/components/sections/CertificationsSection";
import Layout from "@/components/layout/Layout";
import Image from "next/image";
import { useEffect, useState } from "react";
import { getContactInfo } from "@/services/contactServices";
import {
  FaShieldAlt,
  FaCloud,
  FaCogs,
  FaSyncAlt,
  FaHeadset,
  FaIndustry,
} from "react-icons/fa";
import { LifeBuoy } from "lucide-react";
import Link from "next/link";

const Page = () => {
  const breadcrumbItems = [
    { label: "Home", href: "/" },
    { label: "About Us", href: "/about-us" },
  ];

  return (
    <Layout>
      <div className="bg-white">
        <AdvancedBreadcrumb items={breadcrumbItems} />
        <section className="relative overflow-hidden">
          <div className="container mx-auto grid md:grid-cols-2 gap-8 items-center py-4 sm:py-12 px-4 md:px-8">
            <div className="space-y-6">
              <h1 className="text-2xl sm:text-3xl font-bold text-gray-900 leading-snug">
                About Us
              </h1>
              <p className="text-gray-700 text-md leading-relaxed">
                Founded in 2010, Realtime Biometrics has evolved from a system
                integrator into India’s leading biometric and security
                technology brand. With over 15 years of continuous innovation,
                we have played a pivotal role in transforming how organizations
                manage attendance, identity, and access control across
                government and private sectors.
              </p>
            </div>
            <div className="relative h-56 md:h-80 rounded-2xl overflow-hidden shadow-lg">
              <Image
                src="https://api.rssolutionsindia.co.in/storage/gallery/pwI5bfW68tQeymOV8QU3gKkDeUd3UeOHzVK9oug2.jpg"
                alt="About Realtime Biometrics"
                fill
                sizes="(min-width: 768px) 50vw, 100vw"
                className="object-cover"
                priority
              />
            </div>
          </div>
        </section>

        {/* Main Content Grid */}
        <section className="container mx-auto px-4 md:px-8 py-12 space-y-12">
          {/* Row 1: Global Brand & Made in India */}
          <div className="grid md:grid-cols-2 gap-8">
            <div className="bg-white rounded-2xl border border-gray-200 p-8 shadow-sm hover:shadow-md transition-shadow">
              <h2 className="text-xl font-bold text-gray-900 mb-4">
                A Globally Registered & Trusted Brand
              </h2>
              <p className="text-gray-700 leading-relaxed">
                Realtime Biometrics is a globally registered and protected
                brand, officially registered in the USA, United Kingdom, Europe,
                and Hong Kong. Our products and solutions are trusted worldwide
                for their reliability, compliance, and long-term stability,
                making us a preferred choice for large-scale and mission-critical
                deployments.
              </p>
            </div>
            <div className="bg-white rounded-2xl border border-gray-200 p-8 shadow-sm hover:shadow-md transition-shadow">
              <h2 className="text-xl font-bold text-gray-900 mb-4">
                Proudly Made in India
              </h2>
              <p className="text-gray-700 leading-relaxed">
                We are a 100% Indian manufacturer with a complete manufacturing
                and development ecosystem within India. From R&D and hardware
                engineering to firmware, software development, and final
                production, every stage is executed in-house. This commitment
                strengthens India’s self-reliant technology ecosystem and
                ensures superior quality, faster customization, and unmatched
                reliability.
              </p>
            </div>
          </div>

          {/* Row 2: Global Presence & Government Leadership */}
          <div className="grid md:grid-cols-2 gap-8">
            <div className="bg-white rounded-2xl border border-gray-200 p-8 shadow-sm hover:shadow-md transition-shadow">
              <h2 className="text-xl font-bold text-gray-900 mb-4">
                Global Presence Across 42 Countries
              </h2>
              <p className="text-gray-700 leading-relaxed">
                With a strong partner and distributor network across Asia, the
                Middle East, Africa, and Europe, Realtime Biometrics is among
                India’s fastest-growing multinational biometric brands. We
                deliver global standards powered by Indian innovation, serving
                diverse industries and regulatory environments.
              </p>
            </div>
            <div className="bg-white rounded-2xl border border-gray-200 p-8 shadow-sm hover:shadow-md transition-shadow">
              <h2 className="text-xl font-bold text-gray-900 mb-4">
                Government Market Leadership
              </h2>
              <p className="text-gray-700 leading-relaxed">
                Realtime Biometrics holds an unmatched 70% market share in
                Government Attendance Projects in India. We offer the largest
                and most stable range of Aadhaar Enabled Biometric Attendance
                Systems (AEBAS), trusted and field-tested for nationwide
                deployments.
              </p>
            </div>
          </div>

          {/* Row 3: In-House Development & Manufacturing */}
          <div className="grid md:grid-cols-2 gap-8">
            <div className="bg-white rounded-2xl border border-gray-200 p-8 shadow-sm hover:shadow-md transition-shadow">
              <h2 className="text-xl font-bold text-gray-900 mb-4">
                100% In-House Development Advantage
              </h2>
              <p className="text-gray-700 mb-4">
                Our complete in-house capability includes:
              </p>
              <ul className="list-disc list-inside space-y-2 text-gray-700 mb-4 ml-2">
                <li>Hardware design & engineering</li>
                <li>Firmware development</li>
                <li>Software and cloud platform development</li>
                <li>Continuous testing, upgrades, and customization</li>
              </ul>
              <p className="text-gray-700">
                This vertical integration allows us to deliver highly reliable
                products with the fastest turnaround for customization in the
                industry.
              </p>
            </div>
            <div className="bg-white rounded-2xl border border-gray-200 p-8 shadow-sm hover:shadow-md transition-shadow">
              <h2 className="text-xl font-bold text-gray-900 mb-4">
                Complete Manufacturing Ecosystem
              </h2>
              <p className="text-gray-700 mb-4">
                Our Indian manufacturing ecosystem includes:
              </p>
              <ul className="list-disc list-inside space-y-2 text-gray-700 mb-4 ml-2">
                <li>PCB manufacturing</li>
                <li>Injection mould design & fabrication</li>
                <li>Hardware assembly & testing</li>
                <li>Firmware and full software development lifecycle</li>
              </ul>
              <p className="text-gray-700">
                This ensures consistent quality control and long-term product
                support.
              </p>
            </div>
          </div>

          {/* Row 4: Brand Recall, Partner Model, Recurring Revenue */}
          <div className="grid md:grid-cols-3 gap-6">
            <div className="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm hover:shadow-md transition-shadow">
              <h2 className="text-lg font-bold text-gray-900 mb-3">
                Strong Brand Recall & Market Trust
              </h2>
              <p className="text-gray-700 text-sm leading-relaxed">
                Realtime Biometrics is recognized as one of the strongest brands
                in biometric attendance and access control. Enterprises,
                institutions, system integrators, and government bodies
                associate our brand with innovation, dependable after-sales
                support, and product longevity.
              </p>
            </div>
            <div className="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm hover:shadow-md transition-shadow">
              <h2 className="text-lg font-bold text-gray-900 mb-3">
                Partner-First Growth Model
              </h2>
              <p className="text-gray-700 text-sm leading-relaxed">
                Our products create continuous market demand, enabling partners
                to sell across multiple product categories, not limited to
                biometrics alone. This results in higher customer retention,
                expanded business opportunities, and recurring revenue growth.
              </p>
            </div>
            <div className="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm hover:shadow-md transition-shadow">
              <h2 className="text-lg font-bold text-gray-900 mb-3">
                Software & Recurring Revenue Opportunities
              </h2>
              <p className="text-gray-700 text-sm leading-relaxed">
                We offer a lifetime recurring income model for partners through
                annual software renewals and SaaS-based solutions, delivering
                high-margin earning potential alongside hardware sales.
              </p>
            </div>
          </div>

          {/* Solution Driven Organization */}
          <div className="bg-orange-50 rounded-2xl p-8 md:p-12 text-center">
            <h2 className="text-2xl font-bold text-gray-900 mb-4">
              Solution-Driven Organization
            </h2>
            <p className="text-gray-700 max-w-3xl mx-auto leading-relaxed">
              Realtime Biometrics is not just a hardware manufacturer—we are a
              solution-driven technology organization. Our integrated Hardware +
              Software + Cloud ecosystem is designed to solve real-world
              challenges for government, enterprises, SMEs, retail, and
              institutions.
            </p>
          </div>

          {/* Row 5: Portfolios */}
          <div className="grid md:grid-cols-3 gap-6">
            <div className="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm hover:shadow-md transition-shadow">
              <h2 className="text-lg font-bold text-gray-900 mb-3">
                Largest Product & Solution Portfolio
              </h2>
              <p className="text-gray-700 text-sm mb-3">
                Our comprehensive portfolio includes:
              </p>
              <ul className="list-disc list-inside space-y-1 text-gray-700 text-sm mb-3 ml-1">
                <li>Biometric Attendance Terminals</li>
                <li>AI Face Recognition Devices</li>
                <li>DFMD / HFMD</li>
                <li>Parking Boom Barriers</li>
                <li>PoE Switches</li>
              </ul>
              <p className="text-gray-700 text-sm">
                All offered under one unified brand, enabling complete security
                and automation solutions.
              </p>
            </div>
            <div className="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm hover:shadow-md transition-shadow">
              <h2 className="text-lg font-bold text-gray-900 mb-3">
                Advanced Software Platforms
              </h2>
              <p className="text-gray-700 text-sm mb-3">
                Our software solutions include:
              </p>
              <ul className="list-disc list-inside space-y-1 text-gray-700 text-sm ml-1">
                <li>Cloud Attendance Management System</li>
                <li>Parking Management (QR, Face & ANPR)</li>
                <li>Gym & Club Management System</li>
                <li>Visitor Management with pre-registration and face recognition</li>
              </ul>
            </div>
            <div className="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm hover:shadow-md transition-shadow">
              <h2 className="text-lg font-bold text-gray-900 mb-3">
                Maximum Integration Capability
              </h2>
              <p className="text-gray-700 text-sm leading-relaxed">
                Realtime Biometrics solutions integrate seamlessly with all
                major HRMS, Payroll, and ERP platforms through open APIs, making
                our ecosystem the most compatible and flexible in the Indian
                market.
              </p>
            </div>
          </div>
        </section>

        {/* Why Choose Us */}
        <section className="container mx-auto px-4 md:px-8">
          <div className="text-center mb-10 pt-10">
            <h2 className="text-2xl sm:text-3xl section-title-long font-bold text-gray-900 mb-2">
              Why Choose Realtime Biometrics
            </h2>
            <p className="text-gray-500 text-sm md:text-base">
              Built for reliability, scale, and seamless integration
            </p>
          </div>
          <div className="grid md:grid-cols-3 gap-8 mb-10">
            {[
              {
                title: "Secure by Design",
                desc: "Multi-layered security with encrypted data flows and role-based access.",
                Icon: FaShieldAlt,
              },
              {
                title: "Cloud & On-Premise",
                desc: "Flexible deployments to match your infrastructure and compliance needs.",
                Icon: FaCloud,
              },
              {
                title: "Easy Integration",
                desc: "RESTful APIs and connectors for HRMS, ERP, and facility systems.",
                Icon: FaSyncAlt,
              },
              {
                title: "Configurable Workflows",
                desc: "Adapt rules, schedules, and policies to match your operations.",
                Icon: FaCogs,
              },
              {
                title: "Enterprise Support",
                desc: "Implementation assistance, training, and 24/7 support options.",
                Icon: FaHeadset,
              },
              {
                title: "Industry Ready",
                desc: "Solutions tailored for manufacturing, corporate, education, and more.",
                Icon: FaIndustry,
              },
            ].map((f) => (
              <div
                key={f.title}
                className="bg-white rounded-2xl border border-gray-200 p-6 shadow-md hover:shadow-xl transition-transform transform hover:-translate-y-1"
              >
                <div className="flex items-center justify-center w-16 h-16 mx-auto rounded-full bg-gradient-to-tr from-orange-300 to-orange-500 text-white mb-4">
                  <f.Icon className="w-6 h-6" />
                </div>
                <h3 className="text-center font-semibold text-gray-900 text-lg">
                  {f.title}
                </h3>
                <p className="text-center text-gray-700 text-sm mt-2">
                  {f.desc}
                </p>
              </div>
            ))}
          </div>
        </section>

        {/* Industries */}
        <section className="container mx-auto px-4 md:px-8 bg-orange-50 rounded-3xl mb-12">
          <div className="text-center mb-6 pt-10">
            <h2 className="text-2xl sm:text-3xl section-title font-bold text-gray-900">
              Industries We Serve
            </h2>
            <p className="text-gray-600 text-sm">
              Cross-industry deployments at scale
            </p>
          </div>
          <div className="flex flex-wrap justify-center gap-3 mb-10">
            {[
              "Manufacturing",
              "Corporate Offices",
              "Education",
              "Healthcare",
              "Retail",
              "Government",
              "Logistics",
              "Hospitality",
            ].map((i) => (
              <span
                key={i}
                className="px-4 py-2 rounded-full bg-white border border-gray-200 text-gray-700 text-sm shadow-sm hover:shadow-lg transition-all cursor-pointer"
              >
                {i}
              </span>
            ))}
          </div>
        </section>

        {/* Mission & Vision */}
        <section className="container mx-auto px-4 md:px-8 py-12 grid md:grid-cols-2 gap-8">
          {[
            {
              title: "Our Mission",
              desc: "To design and develop world-class smart security and automation products under one roof, helping clients across industries secure, manage, and automate their premises effectively.",
            },
            {
              title: "Our Vision",
              desc: "To become one of the top five global leaders in Biometric Attendance, Time & Attendance, Access Control, Entrance Control, Traffic & Parking, and Inspection Control Systems — known for quality, innovation, and customer satisfaction.",
            },
          ].map((item) => (
            <div
              key={item.title}
              className="bg-white rounded-2xl border border-gray-200 p-6 shadow-md hover:shadow-xl transition-all mb-10"
            >
              <h3 className="text-xl font-semibold text-gray-900 text-center">
                {item.title}
              </h3>
              <p className="mt-3 text-gray-700 text-sm leading-relaxed text-justify">
                {item.desc}
              </p>
            </div>
          ))}
        </section>

        {/* Certifications */}
        <CertificationsSection />

        {/* Presence */}
        <PresenceWithMap />

        <div className="py-10 px-4">
          <div className="container mx-auto rounded-2xl border border-neutral-200 shadow-sm p-5 md:p-8 flex flex-col md:flex-row items-center gap-4 md:gap-6 bg-white">
            <div className="w-12 h-12 rounded-full bg-orange-50 text-orange-600 flex items-center justify-center">
              <LifeBuoy className="w-6 h-6" />
            </div>
            <div className="flex-1 text-center md:text-left">
              <h3 className="text-base md:text-lg font-semibold text-neutral-800">
                Need Help Getting Started?
              </h3>
              <p className="mt-1 text-sm md:text-base text-neutral-600 pe-0 sm:pe-10">
                Our technical support team is ready to assist you with
                installation, configuration, and any questions you may have
                about our software.
              </p>
            </div>
            <Link
              href="/sales"
              className="me-0 sm:me-10 inline-flex items-center bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg text-sm md:text-base font-medium transition hover:translate-y-1"
            >
              Contact Sales
            </Link>
          </div>
        </div>
      </div>
    </Layout>
  );
};

type ContactInfo = {
  hq_address?: string;
  hq_city?: string;
  hq_state?: string;
  hq_country?: string;
  hq_postal_code?: string;
  manufacturing_address?: string;
  manufacturing_city?: string;
  manufacturing_state?: string;
  manufacturing_country?: string;
  manufacturing_postal_code?: string;
  hq_phone?: string;
  hq_email?: string;
  manufacturing_phone?: string;
  manufacturing_email?: string;
};

// Presence + Map section
const PresenceWithMap: React.FC = () => {
  const [contactInfo, setContactInfo] = useState<ContactInfo | null>(null);

  useEffect(() => {
    const fetchContactInfo = async () => {
      try {
        const res = await getContactInfo();
        setContactInfo(res?.data || null);
      } catch (error) {
        console.warn("Contact Info load failed:", error);
      }
    };
    fetchContactInfo();
  }, []);

  const fullAddress = [
    contactInfo?.hq_address,
    contactInfo?.hq_city,
    contactInfo?.hq_state,
    contactInfo?.hq_country,
    contactInfo?.hq_postal_code,
  ]
    .filter(Boolean)
    .join(", ");

  const manufacturingAddress = [
    contactInfo?.manufacturing_address,
    contactInfo?.manufacturing_city,
    contactInfo?.manufacturing_state,
    contactInfo?.manufacturing_country,
    contactInfo?.manufacturing_postal_code,
  ]
    .filter(Boolean)
    .join(", ");

  return (
    <section className="bg-gray-50 px-4 md:px-8">
      <div className="container mx-auto text-center py-10 px-6">
        <h2 className="text-3xl font-bold text-black text-center mb-8">
          Strong Presence in Major Cities
        </h2>
        <div className="grid md:grid-cols-2 gap-8">
          {/* India */}
          <div>
            <h4 className="text-xl font-semibold text-gray-900 text-center">
              India
            </h4>
            <div className="flex flex-wrap justify-center gap-2 mt-4">
              {[
                "Jammu & Kashmir",
                "Haryana",
                "Delhi NCR",
                "Lucknow",
                "Indore",
                "Surat",
                "Ahmedabad",
                "Bengaluru",
                "Hyderabad",
                "Chennai",
                "Pune",
                "Mumbai",
                "Kolkata",
                "Jaipur",
                "Coimbatore",
                "Kochi",
                "Bhubaneshwar",
              ].map((city) => (
                <span
                  key={city}
                  className="px-3 py-1 rounded-full bg-orange-50 text-orange-700 border border-orange-200 text-sm hover:bg-orange-100 transition"
                >
                  {city}
                </span>
              ))}
            </div>
          </div>

          {/* GCC & Europe */}
          <div>
            <h4 className="text-xl font-semibold text-gray-900 text-center">
              GCC & Europe
            </h4>
            <div className="flex flex-wrap justify-center gap-2 mt-4">
              {[
                "UAE",
                "Saudi Arabia",
                "Oman",
                "Qatar",
                "Bahrain",
                "Kuwait",
                "Germany",
                "United Kingdom",
                "Taiwan",
              ].map((region) => (
                <span
                  key={region}
                  className="px-3 py-1 rounded-full bg-orange-50 text-orange-700 border border-orange-200 text-sm hover:bg-orange-100 transition"
                >
                  {region}
                </span>
              ))}
            </div>
          </div>
        </div>

        {/* Map */}
        <div className="mt-8 relative w-full h-64 md:h-96 rounded-2xl overflow-hidden shadow-lg">
          <Image
            src="https://api.rssolutionsindia.co.in/storage/gallery/sJwqp7HgQpIdnVtUHPczoFRsvVdVXBGtRbFmvRzu.png"
            alt="Global Presence"
            fill
            className="object-cover"
          />
        </div>

        {/* Addresses */}
        <div className="mt-6 flex flex-col md:flex-row justify-center gap-6 text-sm text-gray-700">
          <div className="text-center md:text-left">
            <span className="font-semibold text-gray-900">Headquarters:</span>{" "}
            {fullAddress || "Delhi NCR, India"}
            <br />
            {contactInfo?.hq_phone && (
              <>
                Phone: {contactInfo.hq_phone}
                <br />
              </>
            )}
            {contactInfo?.hq_email && <>Email: {contactInfo.hq_email}</>}
          </div>
          <div className="text-center md:text-left">
            <span className="font-semibold text-gray-900">Manufacturing:</span>{" "}
            {manufacturingAddress || "Delhi NCR, India"}
            <br />
            {contactInfo?.manufacturing_phone && (
              <>
                Phone: {contactInfo.manufacturing_phone}
                <br />
              </>
            )}
            {contactInfo?.manufacturing_email && (
              <>Email: {contactInfo.manufacturing_email}</>
            )}
          </div>
        </div>
      </div>
    </section>
  );
};

export default Page;
