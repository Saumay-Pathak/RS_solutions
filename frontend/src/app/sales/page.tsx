"use client";

import { useState, useEffect, ChangeEvent, FormEvent } from "react";
import Image from "next/image";
import Select, { StylesConfig, GroupBase } from "react-select";
import Swal from "sweetalert2";
import axiosClient from "@/services/axiosClient";
import Link from "next/link";

interface Option {
  value: string;
  label: string;
}

interface FloatingTextareaProps {
  label: string;
  name: string;
  value: string;
  onChange: (e: ChangeEvent<HTMLTextAreaElement>) => void;
}

interface FloatingInputProps {
  label: string;
  name: string;
  value: string;
  onChange: (e: ChangeEvent<HTMLInputElement>) => void;
  onBlur?: (e: ChangeEvent<HTMLInputElement>) => void;
  touched?: boolean;
  validate?: (name: string, value: string) => string;
  maxLength?: number;
}

export default function RequirementForm() {
  const menuPortalTarget =
    typeof document !== "undefined" ? document.body : undefined;

  const requirementTypes: Option[] = [
    { value: "Face + Fingerprint Device", label: "Face + Fingerprint Device" },
    { value: "Face Device", label: "Face Device" },
    { value: "Aadhar Device", label: "Aadhar Device" },
    { value: "Fingerprint Device", label: "Fingerprint Device" },
    { value: "4G WiFi Router", label: "4G WiFi Router" },
    { value: "4G/WiFi Cameras", label: "4G/WiFi Cameras" },
    { value: "POE", label: "POE" },
    { value: "Accessories", label: "Accessories" },
    { value: "Support", label: "Support" },
    { value: "Others", label: "Others" },
  ];

  const sourceOptions: Option[] = [
    { value: "General", label: "General" },
    { value: "Social Media Ad", label: "Social Media Ad" },
    { value: "Others", label: "Others" },
  ];

  const [form, setForm] = useState({
    name: "",
    email: "",
    phone: "",
    pincode: "",
    state: "",
    country: "",
    message: "",
  });

  const [tracking, setTracking] = useState({
    page_url: "",
    referrer: "",
    utm_source: "",
    utm_medium: "",
    utm_campaign: "",
  });

  const [selectedRequirement, setSelectedRequirement] = useState<Option | null>(
    null
  );
  const [selectedSource, setSelectedSource] = useState<Option | null>(null);
  const [loading, setLoading] = useState(false);
  const [touched, setTouched] = useState<Record<string, boolean>>({});
  const [pincodeLoading, setPincodeLoading] = useState(false);

  const resetForm = () => {
    setForm({
      name: "",
      email: "",
      phone: "",
      pincode: "",
      state: "",
      country: "",
      message: "",
    });
    setSelectedRequirement(null);
    setSelectedSource(null);
    setTouched({});
  };

  useEffect(() => {
    const params = new URLSearchParams(window.location.search);
    setTracking({
      page_url: window.location.href,
      referrer: document.referrer,
      utm_source: params.get("utm_source") || "",
      utm_medium: params.get("utm_medium") || "",
      utm_campaign: params.get("utm_campaign") || "",
    });
  }, []);

  const handleChange = (
    e: ChangeEvent<HTMLInputElement | HTMLTextAreaElement>
  ) => {
    setForm((prev) => ({ ...prev, [e.target.name]: e.target.value }));
  };

  const handleBlur = (
    e: ChangeEvent<HTMLInputElement | HTMLTextAreaElement>
  ) => {
    setTouched((prev) => ({ ...prev, [e.target.name]: true }));
  };

  // Auto-fill state/country based on pincode
  useEffect(() => {
    if (form.pincode.length !== 6 || !/^\d+$/.test(form.pincode)) return;

    const fetchPincode = async () => {
      try {
        setPincodeLoading(true);
        const res = await fetch(
          `https://api.postalpincode.in/pincode/${form.pincode}`
        );
        const data = await res.json();
        if (data[0]?.Status === "Success") {
          const po = data[0].PostOffice[0];
          setForm((prev) => ({
            ...prev,
            state: po.State,
            country: po.Country,
          }));
        } else {
          Swal.fire({
            toast: true,
            icon: "warning",
            title: "Invalid pincode",
            position: "top-right",
            timer: 2500,
            showConfirmButton: false,
          });
        }
      } finally {
        setPincodeLoading(false);
      }
    };

    const timer = setTimeout(fetchPincode, 600);
    return () => clearTimeout(timer);
  }, [form.pincode]);

  const validateField = (name: string, value: string): string => {
    switch (name) {
      case "name":
        return value.length < 2 ? "Enter a valid name" : "";
      case "email":
        return !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value) ? "Invalid email" : "";
      case "phone":
        return !/^\d{10}$/.test(value) ? "Enter 10-digit number" : "";
      case "pincode":
        return !/^\d{6}$/.test(value) ? "Invalid pincode" : "";
      case "state":
        return value.trim() === "" ? "State required" : "";
      case "country":
        return value.trim() === "" ? "Country required" : "";
      default:
        return "";
    }
  };

  const validateForm = (): string | null => {
    const fields = [
      "name",
      "email",
      "phone",
      "pincode",
      "state",
      "country",
    ] as const;
    for (const key of fields) {
      const err = validateField(key, form[key]);
      if (err) return err;
    }
    if (!selectedRequirement) return "Requirement type is required";
    if (!selectedSource) return "Source is required";
    return null;
  };

  const handleSubmit = async (e: FormEvent) => {
    e.preventDefault();
    setTouched({
      name: true,
      email: true,
      phone: true,
      pincode: true,
      state: true,
      country: true,
    });

    const err = validateForm();
    if (err) {
      Swal.fire({ icon: "warning", title: "Validation Error", text: err });
      return;
    }

    setLoading(true);
    const payload = {
      ...form,
      requirement_type: selectedRequirement!.value,
      source: selectedSource!.value,
      ...tracking,
    };

    try {
      const res = await axiosClient.post("/sales/requirements", payload);
      Swal.fire({
        icon: "success",
        title: "Submitted Successfully!",
        text: res.data.message,
      });
      resetForm();
    } catch {
      Swal.fire({
        icon: "error",
        title: "Error",
        text: "Something went wrong. Please try again.",
      });
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="max-w-5xl mx-auto bg-white rounded-3xl shadow-xl overflow-hidden my-0 sm:my-10">
      {/* Header */}
      <div className="bg-gradient-to-r from-orange-50 to-white p-8 text-center relative">
        <div className="flex justify-center mb-4">
          <Link href="/">
            <Image
              src="/images/logo-black.png"
              alt="Realtime Biometrics logo"
              width={200}
              height={60}
            />
          </Link>
        </div>
        <h2 className="text-3xl md:text-4xl font-bold text-black mb-2">
          Send Your Requirement
        </h2>
        <p className="text-gray-500 text-sm md:text-base">
          We&apos;ll get back to you within 24 hours
        </p>
      </div>

      {/* Form */}
      <div className="px-6 md:px-16 py-10">
        <form
          onSubmit={handleSubmit}
          className="grid grid-cols-1 md:grid-cols-2 gap-6"
        >
          {/* Name & Email */}
          <FloatingInput
            label="Full Name *"
            name="name"
            value={form.name}
            onBlur={handleBlur}
            touched={touched.name}
            validate={validateField}
            onChange={handleChange}
          />
          <FloatingInput
            label="Email Address *"
            name="email"
            value={form.email}
            onBlur={handleBlur}
            touched={touched.email}
            validate={validateField}
            onChange={handleChange}
          />

          {/* Phone & Pincode */}
          <FloatingInput
            label="Phone Number *"
            name="phone"
            value={form.phone}
            onBlur={handleBlur}
            touched={touched.phone}
            validate={validateField}
            onChange={handleChange}
            maxLength={10}
          />
          <div>
            <FloatingInput
              label="Pincode *"
              name="pincode"
              value={form.pincode}
              onBlur={handleBlur}
              touched={touched.pincode}
              validate={validateField}
              onChange={handleChange}
              maxLength={6}
            />
            {pincodeLoading && (
              <p className="text-xs text-orange-500 mt-1 animate-pulse">
                Fetching location…
              </p>
            )}
          </div>

          {/* State & Country */}
          <FloatingInput
            label="State *"
            name="state"
            value={form.state}
            onBlur={handleBlur}
            touched={touched.state}
            validate={validateField}
            onChange={handleChange}
          />
          <FloatingInput
            label="Country *"
            name="country"
            value={form.country}
            onBlur={handleBlur}
            touched={touched.country}
            validate={validateField}
            onChange={handleChange}
          />

          {/* Requirement & Source */}
          <div>
            <label className="text-sm text-gray-700 mb-1 block">
              Requirement Type *
            </label>
            <Select
              options={requirementTypes}
              value={selectedRequirement}
              onChange={setSelectedRequirement}
              placeholder="Select requirement..."
              menuPortalTarget={menuPortalTarget}
              menuPosition="fixed"
              menuShouldScrollIntoView={false}
              styles={selectStyles}
            />
          </div>
          <div>
            <label className="text-sm text-gray-700 mb-1 block">
              How did you hear about us? *
            </label>
            <Select
              options={sourceOptions}
              value={selectedSource}
              onChange={setSelectedSource}
              placeholder="Select source..."
              menuPortalTarget={menuPortalTarget}
              menuPosition="fixed"
              menuShouldScrollIntoView={false}
              styles={selectStyles}
            />
          </div>

          {/* Message */}
          <div className="md:col-span-2">
            <FloatingTextArea
              label="Additional Message"
              name="message"
              value={form.message}
              onChange={handleChange}
            />
          </div>

          {/* Submit */}
          <div className="md:col-span-2">
            <button
              type="submit"
              disabled={loading}
              className="w-full bg-orange-500 hover:bg-orange-600 text-white py-4 rounded-xl font-semibold text-lg shadow-lg transition transform hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              {loading ? (
                <div className="flex items-center justify-center">
                  <svg
                    className="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                  >
                    <circle
                      className="opacity-25"
                      cx="12"
                      cy="12"
                      r="10"
                      stroke="currentColor"
                      strokeWidth="4"
                    ></circle>
                    <path
                      className="opacity-75"
                      fill="currentColor"
                      d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                    ></path>
                  </svg>
                  Submitting...
                </div>
              ) : (
                "Submit Requirement"
              )}
            </button>
          </div>
        </form>
      </div>
    </div>
  );
}

// -------------------- REUSABLE INPUTS --------------------
function FloatingInput({
  label,
  name,
  value,
  onChange,
  onBlur,
  touched,
  validate,
  maxLength,
}: FloatingInputProps) {
  const error = validate ? validate(name, value) : "";
  const showError = touched && error;

  return (
    <div className="relative">
      <input
        className={`w-full text-black border-1 rounded-xl px-4 pt-6 pb-2 bg-gray-50 focus:bg-white transition
    ${
      showError
        ? "border-red-500 focus:border-red-500"
        : "border-gray-200 focus:border-orange-500"
    }
  `}
        placeholder=" "
        name={name}
        value={value}
        onChange={onChange}
        onBlur={onBlur}
        maxLength={maxLength}
      />
      <label
        className={`absolute left-4 text-gray-500 text-sm transition-all duration-200 pointer-events-none
    ${value ? "top-2 text-xs text-orange-600" : "top-4 text-sm"}
  `}
      >
        {label}
      </label>
      {showError && <p className="text-red-500 text-xs mt-1">{error}</p>}
    </div>
  );
}

function FloatingTextArea({
  label,
  name,
  value,
  onChange,
}: FloatingTextareaProps) {
  return (
    <div className="relative">
      <textarea
        rows={5}
        className="peer w-full border-2 border-gray-200 rounded-xl px-4 pt-7 pb-3 text-black bg-gray-50 focus:bg-white focus:border-orange-500 transition resize-none"
        placeholder=" "
        name={name}
        value={value}
        onChange={onChange}
      />
      <label className="absolute left-4 top-4 text-gray-500 text-sm peer-placeholder-shown:top-4 peer-placeholder-shown:text-sm peer-focus:top-3 peer-focus:text-xs peer-focus:text-orange-600 transition-all duration-200 pointer-events-none">
        {label}
      </label>
    </div>
  );
}

const selectStyles: StylesConfig<Option, false, GroupBase<Option>> = {
  menuPortal: (base) => ({ ...base, zIndex: 999999 }),
  control: (base) => ({
    ...base,
    border: "2px solid #e5e7eb",
    borderRadius: "12px",
    padding: "4px 0",
    cursor: "pointer",
    boxShadow: "none",
    ":hover": { border: "2px solid #f97316" },
  }),
  option: (base) => ({
    ...base,
    color: "#333",
    cursor: "pointer",
    ":hover": { backgroundColor: "#fff7ed" },
  }),
};
