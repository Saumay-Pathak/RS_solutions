"use client";

import { useState, useEffect, ChangeEvent, FormEvent } from "react";
import Select from "react-select";
import Swal from "sweetalert2";
import axiosClient from "@/services/axiosClient";

interface Option {
  value: string;
  label: string;
}

interface ModalProps {
  isOpen: boolean;
  onClose: () => void;
  productName?: string;
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

export default function SendRequirementModal({ isOpen, onClose, productName }: ModalProps) {
  /** ---------------- OPTIONS ---------------- **/
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

  /** ---------------- FORM STATE ---------------- **/
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

  /** ---------------- RESET FORM ---------------- **/
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

  /** ---------------- LOCK BODY SCROLL WHEN OPEN ---------------- **/
  useEffect(() => {
    if (!isOpen) {
      resetForm();
      return;
    }

    document.body.style.overflow = "hidden";

    const params = new URLSearchParams(window.location.search);

    setTracking({
      page_url: window.location.href,
      referrer: document.referrer,
      utm_source: params.get("utm_source") || "",
      utm_medium: params.get("utm_medium") || "",
      utm_campaign: params.get("utm_campaign") || "",
    });

    return () => {
      document.body.style.removeProperty("overflow");
    };
  }, [isOpen]);

  // Safe portal target for react-select menus (client-only)
  const portalTarget = typeof document !== "undefined" ? document.body : undefined;

  /** ---------------- HANDLE INPUT ---------------- **/
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

  /** ---------------- PINCODE AUTO FILL ---------------- **/
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

  /** ---------------- VALIDATION ---------------- **/
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

  /** ---------------- SUBMIT ---------------- **/
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
      ...(productName ? { product: productName } : {}),
    };

    try {
      const res = await axiosClient.post("/sales/requirements", payload);

      Swal.fire({
        icon: "success",
        title: "Submitted",
        text: res.data.message,
      });

      onClose();
    } catch {
      Swal.fire({
        icon: "error",
        title: "Error",
        text: "Something went wrong",
      });
    } finally {
      setLoading(false);
    }
  };

  if (!isOpen) return null;

  /** ---------------- UI START ---------------- **/
  return (
    <div className="fixed inset-0 bg-black/70 flex justify-center items-center z-50 p-4">
      <div className="bg-white w-full max-w-2xl rounded-2xl shadow-xl overflow-hidden">
        {/* HEADER */}
        <div className="flex justify-between items-center p-6 pb-0  bg-orage-50/60">
          <div>
            <h2 className="text-2xl font-bold text-gray-900">
              Send Your Requirement
            </h2>
            <p className="text-sm text-gray-600 mt-1">
              We&apos;ll get back to you within 24 hours
            </p>
          </div>
          <button
            onClick={onClose}
            className="text-gray-500 hover:text-black cursor-pointer">
            ✖
          </button>
        </div>

        {/* FORM BODY */}
        <div className="p-6 max-h-[60vh] overflow-y-auto no-scrollbar">
          <form
            onSubmit={handleSubmit}
            className="grid grid-cols-1 md:grid-cols-2 gap-6">
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
              <p className="text-xs text-gray-500 mt-1">
                Enter pincode to auto-fill state & country
              </p>
              {pincodeLoading && (
                <p className="text-xs text-orange-500 mt-1 animate-pulse">
                  Fetching location…
                </p>
              )}
            </div>

            <FloatingInput
              label="Country *"
              name="country"
              value={form.country}
              onBlur={handleBlur}
              touched={touched.country}
              validate={validateField}
              onChange={handleChange}
            />
            <FloatingInput
              label="State *"
              name="state"
              value={form.state}
              onBlur={handleBlur}
              touched={touched.state}
              validate={validateField}
              onChange={handleChange}
            />

            {/* REQUIREMENT */}
            <div>
              <label className="text-sm text-gray-700 mb-1 block">
                Requirement Type *
              </label>
              <Select
                options={requirementTypes}
                value={selectedRequirement}
                onChange={setSelectedRequirement}
                placeholder="Select requirement..."
                menuPortalTarget={portalTarget}
                menuPosition="fixed"
                menuShouldScrollIntoView={false}
                styles={{
                  menuPortal: (base) => ({ ...base, zIndex: 999999 }),
                  option: (base) => ({
                    ...base,
                    color: "#333333",
                    cursor:"pointer"
                  }),
                }}
              />
            </div>

            {/* SOURCE */}
            <div>
              <label className="text-sm text-gray-700 mb-1 block">
                How did you hear about us? *
              </label>
              <Select
                options={sourceOptions}
                value={selectedSource}
                onChange={setSelectedSource}
                placeholder="Select source..."
                menuPortalTarget={portalTarget}
                menuPosition="fixed"
                menuShouldScrollIntoView={false}
                styles={{
                  menuPortal: (base) => ({ ...base, zIndex: 999999 }),
                    option: (base) => ({
                    ...base,
                    color: "#333333",
                    cursor:"pointer"
                  }),
                }}
              />
            </div>

            {/* MESSAGE */}
            <div className="md:col-span-2">
              <FloatingTextArea
                label="Additional Message"
                name="message"
                value={form.message}
                onChange={handleChange}
              />
            </div>
          </form>
        </div>

        {/* STICKY FOOTER BUTTON */}
        <div className="p-5 bg-white sticky bottom-0">
          <button
            onClick={handleSubmit}
            disabled={loading}
            className="w-full bg-orange-500 hover:bg-orange-600 text-white py-3 rounded-xl font-semibold shadow-md transition">
            {loading ? "Submitting..." : "Submit Requirement"}
          </button>
        </div>
      </div>
    </div>
  );
}

/** ---------------- REUSABLE INPUTS ---------------- **/

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
        className={`peer w-full border-2 rounded-xl px-4 pt-6 pb-2 text-black bg-gray-50 focus:bg-white transition 
         ${
           showError
             ? "border-red-500"
             : "border-gray-200 focus:border-orange-500"
         }`}
        placeholder=" "
        name={name}
        value={value}
        onChange={onChange}
        onBlur={onBlur}
        maxLength={maxLength}
      />
      <label className="absolute left-4 top-4 text-gray-500 text-sm transition peer-focus:top-2 peer-focus:text-xs peer-[&:not(:placeholder-shown)]:top-2 peer-[&:not(:placeholder-shown)]:text-xs">
        {label}
      </label>
      {showError && <p className="text-red-500 text-xs mt-1">{error}</p>}
    </div>
  );
}

function FloatingTextArea({ label, name, value, onChange }: FloatingTextareaProps) {
  return (
    <div className="relative">
      <textarea
        rows={4}
        className="peer w-full border-2 border-gray-200 rounded-xl px-4 pt-7 pb-3 text-black bg-gray-50 focus:border-orange-500"
        placeholder=" "
        name={name}
        value={value}
        onChange={onChange}
      />
      <label className="absolute left-4 top-4 text-gray-500 text-sm transition peer-focus:top-3 peer-focus:text-xs peer-[&:not(:placeholder-shown)]:top-3 peer-[&:not(:placeholder-shown)]:text-xs">
        {label}
      </label>
    </div>
  );
}
