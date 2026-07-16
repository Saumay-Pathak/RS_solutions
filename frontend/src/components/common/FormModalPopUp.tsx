"use client";

import axios from "axios";
import { useEffect, useState, type ChangeEvent, type FormEvent } from "react";
import Select from "react-select";
import locationService, { type Country, type State } from "@/services/locationService";

type HeaderData = {
  status?: {
    custom_activity_tracker?: boolean;
  };
};

type SelectOption = { label: string; value: string };

type FormDataState = {
  phone: string;
  email: string;
  activity: string;
  activity_url: string;
  country: string;
  zipcode: string;
  state: string;
  area: string;
  domain: string;
};

function FormModalPopUp({ headerData }: { headerData?: HeaderData }) {
  const [open, setOpen] = useState(false);

  useEffect(() => {
    const timer = setTimeout(() => setOpen(true), 10000);
    return () => clearTimeout(timer);
  }, []);

  // -----------------------------------------
  // FORM STATE
  // -----------------------------------------
  const [formData, setFormData] = useState<FormDataState>({
    phone: "",
    email: "",
    activity: "",
    activity_url: "",
    country: "",
    zipcode: "",
    state: "",
    area: "",
    domain: "rssolutions",
  });

  // -----------------------------------------
  // COUNTRY + STATE DATA
  // -----------------------------------------
  const [countryList, setCountryList] = useState<SelectOption[]>([]);
  const [stateList, setStateList] = useState<SelectOption[]>([]);
  const [selectedCountry, setSelectedCountry] = useState<SelectOption | null>(null);
  const [selectedState, setSelectedState] = useState<SelectOption | null>(null);

  // -----------------------------------------
  // FETCH COUNTRIES (WORKING API)
  // -----------------------------------------
  useEffect(() => {
    const loadCountries = async () => {
      try {
        const data = await locationService.fetchCountries(); // <-- SAME AS PARTNER PAGE

        const formatted = data.map((c: Country) => ({
          label: c.name,
          value: c.code,
        }));

        setCountryList(formatted);
      } catch (err) {
        console.error("Country Load Failed", err);
      }
    };

    loadCountries();
  }, []);

  // -----------------------------------------
  // COUNTRY CHANGE
  // -----------------------------------------
  const handleCountryChange = async (option: SelectOption | null) => {
    if (!option) return;
    setSelectedCountry(option);
    setSelectedState(null);
    setFormData({ ...formData, country: option.label, state: "" });

    // Fetch states based on country code
    try {
      const states = await locationService.fetchStatesByCountry(option.value); // <--- SAME AS PARTNER PAGE

      const formattedStates = states.map((s: State) => ({
        label: s.name,
        value: s.code,
      }));

      setStateList(formattedStates);
    } catch (error) {
      console.error("State Load Failed", error);
      setStateList([]);
    }
  };

  // -----------------------------------------
  // STATE CHANGE
  // -----------------------------------------
  const handleStateChange = (option: SelectOption | null) => {
    setSelectedState(option);
    setFormData({ ...formData, state: option?.label ?? "" });
  };

  // -----------------------------------------
  // NORMAL INPUT CHANGE
  // -----------------------------------------
  const handleChange = (e: ChangeEvent<HTMLInputElement>) => {
    setFormData({ ...formData, [e.target.name]: e.target.value });
  };

  // -----------------------------------------
  // SUBMIT FORM
  // -----------------------------------------
  const handleSubmit = async (e: FormEvent<HTMLFormElement>) => {
    e.preventDefault();

    try {
      await axios.post(
        "https://markvisitor.com/app/dev/website/saveactivity.php",
        formData
      );

      alert("Submitted Successfully!");
      setOpen(false);
    } catch (err) {
      console.error(err);
      alert("Error submitting form!");
    }
  };

  if (!open || !headerData?.status?.custom_activity_tracker) return null;

  return (
    <div className="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4">
      <div className="bg-white text-black/80 rounded-xl w-full max-w-lg p-6 shadow-lg relative">

        {/* HEADER */}
        <h2 className="text-xl font-semibold mb-5">User Activity Form</h2>
        <button
          className="absolute top-5 right-5 text-black"
          onClick={() => setOpen(false)}
        >
          ✕
        </button>

        {/* FORM */}
        <form onSubmit={handleSubmit} className="space-y-10">
          <div className="grid md:grid-cols-2 gap-6">

            <FloatingField
              name="phone"
              label="Phone"
              value={formData.phone}
              onChange={handleChange}
              required
            />

            <FloatingField
              name="email"
              type="email"
              label="Email"
              value={formData.email}
              onChange={handleChange}
              required
            />

            <FloatingField
              name="activity"
              label="Activity"
              value={formData.activity}
              onChange={handleChange}
            />

            <FloatingField
              name="activity_url"
              label="Activity URL"
              value={formData.activity_url}
              onChange={handleChange}
            />

            {/* COUNTRY DROPDOWN */}
            <div> 
              <Select<SelectOption>
                options={countryList}
                value={selectedCountry}
                onChange={handleCountryChange}
                placeholder="Search Country..."
                instanceId="formmodal-country"
                inputId="formmodal-country-input"
                menuPortalTarget={menuPortalTarget}
                menuPosition="fixed"
                menuShouldScrollIntoView={false}
                className="react-select-container"
        classNames={{
          control: (state) =>
            `border border-gray-300 rounded-xl hover:border-orange-400 focus:border-orange-500 transition-all ${
              state.isFocused ? "border-orange-500 ring-0" : ""
            }`,
          option: (state) =>
            `${
              state.isFocused ? "bg-orange-50 text-orange-800" : "bg-white text-gray-800"
            }`,
        }}
        styles={{
          control: (base) => ({
            ...base,
            padding: "2px",
            borderRadius: "8px",
            boxShadow: "none",
            minHeight: "55px",
          }),
          option: (base, state) => ({
            ...base,
            backgroundColor: state.isFocused ? "#fff7ed" : "white",
            color: state.isFocused ? "#9a3412" : "#1e1410",
            ":active": {
              backgroundColor: "#fed7aa",
            },
          }),
          singleValue: (base) => ({
            ...base,
            color: "#1e1410",
          }),
          placeholder: (base) => ({
            ...base,
            color: "#9ca3af",
          }),
          menuPortal: (base) => ({ ...base, zIndex: 999999 }),
        }}
                
              />
            </div>

            <FloatingField
              name="zipcode"
              label="Zipcode"
              value={formData.zipcode}
              onChange={handleChange}
            />

            {/* STATE DROPDOWN */}
            <div>
              <Select<SelectOption>
                options={stateList}
                value={selectedState}
                onChange={handleStateChange}
                placeholder={
                  selectedCountry ? "Search State..." : "Select Country first"
                }
                isDisabled={!selectedCountry}
                instanceId="formmodal-state"
                inputId="formmodal-state-input"
                menuPortalTarget={menuPortalTarget}
                menuPosition="fixed"
                menuShouldScrollIntoView={false}
                className="react-select-container"
        classNames={{
          control: (state) =>
            `border border-gray-300 rounded-xl hover:border-orange-400 focus:border-orange-500 transition-all ${
              state.isFocused ? "border-orange-500 ring-0" : ""
            }`,
          option: (state) =>
            `${
              state.isFocused ? "bg-orange-50 text-orange-800" : "bg-white text-gray-800"
            }`,
        }}
        styles={{
          control: (base) => ({
            ...base,
            padding: "2px",
            borderRadius: "8px",
            boxShadow: "none",
            minHeight: "55px",
          }),
          option: (base, state) => ({
            ...base,
            backgroundColor: state.isFocused ? "#fff7ed" : "white",
            color: state.isFocused ? "#9a3412" : "#1e1410",
            ":active": {
              backgroundColor: "#fed7aa",
            },
          }),
          singleValue: (base) => ({
            ...base,
            color: "#1e1410",
          }),
          placeholder: (base) => ({
            ...base,
            color: "#9ca3af",
          }),
          menuPortal: (base) => ({ ...base, zIndex: 999999 }),
        }}
              />
            </div>

            <FloatingField
              name="area"
              label="Area"
              value={formData.area}
              onChange={handleChange}
            />

          </div>

          <div className="flex items-start gap-3 select-none">
            <input
              type="checkbox"
              required
              id="consent-checkbox-modal-popup"
              className="w-4 h-4 mt-1 accent-orange-500 cursor-pointer"
            />
            <label htmlFor="consent-checkbox-modal-popup" className="text-xs text-gray-600 leading-normal cursor-pointer text-left">
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
            type="submit"
            className="bg-orange-500 text-white py-3 rounded-md w-full hover:bg-orange-600 transition"
          >
            Submit
          </button>
        </form>
      </div>
    </div>
  );
}

/* ---------------- Floating Input Component ---------------- */

type FloatingFieldProps = {
  label: string;
  name: string;
  value: string;
  onChange: (e: ChangeEvent<HTMLInputElement>) => void;
  type?: string;
  required?: boolean;
};

function FloatingField({
  label,
  name,
  value,
  onChange,
  type = "text",
  required = false,
}: FloatingFieldProps) {
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
        className="peer w-full border border-gray-300 rounded-lg px-4 pt-5 pb-2 text-black focus:border-orange-500 focus:ring-0 outline-none"
      />
      <label
        className={`absolute left-4 bg-white px-1 transition-all duration-200 
          ${
            hasValue
              ? "top-1 text-xs text-gray-500"
              : "top-3.5 text-gray-500 text-sm"
          }
          peer-focus:top-1 peer-focus:text-xs peer-focus:text-orange-500`}
      >
        {label}
      </label>
    </div>
  );
}

export default FormModalPopUp;





  const menuPortalTarget =
    typeof document !== "undefined" ? document.body : undefined;
