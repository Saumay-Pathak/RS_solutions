"use client";

import { useState, useEffect, type ChangeEvent, type FormEvent } from "react";
import Select, { type StylesConfig, type SingleValue } from "react-select";
import Swal from "sweetalert2";
import { IconMail, IconPhone, IconMapPin } from "@tabler/icons-react";

import locationService from "@/services/locationService";
import { getContactInfo, submitForm, submitNewsletter } from "@/services/contactServices";

type OptionType = { label: string; value: string };
type Country = { name: string; code: string };
type StateRegion = { name: string; code: string };
type ContactInfo = {
  is_active?: boolean;
  display_order?: number;
  customer_support_number?: string;
  enquiry_number?: string;
  general_email?: string;
  hq_name?: string;
  hq_address?: string;
  hq_city?: string;
  hq_state?: string;
  hq_country?: string;
  hq_postal_code?: string | null;
  manufacturing_address?: string;
  manufacturing_city?: string;
  manufacturing_state?: string;
  manufacturing_country?: string;
  manufacturing_postal_code?: string | null;
};

/* ------------------- FLOATING INPUT ------------------- */
interface FloatingInputProps {
  label: string;
  name: string;
  value: string;
  onChange: (e: ChangeEvent<HTMLInputElement>) => void;
  type?: string;
  required?: boolean;
}

function FloatingInput({ label, name, value, onChange, type = "text", required = false }: FloatingInputProps) {
  const hasValue = value !== "" && value !== undefined && value !== null;

  return (
    <div className="relative">
      <input
        type={type}
        name={name}
        value={value}
        required={required}
        onChange={onChange}
        placeholder=" "
        className="peer w-full border border-gray-300 rounded-lg px-4 pt-5 pb-2 text-black 
                   focus:border-orange-500 focus:ring-0 outline-none"
      />
      <label
        className={`absolute left-4 bg-white px-1 transition-all duration-200
          ${hasValue ? "top-1 text-xs text-gray-500" : "top-3.5 text-gray-500 text-sm"}
          peer-focus:top-1 peer-focus:text-xs peer-focus:text-orange-500`}
      >
        {label}
      </label>
    </div>
  );
}

/* ------------------- FLOATING TEXTAREA ------------------- */
interface FloatingTextareaProps {
  label: string;
  name: string;
  value: string;
  onChange: (e: ChangeEvent<HTMLTextAreaElement>) => void;
  required?: boolean;
  rows?: number;
}

function FloatingTextarea({ label, name, value, onChange, required = false, rows = 4 }: FloatingTextareaProps) {
  const hasValue = value !== "";
  return (
    <div className="relative">
      <textarea
        name={name}
        value={value}
        required={required}
        onChange={onChange}
        rows={rows}
        placeholder=" "
        className="peer w-full border border-gray-300 rounded-lg px-4 pt-5 pb-2 text-black 
                   focus:border-orange-500 focus:ring-0 outline-none resize-none"
      />
      <label
        className={`absolute left-4 bg-white px-1 transition-all duration-200
        ${hasValue ? "top-1 text-xs text-gray-500" : "top-3.5 text-gray-500 text-sm"}
        peer-focus:top-1 peer-focus:text-xs peer-focus:text-orange-500`}
      >
        {label}
      </label>
    </div>
  );
}

/* ------------------- CONTACT SECTION ------------------- */
const ContactSection = () => {
  const [formData, setFormData] = useState({
    name: "",
    email: "",
    phone: "",
    city: "",
    zipCode: "",
    message: "",
  });
  const [isSubscribed, setIsSubscribed] = useState(false);

  const [countries, setCountries] = useState<Country[]>([]);
  const [states, setStates] = useState<StateRegion[]>([]);
  const [selectedCountry, setSelectedCountry] = useState<Country | null>(null);
  const [selectedState, setSelectedState] = useState<StateRegion | null>(null);
  const [contactInfo, setContactInfo] = useState<ContactInfo | null>(null);
  const [loading, setLoading] = useState({ countries: false, states: false });
  const [isSubmitting, setIsSubmitting] = useState(false);

  /* Fetch countries and contact info */
  useEffect(() => {
    const loadCountries = async () => {
      setLoading((p) => ({ ...p, countries: true }));
      const res = await locationService.fetchCountries();
      setCountries(res);
      setLoading((p) => ({ ...p, countries: false }));
    };
    loadCountries();

    const loadContactInfo = async () => {
      try {
        const data = await getContactInfo();
        setContactInfo(data.data as ContactInfo);
      } catch (err) {
        console.error("Error fetching contact info:", err);
      }
    };
    loadContactInfo();
  }, []);

  /* Fetch states when country changes */
  useEffect(() => {
    if (!selectedCountry) return;
    const loadStates = async () => {
      setLoading((p) => ({ ...p, states: true }));
      const res = await locationService.fetchStatesByCountry(selectedCountry.code);
      setStates(res);
      setSelectedState(null);
      setLoading((p) => ({ ...p, states: false }));
    };
    loadStates();
  }, [selectedCountry]);

  const handleInputChange = (e: ChangeEvent<HTMLInputElement | HTMLTextAreaElement>) => {
    const { name, value } = e.target;
    setFormData((prev) => ({ ...prev, [name]: value }));
  };

  const handleCountryChange = (option: SingleValue<OptionType>) => {
    if (!option) return setSelectedCountry(null);
    const country = countries.find((c) => c.code === option.value) || null;
    setSelectedCountry(country);
  };

  const handleStateChange = (option: SingleValue<OptionType>) => {
    if (!option) return setSelectedState(null);
    const state = states.find((s) => s.code === option.value) || null;
    setSelectedState(state);
  };

  const handleSubscribeChange = async (e: ChangeEvent<HTMLInputElement>) => {
    const checked = e.target.checked;
    setIsSubscribed(checked);
    if (checked) {
      try {
        const res = await submitNewsletter(formData.name, formData.email);
        Swal.fire("Subscribed!", res.message, "success");
      } catch {
        Swal.fire("Error", "Subscription failed", "error");
      }
    }
  };

  const handleSubmit = async (e: FormEvent<HTMLFormElement>) => {
    e.preventDefault();
    setIsSubmitting(true);
    const payload = {
      ...formData,
      country: selectedCountry?.name || "",
      state: selectedState?.name || "",
      subject: "Website Contact",
      form_type: "contact",
      page_url: typeof window !== "undefined" ? window.location.href : "",
      custom_fields: { subscribed: isSubscribed ? "Yes" : "No" },
    };
    try {
      const res = await submitForm(payload);
      Swal.fire("Success!", res.message, "success");
      setFormData({ name: "", email: "", phone: "", city: "", zipCode: "", message: "" });
    } catch {
      Swal.fire("Error", "Submission failed", "error");
    }
    setIsSubmitting(false);
  };

  const selectStyles: StylesConfig<OptionType, false> = {
    control: (base, state) => ({
      ...base,
      padding: "2px",
      minHeight: "55px",
      borderRadius: "8px",
      borderColor: state.isFocused ? "#f97316" : "#d1d5db",
      boxShadow: "none",
      "&:hover": { borderColor: "#fb923c" },
    }),
    singleValue: (base) => ({ ...base, color: "#1e1410" }),
    placeholder: (base) => ({ ...base, color: "#9ca3af" }),
    option: (base, state) => ({
      ...base,
      backgroundColor: state.isFocused ? "#fff7ed" : "white",
      color: state.isFocused ? "#9a3412" : "#1e1410",
    }),
  };

  const hqAddress = [contactInfo?.hq_address, contactInfo?.hq_city, contactInfo?.hq_state, contactInfo?.hq_country, contactInfo?.hq_postal_code].filter(Boolean).join(", ");
  const manufacturingAddress = [contactInfo?.manufacturing_address, contactInfo?.manufacturing_city, contactInfo?.manufacturing_state, contactInfo?.manufacturing_country, contactInfo?.manufacturing_postal_code].filter(Boolean).join(", ");
  const addressText = hqAddress || manufacturingAddress;

  return (
    <section className="mx-auto bg-white">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-12 pb-15">
        <div className="text-[#1E1410] text-center mb-8">
          <h2 className="section-title font-bold text-2xl sm:text-3xl">Get in Touch</h2>
          <p className="section-subtitle max-w-4xl mx-auto text-sm">Ready to revolutionize your workforce management? Let&apos;s talk!</p>
        </div>

        <div className="grid grid-cols-1 lg:grid-cols-2 gap-10 md:gap-16">
          {/* Contact Info Left */}
          <div className="order-1 lg:order-1 p-0">
            <h3 className="text-2xl md:text-[34px] font-[700] mb-4 md:mb-6 mt-8 md:mt-0">
              <span className="text-[#1E1410]">Secure Your Premises with</span>
              <span className="text-orange-600"> R S Solutions - Realtime Biometrics</span>
            </h3>
            <p className="text-[#4B4B4B] text-sm md:text-base mb-6 md:mb-8">Connect with our security experts today — claim your free consultation and see how we can protect your property & assets!</p>
            <div className="space-y-6">
              {/* Phone Numbers */}
              {contactInfo?.enquiry_number && (
                <div className="flex items-start gap-3">
                  <div className="h-10 w-10 rounded-lg bg-orange-50 text-orange-600 flex items-center justify-center">
                    <IconPhone size={20} />
                  </div>
                  <div>
                    <div className="text-[#1E1410] font-medium">Contact Number</div>
                    <div className="text-[#4B4B4B] text-sm">{contactInfo.enquiry_number}</div>
                  </div>
                </div>
              )}
              {contactInfo?.customer_support_number && (
                <div className="flex items-start gap-3">
                  <div className="h-10 w-10 rounded-lg bg-orange-50 text-orange-600 flex items-center justify-center">
                    <IconPhone size={20} />
                  </div>
                  <div>
                    <div className="text-[#1E1410] font-medium">Support Number</div>
                    <div className="text-[#4B4B4B] text-sm">{contactInfo.customer_support_number}</div>
                  </div>
                </div>
              )}

              {/* Email */}
              {contactInfo?.general_email && (
                <div className="flex items-start gap-3">
                  <div className="h-10 w-10 rounded-lg bg-orange-50 text-orange-600 flex items-center justify-center">
                    <IconMail size={20} />
                  </div>
                  <div>
                    <div className="text-[#1E1410] font-medium">Email Address</div>
                    <div className="text-[#4B4B4B] text-sm break-all">{contactInfo.general_email}</div>
                  </div>
                </div>
              )}

              {/* HQ / Manufacturing Address */}
              {(contactInfo?.hq_name || addressText) && (
                <div className="flex items-start gap-3">
                  <div className="h-10 w-10 rounded-lg bg-orange-50 text-orange-600 flex items-center justify-center">
                    <IconMapPin size={20} />
                  </div>
                  <div>
                    <div className="text-[#1E1410] font-medium">Corporate Headquarters</div>
                    <div className="text-[#4B4B4B] text-sm">{addressText}</div>
                  </div>
                </div>
              )}

              {/* Google Map */}
              {addressText && (
                <div className="mt-6 md:mt-8">
                  <div className="rounded-2xl overflow-hidden border border-gray-200 shadow-sm">
                    <iframe
                      title="company-location-map"
                      src={`https://www.google.com/maps?q=${encodeURIComponent(addressText)}&output=embed`}
                      className="w-full h-[240px] md:h-[320px]"
                      loading="lazy"
                      referrerPolicy="no-referrer-when-downgrade"
                    />
                  </div>
                </div>
              )}
            </div>
          </div>

          {/* Form Right */}
          <div className="order-2 lg:order-2 bg-white rounded-2xl shadow-lg border border-gray-200 p-5 md:p-8">
            <h3 className="text-md md:text-[26px] font-bold text-[#1E1410]">Send Us a Message</h3>
            <p className="mb-10 text-stone-600">Contact Now to get a call back!</p>
            <form onSubmit={handleSubmit} className="space-y-6">
              <FloatingInput label="Name*" name="name" value={formData.name} onChange={handleInputChange} />
              <FloatingInput label="Email*" name="email" type="email" value={formData.email} onChange={handleInputChange} />
              <label htmlFor="subscribe" className="flex items-center gap-3 cursor-pointer select-none">
                <input id="subscribe" type="checkbox" className="sr-only" checked={isSubscribed} onChange={handleSubscribeChange} />
                <div className={`${isSubscribed ? "bg-orange-500 border-orange-500" : "border-gray-400"} w-6 h-6 rounded border flex items-center justify-center transition-all`}>
                  {isSubscribed && (<svg className="w-4 h-4 text-white" fill="none" stroke="currentColor" strokeWidth="3" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" /></svg>)}
                </div>
                <span className="text-sm md:text-[15px] text-black/60">Subscribe to our newsletter</span>
              </label>
              <FloatingInput label="Phone Number*" name="phone" value={formData.phone} onChange={handleInputChange} />

              <div className="grid grid-cols-1 sm:grid-cols-2 gap-3 md:gap-4">
                <Select instanceId="country-select" inputId="country-select-input" options={countries.map((c) => ({ label: c.name, value: c.code }))} value={selectedCountry ? { label: selectedCountry.name, value: selectedCountry.code } : null} onChange={handleCountryChange} isLoading={loading.countries} isSearchable placeholder="Search Country..." styles={selectStyles} />
                <Select instanceId="state-select" inputId="state-select-input" options={states.map((s) => ({ label: s.name, value: s.code }))} value={selectedState ? { label: selectedState.name, value: selectedState.code } : null} onChange={handleStateChange} isDisabled={!selectedCountry} isLoading={loading.states} isSearchable placeholder={selectedCountry ? "Search State..." : "Select Country first"} styles={selectStyles} />
              </div>

              <div className="grid grid-cols-1 sm:grid-cols-2 gap-3 md:gap-4">
                <FloatingInput label="City*" name="city" value={formData.city} onChange={handleInputChange} />
                <FloatingInput label="Zip Code*" name="zipCode" value={formData.zipCode} onChange={handleInputChange} />
              </div>

              <FloatingTextarea label="Message*" name="message" value={formData.message} onChange={handleInputChange} rows={5} />

              <div className="flex items-start gap-3 select-none">
                <input
                  type="checkbox"
                  required
                  id="consent-checkbox-contact-section"
                  className="w-4 h-4 mt-1 accent-orange-500 cursor-pointer animate-none"
                />
                <label htmlFor="consent-checkbox-contact-section" className="text-xs text-gray-600 leading-normal cursor-pointer text-left">
                  I hereby authorise to send notification on SMS/Messages/WhatsApp/Promotional/ RCS/ information Messages. By clicking Submit, you agree to our{" "}
                  <a
                    href="/terms-of-service"
                    target="_blank"
                    className="text-orange-600 underline hover:text-orange-700 font-medium"
                  >
                    Terms of Services
                  </a>{" "}
                  and then you have Read our{" "}
                  <a
                    href="/privacy-policy"
                    target="_blank"
                    className="text-orange-600 underline hover:text-orange-700 font-medium"
                  >
                    Privacy Policy
                  </a>.
                </label>
              </div>

              <button type="submit" className="bg-orange-500 text-white py-2.5 md:py-2 w-full rounded-md shadow-md hover:shadow-lg hover:bg-orange-600 transition">{isSubmitting ? "Submitting..." : "Submit"}</button>
            </form>
          </div>
        </div>
      </div>
    </section>
  );
};

export default ContactSection;
