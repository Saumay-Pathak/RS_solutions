"use client";

import {
  useEffect,
  useState,
  ChangeEvent,
  ReactNode,
  InputHTMLAttributes,
} from "react";

import Select, { SingleValue } from "react-select";
import Swal from "sweetalert2";
import axiosClient from "@/services/axiosClient";
import locationService from "@/services/locationService";
import Layout from "@/components/layout/Layout";
import FileUpload from "./FileUpload";

/* ================= TYPES ================= */

interface Option {
  value: string;
  label: string;
}
interface Country {
  name: string;
  code: string;
}
interface State {
  name: string;
  code: string;
}

// Tracking interface
interface TrackingInfo {
  ip_address: string;
  user_agent: string;
  page_url: string;
  referrer: string;
  utm_source: string;
  utm_medium: string;
  utm_campaign: string;
  utm_term?: string;
  utm_content?: string;
  status: string;
  priority: string;
}

interface FormDataState {
  company_name: string;
  director_name: string;
  email: string;
  mobile_number: string;
  gst_number: string;
  address: string;
  city: string;
  pin_code: string;
  district: string;
  country: string;
  state: string;
  engineer_name_1: string;
  engineer_number_1: string;
  engineer_name_2: string;
  engineer_number_2: string;
  engineer_name_3: string;
  engineer_number_3: string;
  engineer_name_4: string;
  engineer_number_4: string;
}

interface FloatingInputProps extends InputHTMLAttributes<HTMLInputElement> {
  label: string;
}

/* ================= CONSTANTS ================= */

const initialFormState: FormDataState = {
  company_name: "",
  director_name: "",
  email: "",
  mobile_number: "",
  gst_number: "",
  address: "",
  city: "",
  pin_code: "",
  district: "",
  country: "",
  state: "",
  engineer_name_1: "",
  engineer_number_1: "",
  engineer_name_2: "",
  engineer_number_2: "",
  engineer_name_3: "",
  engineer_number_3: "",
  engineer_name_4: "",
  engineer_number_4: "",
};

const isValidGST = (gst: string) =>
  /^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z][1-9A-Z]Z[0-9A-Z]$/.test(gst);

/* ================= PAGE ================= */

export default function PartnerRegisterPage() {
  const [form, setForm] = useState<FormDataState>(initialFormState);
  const [countries, setCountries] = useState<Country[]>([]);
  const [states, setStates] = useState<State[]>([]);
  const [gstFile, setGstFile] = useState<File | null>(null);
  const [isLoading, setIsLoading] = useState(false);
  const [uploadProgress, setUploadProgress] = useState<number>(0);
  const [tracking, setTracking] = useState<TrackingInfo>({
    ip_address: "",
    user_agent: "",
    page_url: "",
    referrer: "",
    utm_source: "",
    utm_medium: "",
    utm_campaign: "",
    status: "new",
    priority: "medium",
  });

  /* ================= EFFECTS ================= */

  // ✅ Fetch IP + UTM + tracking info
  useEffect(() => {
    if (typeof window === "undefined") return;
    const searchParams = new URLSearchParams(window.location.search);

    const defaultTracking: TrackingInfo = {
      ip_address: "Detecting...",
      user_agent: navigator.userAgent || "",
      page_url: window.location.href,
      referrer: document.referrer || "",
      utm_source: searchParams.get("utm_source") || "",
      utm_medium: searchParams.get("utm_medium") || "",
      utm_campaign: searchParams.get("utm_campaign") || "",
      status: "new",
      priority: "medium",
    };

    setTracking(defaultTracking);

    // 🧩 Fetch public IP
    const fetchIP = async () => {
      try {
        const res = await fetch("https://api.ipify.org?format=json");
        const data = await res.json();
        setTracking((prev) => ({ ...prev, ip_address: data.ip }));
      } catch {
        console.warn("ipify failed, trying ipapi...");
        try {
          const res2 = await fetch("https://ipapi.co/json/");
          const data2 = await res2.json();
          setTracking((prev) => ({ ...prev, ip_address: data2.ip || "Unavailable" }));
        } catch {
          console.error("Both IP fetch failed");
          setTracking((prev) => ({ ...prev, ip_address: "Unavailable" }));
        }
      }
    };
    fetchIP();
  }, []);

  useEffect(() => {
    locationService.fetchCountries().then(setCountries);
  }, []);

  useEffect(() => {
    if (!form.country) return setStates([]);
    locationService.fetchStatesByCountry(form.country).then(setStates);
  }, [form.country]);

  /* ================= HANDLERS ================= */

  const handleChange = (
    e: ChangeEvent<HTMLInputElement | HTMLTextAreaElement>
  ) => {
    const { name, value } = e.target;
    setForm((p) => ({
      ...p,
      [name]:
        name === "gst_number" ? value.toUpperCase().replace(/\s/g, "") : value,
    }));
  };

  const validate = () => {
    if (!form.company_name) return "Company name required";
    if (!form.director_name) return "Contact person required";
    if (!/^\S+@\S+\.\S+$/.test(form.email)) return "Valid email required";
    if (!form.mobile_number) return "mobile_number required";
    if (!form.gst_number) return "GST number required";
    if (!isValidGST(form.gst_number)) return "Invalid GST number";
    if (!form.engineer_name_1 || !form.engineer_number_1)
      return "At least one engineer required";
    if (!gstFile) return "GST certificate required";
    return null;
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    const err = validate();
    if (err) return Swal.fire("Validation Error", err, "warning");

    const payload = new FormData();
    Object.entries(form).forEach(([k, v]) => payload.append(k, v));
    if (gstFile) {
      payload.append("document_file", gstFile);
    }
    
    // Append tracking info
    Object.entries(tracking).forEach(([key, value]) => {
      if (value) payload.append(key, value);
    });

    setIsLoading(true);

    try {
      // Use axiosClient but rely on browser/axios to set Content-Type for FormData
      const res = await axiosClient.post("/partners/register", payload, {
        headers: {
            "Content-Type": "multipart/form-data",
        },
        onUploadProgress: (progressEvent) => {
            const percentCompleted = Math.round(
                (progressEvent.loaded * 100) / (progressEvent.total || 1)
            );
            setUploadProgress(percentCompleted);
        },
      });
      const data = res?.data;

      if (data?.success) {
        const reg = data?.data || {};
        const messageLines = [
          data.message || "Registration successful.",
          reg.registration_id ? `<b>Registration ID:</b> ${reg.registration_id}` : "",
          reg.estimated_response_time
            ? `<b>Expected Response:</b> ${reg.estimated_response_time}`
            : "",
        ].filter(Boolean);

        await Swal.fire({
          icon: "success",
          title: "Registration Submitted 🎉",
          html: `<div style='text-align:left;'>${messageLines.join("<br/>")}</div>`,
          confirmButtonColor: "#f97316",
        });

        // Reset form
        setForm(initialFormState);
        setGstFile(null);
      } else {
        Swal.fire({
          icon: "error",
          title: "Submission Failed",
          text: data?.message || "Something went wrong.",
        });
      }

    } catch (err: unknown) {
      console.error("Submit error:", err);
      
      // Type-safe error handling
      let errorMessage = "Network error. Try again.";
      
      if (typeof err === 'object' && err !== null) {
        // Axios error check
        if ('response' in err) {
          const axiosError = err as { response?: { data?: { message?: string } } };
          errorMessage = axiosError.response?.data?.message || errorMessage;
        } 
        // Generic Error object check
        else if ('message' in err && typeof (err as { message: string }).message === 'string') {
          errorMessage = (err as Error).message;
        }
      }

      Swal.fire({
        icon: "error",
        title: "Submission Failed",
        text: errorMessage,
      });
    } finally {
      setIsLoading(false);
      setUploadProgress(0);
    }
  };

  const countryOptions = countries.map((c) => ({
    value: c.code,
    label: c.name,
  }));
  const stateOptions = states.map((s) => ({
    value: s.code,
    label: s.name,
  }));

  /* ================= RENDER ================= */

  return (
    <Layout>
      <form
        onSubmit={handleSubmit}
        className="max-w-6xl mx-auto py-14 space-y-12 px-4"
      >
        <h1 className="text-3xl sm:text-3xl section-title font-bold text-center">
          Partner Registration
        </h1>
        <p className="text-gray-700 text-center text-sm">
          Fill in the details below to become an official partner of Realtime
          Biometrics.
        </p>

        <Card title="Company Information">
          <Grid>
            <FloatingInput
              name="company_name"
              label="Company Name *"
              value={form.company_name}
              onChange={handleChange}
            />
            <FloatingInput
              name="director_name"
              label="Contact Person *"
              value={form.director_name}
              onChange={handleChange}
            />
            <FloatingInput
              name="email"
              label="Email *"
              value={form.email}
              onChange={handleChange}
            />
            <FloatingInput
              name="mobile_number"
              label="Phone *"
              value={form.mobile_number}
              onChange={handleChange}
            />
          </Grid>
          <FloatingInput
            name="gst_number"
            label="GST Number (GSTIN)"
            value={form.gst_number}
            onChange={handleChange}
            maxLength={15}
          />
          {form.gst_number && !isValidGST(form.gst_number) && (
            <p className="text-xs text-red-500 col-span-2">
              Invalid GST format
            </p>
          )}
        </Card>

        <Card title="Address">
          <Grid>
            <FloatingInput
              name="address"
              label="Address"
              value={form.address}
              onChange={handleChange}
            />
            <FloatingInput
              name="district"
              label="District"
              value={form.district}
              onChange={handleChange}
            />
            <FloatingInput
              name="city"
              label="City"
              value={form.city}
              onChange={handleChange}
            />
            <FloatingInput
              name="pin_code"
              label="Postal Code"
              value={form.pin_code}
              onChange={handleChange}
            />
            <SelectBox
              label="Country"
              options={countryOptions}
              value={
                countryOptions.find((c) => c.value === form.country) || null
              }
              onChange={(o: SingleValue<Option>) =>
                setForm((p) => ({ ...p, country: o?.value || "" }))
              }
            />
            <SelectBox
              label="State"
              options={stateOptions}
              value={stateOptions.find((s) => s.value === form.state) || null}
              onChange={(o: SingleValue<Option>) =>
                setForm((p) => ({ ...p, state: o?.value || "" }))
              }
              isDisabled={!form.country}
            />
          </Grid>
        </Card>

        <Card title="Engineers">
          <Grid>
            <FloatingInput
              name="engineer_name_1"
              label="Engineer 1 *"
              value={form.engineer_name_1}
              onChange={handleChange}
            />
            <FloatingInput
              name="engineer_number_1"
              label="Phone *"
              value={form.engineer_number_1}
              onChange={handleChange}
            />
            <FloatingInput
              name="engineer_name_2"
              label="Engineer 2"
              value={form.engineer_name_2}
              onChange={handleChange}
            />
            <FloatingInput
              name="engineer_number_2"
              label="Phone"
              value={form.engineer_number_2}
              onChange={handleChange}
            />
            <FloatingInput
              name="engineer_name_3"
              label="Engineer 3"
              value={form.engineer_name_3}
              onChange={handleChange}
            />
            <FloatingInput
              name="engineer_number_3"
              label="Phone"
              value={form.engineer_number_3}
              onChange={handleChange}
            />
            <FloatingInput
              name="engineer_name_4"
              label="Engineer 4"
              value={form.engineer_name_4}
              onChange={handleChange}
            />
            <FloatingInput
              name="engineer_number_4"
              label="Phone"
              value={form.engineer_number_4}
              onChange={handleChange}
            />
          </Grid>
        </Card>

        <Card title="Documents">
          <FileUpload
            label="GST Certificate"
            required
            file={gstFile}
            onChange={setGstFile}
            accept={["application/pdf", "image/png", "image/jpeg"]}
            maxSizeMB={5}
            progress={uploadProgress}
          />
        </Card>

        <div className="flex items-start gap-3 select-none">
          <input
            type="checkbox"
            required
            id="consent-checkbox-partner-page"
            className="w-4 h-4 mt-1 accent-orange-500 cursor-pointer"
          />
          <label htmlFor="consent-checkbox-partner-page" className="text-xs text-gray-600 leading-normal cursor-pointer text-left">
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

        <button
          disabled={isLoading}
          className="w-full bg-orange-500 text-white py-3 rounded-xl font-medium"
        >
          {isLoading ? "Submitting..." : "Submit Application"}
        </button>
      </form>
    </Layout>
  );
}

/* ================= UI ================= */

function Card({ title, children }: { title: string; children: ReactNode }) {
  return (
    <div className="bg-white border border-gray-200 text-black shadow rounded-2xl p-6 space-y-4">
      <h2 className="text-lg font-semibold">{title}</h2>
      {children}
    </div>
  );
}

function Grid({ children }: { children: ReactNode }) {
  return <div className="grid md:grid-cols-2 gap-6">{children}</div>;
}

function FloatingInput({ label, ...props }: FloatingInputProps) {
  return (
    <div className="relative">
      <input
        {...props}
        placeholder=" "
        className="peer w-full border border-gray-300 rounded-xl px-4 pt-5 pb-2 focus:border-orange-500 outline-none"
      />
      <label className="absolute left-4 top-2 text-sm text-gray-500 peer-placeholder-shown:top-4 peer-placeholder-shown:text-base peer-focus:top-2 peer-focus:text-sm">
        {label}
      </label>
    </div>
  );
}

function SelectBox({
  label,
  options,
  value,
  onChange,
  placeholder,
  isDisabled,
}: {
  label: string;
  options: Option[];
  value: Option | null;
  onChange: (value: SingleValue<Option>) => void;
  placeholder?: string;
  isDisabled?: boolean;
}) {
  const instanceId = label
    .toLowerCase()
    .replace(/[^a-z0-9]+/g, "-")
    .replace(/(^-|-$)/g, "");

  const menuPortalTarget =
    typeof document !== "undefined" ? document.body : undefined;

  return (
    <div>
      <label className="text-black/60 text-xs mb-1 block">{label}</label>

      <Select
        options={options}
        value={value}
        onChange={onChange}
        isSearchable
        placeholder={placeholder}
        isDisabled={isDisabled}
        instanceId={instanceId}
        inputId={`${instanceId}-input`}
        menuPortalTarget={menuPortalTarget}
        menuPosition="fixed"
        menuShouldScrollIntoView={false}
        className="react-select-container"
        classNames={{
          control: (state) =>
            `border border-gray-300 rounded-xl hover:border-orange-400 transition-all ${
              state.isFocused ? "border-orange-500 ring-0" : ""
            }`,
          option: (state) =>
            `${
              state.isFocused
                ? "bg-orange-50 text-orange-800"
                : "bg-white text-gray-800"
            }`,
        }}
        styles={{
          control: (base) => ({
            ...base,
            padding: "2px",
            borderRadius: "12px",
            boxShadow: "none",
            minHeight: "52px",
          }),
          option: (base, state) => ({
            ...base,
            backgroundColor: state.isFocused ? "#fff7ed" : "white",
            color: state.isFocused ? "#9a3412" : "#1e1410",
          }),
        }}
      />
    </div>
  );
}
