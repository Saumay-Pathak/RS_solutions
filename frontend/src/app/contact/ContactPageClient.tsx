"use client";

import { useEffect, useState, type FormEvent, type ChangeEvent } from "react";
import Layout from "@/components/layout/Layout";
import AdvancedBreadcrumb from "@/components/common/Bredacrumb";
import { IconPhone, IconMail, IconMapPin } from "@tabler/icons-react";
import { getContactInfo, submitForm } from "@/services/contactServices";
import { getFaqs } from "@/services/faqService";
import Select, { type StylesConfig, type SingleValue } from "react-select";
import locationService from "@/services/locationService";
import Swal from "sweetalert2";

type ContactInfo = {
  customer_support_number?: string;
  partner_support_number?: string;
  enquiry_number?: string;
  service_center_number?: string;
  general_email?: string;
  support_email?: string;
  business_email?: string;
  whatsapp_number?: string;
  hq_name?: string;
  hq_address?: string;
  hq_city?: string;
  hq_state?: string;
  hq_country?: string;
  hq_postal_code?: string | null;
  uk_name?: string;
  uk_address?: string;
  uk_city?: string;
  uk_state?: string;
  uk_country?: string;
  uk_postal_code?: string | null;
  manufacturing_name?: string;
  manufacturing_address?: string;
  manufacturing_city?: string;
  manufacturing_state?: string;
  manufacturing_country?: string;
  manufacturing_postal_code?: string | null;
};

type FaqItem = { id: string; question: string; answer: string };

export default function ContactPageClient() {
  const [info, setInfo] = useState<ContactInfo | null>(null);
  const [faqs, setFaqs] = useState<FaqItem[]>([]);
  const [name, setName] = useState("");
  const [email, setEmail] = useState("");
  const [phone, setPhone] = useState("");
  const [city, setCity] = useState("");
  const [zipCode, setZipCode] = useState("");
  const [message, setMessage] = useState("");
  const [submitting, setSubmitting] = useState(false);
  const [isSubscribed, setIsSubscribed] = useState(false);
  const [countries, setCountries] = useState<{ name: string; code: string }[]>(
    []
  );
  const [states, setStates] = useState<
    { name: string; code: string; countryCode?: string }[]
  >([]);
  const [selectedCountry, setSelectedCountry] = useState<{
    name: string;
    code: string;
  } | null>(null);
  const [selectedState, setSelectedState] = useState<{
    name: string;
    code: string;
    countryCode?: string;
  } | null>(null);
  const [loadingLists, setLoadingLists] = useState({
    countries: false,
    states: false,
  });

  useEffect(() => {
    const load = async () => {
      try {
        const res = await getContactInfo();
        setInfo(res.data || null);
      } catch {}
      try {
        const f = await getFaqs(1);
        const list = Array.isArray(f?.data) ? f.data.slice(0, 6) : [];
        setFaqs(list as FaqItem[]);
      } catch {}
      try {
        setLoadingLists((p) => ({ ...p, countries: true }));
        const cs = await locationService.fetchCountries();
        setCountries(cs);
      } finally {
        setLoadingLists((p) => ({ ...p, countries: false }));
      }
    };
    load();
  }, []);

  useEffect(() => {
    if (!selectedCountry) return;
    const load = async () => {
      setLoadingLists((p) => ({ ...p, states: true }));
      const ss = await locationService.fetchStatesByCountry(
        selectedCountry.code
      );
      setStates(ss);
      setSelectedState(null);
      setLoadingLists((p) => ({ ...p, states: false }));
    };
    load();
  }, [selectedCountry]);

  const isValidPhone = (v?: string | null) => {
    const s = String(v || "").trim();
    return s.length > 1 && s !== "0";
  };
  const endUser = isValidPhone(info?.customer_support_number)
    ? String(info?.customer_support_number).trim()
    : undefined;
  const registeredPartner = isValidPhone(info?.partner_support_number)
    ? String(info?.partner_support_number).trim()
    : undefined;
  const enquiry = isValidPhone(info?.enquiry_number)
    ? String(info?.enquiry_number).trim()
    : undefined;
  const serviceCenter = isValidPhone(info?.service_center_number)
    ? String(info?.service_center_number).trim()
    : undefined;
  const numbers = [endUser, registeredPartner, enquiry, serviceCenter]
    .filter((v) => v !== undefined && v !== null && String(v).trim().length >= 1)
    .map((v) => String(v).trim());

  const addressText = [
    info?.hq_address,
    info?.hq_city,
    info?.hq_state,
    info?.hq_country,
    info?.hq_postal_code,
  ]
    .filter(Boolean)
    .map(String)
    .join(", ");

  const branches = [
    {
      title: info?.hq_name || "Head Office",
      lines: [
        info?.hq_address,
        info?.hq_city,
        info?.hq_state,
        info?.hq_country,
        info?.hq_postal_code,
      ]
        .filter(Boolean)
        .map(String),
    },
    {
      title: info?.uk_name || "UK Office",
      lines: [
        info?.uk_address,
        info?.uk_city,
        info?.uk_state,
        info?.uk_country,
        info?.uk_postal_code,
      ]
        .filter(Boolean)
        .map(String),
    },
    {
      title: info?.manufacturing_name || "Manufacturing",
      lines: [
        info?.manufacturing_address,
        info?.manufacturing_city,
        info?.manufacturing_state,
        info?.manufacturing_country,
        info?.manufacturing_postal_code,
      ]
        .filter(Boolean)
        .map(String),
    },
  ].filter((b) => (b.title && b.title.trim().length > 0) || b.lines.length > 0);

  const handleSubmit = async (e: FormEvent<HTMLFormElement>) => {
    e.preventDefault();
    setSubmitting(true);
    try {
      const payload = {
        name,
        email,
        phone,
        city,
        zip: zipCode,
        message,
        subject: "Website Contact",
        form_type: "contact",
        page_url: typeof window !== "undefined" ? window.location.href : "",
        country: selectedCountry?.name || "",
        state: selectedState?.name || "",
        custom_fields: { subscribed: isSubscribed ? "Yes" : "No" },
      };
      const response = await submitForm(payload);
      if (response?.success || response?.status) {
        Swal.fire({
          icon: "success",
          title: "Submitted Successfully!",
          text: response.message || "Your message has been sent.",
          confirmButtonColor: "#3085d6",
        });
        setName("");
        setEmail("");
        setPhone("");
        setCity("");
        setZipCode("");
        setMessage("");
        setIsSubscribed(false);
        setSelectedCountry(null);
        setSelectedState(null);
      } else {
        Swal.fire({
          icon: "error",
          title: "Failed",
          text: response?.message || "Something went wrong",
        });
      }
    } catch {
      // ❌ Error alert
      Swal.fire({
        icon: "error",
        title: "Submission Failed",
        text: "Server error. Please try again later.",
      });
    } finally {
      setSubmitting(false);
    }
  };

  const breadcrumbItems = [
    { label: "Home", href: "/" },
    { label: "Contact", href: "/contact" },
  ];

  const selectStyles: StylesConfig<{ label: string; value: string }, false> = {
    control: (base, state) => ({
      ...base,
      padding: "2px",
      minHeight: "55px",
      borderRadius: "8px",
      borderColor: state.isFocused ? "#f97316" : "#d1d5db",
      boxShadow: "none",
      ":hover": { borderColor: "#fb923c" },
    }),
    singleValue: (base) => ({ ...base, color: "#1e1410" }),
    placeholder: (base) => ({ ...base, color: "#9ca3af" }),
    option: (base, state) => ({
      ...base,
      backgroundColor: state.isFocused ? "#fff7ed" : "white",
      color: state.isFocused ? "#9a3412" : "#1e1410",
    }),
  };

  return (
    <Layout>
      <AdvancedBreadcrumb items={breadcrumbItems} />
      <section className="pt-0 pb-0 mb-5">
        <div className="max-w-7xl mx-auto px-4 lg:px-10">
          <div className="flex text-center justify-center">
            <h1 className="section-title text-3xl font-bold">Contact Us</h1>
          </div>
          <p className="section-subtitle text-center text-sm">
            Reach our team for accurate answers and quick assistance
          </p>
        </div>
      </section>

      <section className="bg-white mb-10 md:mb-16">
        <div className="container mx-auto px-4">
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-8 md:gap-12">
            <div className="order-2 lg:order-1 bg-white rounded-2xl shadow-lg border border-gray-200 p-6 md:p-8">
              <h3 className="text-2xl font-bold mb-4 md:mb-6 text-[#1E1410]">
                Send Us a Message
              </h3>
              <form onSubmit={handleSubmit} className="space-y-6">
                <div className="relative">
                  <input
                    type="text"
                    value={name}
                    onChange={(e: ChangeEvent<HTMLInputElement>) =>
                      setName(e.target.value)
                    }
                    placeholder=" "
                    required
                    className="peer w-full border border-gray-300 rounded-lg px-4 pt-5 pb-2 text-black focus:border-orange-500 focus:ring-0 outline-none"
                  />
                  <label
                    className={`absolute left-4 bg-white px-1 transition-all duration-200 ${
                      name
                        ? "top-1 text-xs text-gray-500"
                        : "top-3.5 text-gray-500 text-sm"
                    } peer-focus:top-1 peer-focus:text-xs peer-focus:text-orange-500`}
                  >
                    Name*
                  </label>
                </div>
                <div className="relative">
                  <input
                    type="email"
                    value={email}
                    onChange={(e: ChangeEvent<HTMLInputElement>) =>
                      setEmail(e.target.value)
                    }
                    placeholder=" "
                    required
                    className="peer w-full border border-gray-300 rounded-lg px-4 pt-5 pb-2 text-black focus:border-orange-500 focus:ring-0 outline-none"
                  />
                  <label
                    className={`absolute left-4 bg-white px-1 transition-all duration-200 ${
                      email
                        ? "top-1 text-xs text-gray-500"
                        : "top-3.5 text-gray-500 text-sm"
                    } peer-focus:top-1 peer-focus:text-xs peer-focus:text-orange-500`}
                  >
                    Email*
                  </label>
                </div>
                <label className="flex items-center gap-3 cursor-pointer select-none">
                  <input
                    type="checkbox"
                    className="sr-only"
                    checked={isSubscribed}
                    onChange={(e) => setIsSubscribed(e.target.checked)}
                  />
                  <div
                    className={`${
                      isSubscribed
                        ? "bg-orange-500 border-orange-500"
                        : "border-gray-400"
                    } w-6 h-6 rounded border flex items-center justify-center transition-all`}
                  >
                    {isSubscribed && (
                      <svg
                        className="w-4 h-4 text-white"
                        fill="none"
                        stroke="currentColor"
                        strokeWidth="3"
                        viewBox="0 0 24 24"
                      >
                        <path d="M5 13l4 4L19 7" />
                      </svg>
                    )}
                  </div>
                  <span className="text-sm md:text-[15px] text-black/60">
                    Subscribe to our newsletter
                  </span>
                </label>
                <div className="relative">
                  <input
                    type="text"
                    value={phone}
                    onChange={(e: ChangeEvent<HTMLInputElement>) =>
                      setPhone(e.target.value)
                    }
                    placeholder=" "
                    required
                    className="peer w-full border border-gray-300 rounded-lg px-4 pt-5 pb-2 text-black focus:border-orange-500 focus:ring-0 outline-none"
                  />
                  <label
                    className={`absolute left-4 bg-white px-1 transition-all duration-200 ${
                      phone
                        ? "top-1 text-xs text-gray-500"
                        : "top-3.5 text-gray-500 text-sm"
                    } peer-focus:top-1 peer-focus:text-xs peer-focus:text-orange-500`}
                  >
                    Phone Number*
                  </label>
                </div>
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <Select
                    instanceId="country-select"
                    inputId="country-select-input"
                    options={countries.map((c) => ({
                      label: c.name,
                      value: c.code,
                    }))}
                    value={
                      selectedCountry
                        ? {
                            label: selectedCountry.name,
                            value: selectedCountry.code,
                          }
                        : null
                    }
                    onChange={(
                      option: SingleValue<{ label: string; value: string }>
                    ) => {
                      if (!option) {
                        setSelectedCountry(null);
                        return;
                      }
                      const country =
                        countries.find((c) => c.code === option.value) || null;
                      setSelectedCountry(country);
                    }}
                    isLoading={loadingLists.countries}
                    isSearchable={true}
                    placeholder="Search Country..."
                    styles={selectStyles}
                  />
                  <Select
                    instanceId="state-select"
                    inputId="state-select-input"
                    options={states.map((s) => ({
                      label: s.name,
                      value: s.code,
                    }))}
                    value={
                      selectedState
                        ? {
                            label: selectedState.name,
                            value: selectedState.code,
                          }
                        : null
                    }
                    onChange={(
                      option: SingleValue<{ label: string; value: string }>
                    ) => {
                      if (!option) {
                        setSelectedState(null);
                        return;
                      }
                      const state =
                        states.find((st) => st.code === option.value) || null;
                      setSelectedState(state);
                    }}
                    isLoading={loadingLists.states}
                    isSearchable={true}
                    placeholder={
                      selectedCountry
                        ? "Search State..."
                        : "Select Country first"
                    }
                    styles={selectStyles}
                    isDisabled={!selectedCountry}
                  />
                </div>
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <div className="relative">
                    <input
                      type="text"
                      value={city}
                      onChange={(e: ChangeEvent<HTMLInputElement>) =>
                        setCity(e.target.value)
                      }
                      placeholder=" "
                      className="peer w-full border border-gray-300 rounded-lg px-4 pt-5 pb-2 text-black focus:border-orange-500 focus:ring-0 outline-none"
                    />
                    <label
                      className={`absolute left-4 bg-white px-1 transition-all duration-200 ${
                        city
                          ? "top-1 text-xs text-gray-500"
                          : "top-3.5 text-gray-500 text-sm"
                      } peer-focus:top-1 peer-focus:text-xs peer-focus:text-orange-500`}
                    >
                      City
                    </label>
                  </div>
                  <div className="relative">
                    <input
                      type="text"
                      value={zipCode}
                      onChange={(e: ChangeEvent<HTMLInputElement>) =>
                        setZipCode(e.target.value)
                      }
                      placeholder=" "
                      className="peer w-full border border-gray-300 rounded-lg px-4 pt-5 pb-2 text-black focus:border-orange-500 focus:ring-0 outline-none"
                    />
                    <label
                      className={`absolute left-4 bg-white px-1 transition-all duration-200 ${
                        zipCode
                          ? "top-1 text-xs text-gray-500"
                          : "top-3.5 text-gray-500 text-sm"
                      } peer-focus:top-1 peer-focus:text-xs peer-focus:text-orange-500`}
                    >
                      Zipcode
                    </label>
                  </div>
                </div>
                <div>
                  <label className="text-sm text-gray-600">Message</label>
                  <textarea
                    value={message}
                    onChange={(e: ChangeEvent<HTMLTextAreaElement>) =>
                      setMessage(e.target.value)
                    }
                    rows={4}
                    className="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 text-sm text-black bg-white focus:border-orange-500 focus:ring-0 outline-none"
                    required
                  />
                </div>
                <button
                  disabled={submitting}
                  className="w-full md:w-auto bg-orange-500 hover:bg-orange-600 text-white px-6 py-2 rounded-md text-sm font-medium"
                >
                  {submitting ? "Sending..." : "Send Message"}
                </button>
              </form>
            </div>
            <div className="order-1 lg:order-2 p-0">
              <h3 className="text-[28px] md:text-[34px] font-[700] mb-2">
                <span className="text-[#1E1410]">
                  Secure Your Premises with
                </span>
                <span className="text-orange-600">
                  {" "}
                  R S Solutions - Realtime Biometrics
                </span>
              </h3>
              <p className="text-[#4B4B4B] mb-8">
                Connect with our security experts today — claim your free
                on-site consultation and see how we can protect your property!
              </p>
              <div className="space-y-6">
                <div className="flex items-start gap-3">
                  <div className="h-10 w-10 rounded-lg bg-orange-50 text-orange-600 flex items-center justify-center">
                    <IconPhone size={20} />
                  </div>
                  <div>
                    <div className="text-[#1E1410] font-medium">Support Numbers</div>
                    <ul className="text-[#4B4B4B] text-sm space-y-1">
                      {enquiry && (
                        <li>
                          Enquiry — <a href={`tel:${enquiry}`} className="text-[#1E1410]">{enquiry}</a>
                        </li>
                      )}
                      {endUser && (
                        <li>
                          End User — <a href={`tel:${endUser}`} className="text-[#1E1410]">{endUser}</a>
                        </li>
                      )}
                      {registeredPartner && (
                        <li>
                          Registered partner — <a href={`tel:${registeredPartner}`} className="text-[#1E1410]">{registeredPartner}</a>
                        </li>
                      )}
                      {serviceCenter && (
                        <li>
                          Service Center — <a href={`tel:${serviceCenter}`} className="text-[#1E1410]">{serviceCenter}</a>
                        </li>
                      )}
                    </ul>
                  </div>
                </div>
                <div className="flex items-start gap-3">
                  <div className="h-10 w-10 rounded-lg bg-orange-50 text-orange-600 flex items-center justify-center">
                    <IconMail size={20} />
                  </div>
                  <div>
                    <div className="text-[#1E1410] font-medium">
                      Email Addresses
                    </div>
                    <div className="text-[#4B4B4B] text-sm break-all">
                      {info?.general_email ||
                        info?.support_email ||
                        info?.business_email ||
                        "N/A"}
                    </div>
                  </div>
                </div>
                <div className="flex items-start gap-3">
                  <div className="h-10 w-10 rounded-lg bg-orange-50 text-orange-600 flex items-center justify-center">
                    <IconMapPin size={20} />
                  </div>
                  <div>
                    <div className="text-[#1E1410] font-medium">
                      Corporate Headquarters
                    </div>
                    <div className="text-[#4B4B4B] text-sm">
                      {[
                        info?.hq_address,
                        info?.hq_city,
                        info?.hq_state,
                        info?.hq_country,
                        info?.hq_postal_code,
                      ]
                        .filter(Boolean)
                        .map(String)
                        .join(", ") || "N/A"}
                    </div>
                  </div>
                </div>
                {addressText && (
                  <div className="mt-6 md:mt-8">
                    <div className="rounded-2xl overflow-hidden border border-gray-200 shadow-sm">
                      <iframe
                        title="company-location-map"
                        src={`https://www.google.com/maps?q=${encodeURIComponent(
                          addressText
                        )}&output=embed`}
                        className="w-full h-[240px] md:h-[320px]"
                        loading="lazy"
                        referrerPolicy="no-referrer-when-downgrade"
                      />
                    </div>
                  </div>
                )}
              </div>
            </div>
          </div>
        </div>
      </section>

      <section className="bg-white mb-10 md:mb-16">
        <div className="container mx-auto px-4">
          <div className="section-title text-center text-2xl font-semibold">
            Our Branches
          </div>
          <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
            {branches.map((b, idx) => (
              <div
                key={idx}
                className="rounded-2xl bg-white p-5 border border-gray-200 shadow-sm"
              >
                <div className="flex items-start gap-3">
                  <div className="h-10 w-10 rounded-lg bg-orange-50 text-orange-600 grid place-items-center">
                    <IconMapPin size={20} />
                  </div>
                  <div>
                    <div className="text-[#1E1410] font-medium">{b.title}</div>
                    <ul className="text-sm text-gray-600 mt-1">
                      {b.lines.map((line, i) => (
                        <li key={i}>{line}</li>
                      ))}
                    </ul>
                  </div>
                </div>
              </div>
            ))}
          </div>
        </div>
      </section>

      <section className="bg-white mb-10 md:mb-16">
        <div className="container mx-auto px-4">
          <div className="text-[#1E1410] text-lg font-semibold mb-4">
            Frequently Asked Questions
          </div>
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            {faqs.map((f) => (
              <div
                key={f.id}
                className="rounded-xl bg-white p-5 border border-gray-200"
              >
                <div className="text-[#1E1410] font-medium mb-1">
                  {f.question}
                </div>
                <div className="text-sm text-gray-600">{f.answer}</div>
              </div>
            ))}
          </div>
        </div>
      </section>

      <section className="bg-gradient-to-r from-orange-800 to-orange-600 text-center">
        <h3 className="text-2xl sm:text-3xl font-bold text-white mb-1 pt-10">
          Ready to modernize your workforce?
        </h3>
        <p className="text-orange-100 mb-8">
          Book a demo and see how this solution transforms operations.
        </p>
        <div className="flex flex-col sm:flex-row px-4 justify-center gap-4 pb-10">
          {(info?.enquiry_number || info?.customer_support_number) && (
            <a
              href={`tel:${(info?.enquiry_number || info?.customer_support_number || "8080892888").replace(/[^0-9+]/g, "")}`}
              className="inline-flex items-center px-4 py-2 rounded-md bg-white text-[#9E4940] font-medium"
            >
              <IconPhone size={18} className="mr-2" /> {info?.enquiry_number || info?.customer_support_number || "+91 80808 92888"}
            </a>
          )}
          {(info?.general_email ||
            info?.support_email ||
            info?.business_email) && (
            <a
              href={`mailto:${
                info?.general_email ||
                info?.support_email ||
                info?.business_email
              }`}
              className="inline-flex items-center px-4 py-2 rounded-md bg-white text-[#9E4940] font-medium"
            >
              <IconMail size={18} className="mr-2" />{" "}
              {
                (info?.general_email ||
                  info?.support_email ||
                  info?.business_email) as string
              }
            </a>
          )}

        </div>
      </section>
    </Layout>
  );
}
