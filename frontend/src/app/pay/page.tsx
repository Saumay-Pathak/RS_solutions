"use client";
import AdvancedBreadcrumb from "@/components/common/Bredacrumb";
import Layout from "@/components/layout/Layout";
import Link from "next/link";
import Image from "next/image";
import React from "react";
import { baseUri } from "@/services/constant";
import { FaUniversity, FaCopy } from "react-icons/fa";

const Page = () => {
  const breadcrumbItems = [
    { label: "Home", href: "/" },
    { label: "Pay Online", href: "/pay" },
  ];

  const copyToClipboard = (text: string) => {
    navigator.clipboard.writeText(text);
  };

  return (
    <Layout>
      <AdvancedBreadcrumb items={breadcrumbItems} />

      <section className="container mx-auto px-4 md:px-8">
        {/* Heading */}
        <div className="text-center mb-10">
          <h1 className="section-title text-3xl md:text-4xl font-bold text-gray-900">
            Pay Online
          </h1>
          <p className="section-subtitle text-gray-600 mt-2 text-sm md:text-base">
            Pay securely via PayU or Paytm
          </p>
        </div>

        {/* Bank Info */}
        <div className="grid gap-6 md:grid-cols-3 mb-12">
          <div className="flex items-center gap-3 p-4 bg-white border rounded-xl shadow hover:shadow-lg transition">
            <FaUniversity className="text-orange-600 w-6 h-6" />
            <div>
              <p className="text-xs text-gray-500">NEFT NOW</p>
              <p className="text-sm font-medium text-gray-900">
                Realtime Biometrics India Pvt. Ltd.
              </p>
              <p className="text-xs text-gray-600">Branch: Preet Vihar</p>
            </div>
          </div>

          <div className="flex items-center justify-between p-4 bg-white border rounded-xl shadow hover:shadow-lg transition">
            <div>
              <p className="text-xs text-gray-500">ICICI Account Number</p>
              <p className="text-sm font-medium text-gray-900">003705020179</p>
            </div>
            <button
              onClick={() => copyToClipboard("003705020179")}
              className="flex items-center gap-1 text-gray-500 hover:text-gray-700 transition"
            >
              <FaCopy className="w-5 h-5" />
              <span className="text-xs">Copy</span>
            </button>
          </div>

          <div className="flex items-center justify-between p-4 bg-white border rounded-xl shadow hover:shadow-lg transition">
            <div>
              <p className="text-xs text-gray-500">ICICI IFSC Code</p>
              <p className="text-sm font-medium text-gray-900">ICIC0000037</p>
            </div>
            <button
              onClick={() => copyToClipboard("ICIC0000037")}
              className="flex items-center gap-1 text-gray-500 hover:text-gray-700 transition"
            >
              <FaCopy className="w-5 h-5" />
              <span className="text-xs">Copy</span>
            </button>
          </div>
        </div>

        {/* Payment Options */}
        <div className="grid gap-8 md:grid-cols-2 mb-15">
          {/* PayU */}
          <div className="border rounded-2xl shadow hover:shadow-lg transition overflow-hidden">
            <div className="flex items-center gap-3 p-5 border-b bg-gray-50">
              <Image
                src="https://upload.wikimedia.org/wikipedia/commons/thumb/c/cd/PayU.svg/1200px-PayU.svg.png"
                alt="PayU"
                width={120}
                height={40}
                className="h-9 w-auto"
              />
              <span className="font-semibold text-gray-900">
                Pay via PayU or any UPI App
              </span>
            </div>
            <div className="p-6 flex flex-col items-center">
              <p className="text-gray-900 font-medium">Realtime Biometrics</p>
              <Image
                src={`${baseUri}gallery/8F9rdpWPWrvoW2Dd7Lv8XbFiL8lH8egR2ID1HH5V.jpg`}
                alt="PayU QR Code"
                width={400}
                height={400}
                className="mt-4 w-56 md:w-64 h-auto rounded-md border border-gray-200"
              />
              <Link
                href="https://payu.in/web/987B6EFBB6EDCAC2CAA841A408CFFB76"
                target="_blank"
                className="mt-5 px-5 py-1.5 bg-orange-600 hover:bg-orange-700 text-white font-medium rounded-lg transition"
              >
                Pay via PayU Link
              </Link>
              <p className="mt-2 text-xs text-gray-500 text-center">
                Scan QR with any UPI app or use the PayU link
              </p>
            </div>
          </div>

          {/* Paytm */}
          <div className="border rounded-2xl shadow hover:shadow-lg transition overflow-hidden">
            <div className="flex items-center gap-3 p-5 border-b bg-gray-50">
              <Image
                src="https://upload.wikimedia.org/wikipedia/commons/4/42/Paytm_logo.png"
                alt="Paytm"
                width={120}
                height={40}
                className="h-9 w-auto"
              />
              <span className="font-semibold text-gray-900">
                Pay via Paytm or any UPI App
              </span>
            </div>
            <div className="p-6 flex flex-col items-center">
              <p className="text-gray-900 font-medium">Verified Merchant</p>
              <Image
                src={`${baseUri}gallery/QHUpy2CYbCtvgZmmCUHs4QfOAiuDFcwkkCmLkIvO.png`}
                alt="Paytm QR Code"
                width={400}
                height={400}
                className="mt-4 w-56 md:w-64 h-auto rounded-md border border-gray-200"
              />
              <p className="mt-5 text-xs text-gray-500 text-center">
                Scan QR with Paytm or any UPI app
              </p>
            </div>
          </div>
        </div>
      </section>
    </Layout>
  );
};

export default Page;
