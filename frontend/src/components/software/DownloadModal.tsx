"use client";

import { useState, useEffect, useRef } from "react";
import { ArrowDownToLine, X, CheckCircle2 } from "lucide-react";
import axios from "axios";
import { baseUri } from "@/services/constant";

type Software = {
  id: string;
  slug: string;
  title: string;
  version: string;
  file?: string | null;
  external_url?: string | null;
};

type Props = {
  software: Software;
  onClose: () => void;
};

export default function DownloadModal({ software, onClose }: Props) {
  const [step, setStep] = useState<"form" | "otp" | "success">("form");
  const [form, setForm] = useState({ name: "", email: "", phone: "", zip: "" });
  const [otp, setOtp] = useState(["", "", "", ""]);
  const [generatedOtp, setGeneratedOtp] = useState("");
  const [error, setError] = useState(false);
  const [timeLeft, setTimeLeft] = useState(60);
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [otpExpired, setOtpExpired] = useState(false);
  const inputRefs = useRef<(HTMLInputElement | null)[]>([]);

  useEffect(() => {
    let timer: NodeJS.Timeout;
    if (step === "otp" && timeLeft > 0) {
      timer = setTimeout(() => setTimeLeft(timeLeft - 1), 1000);
    } else if (timeLeft === 0) {
      setOtpExpired(true);
    }
    return () => clearTimeout(timer);
  }, [step, timeLeft]);

  const handleFormChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    setForm({ ...form, [e.target.name]: e.target.value });
  };

  const handleOtpChange = (value: string, index: number) => {
    if (!/^[0-9]?$/.test(value)) return;

    const updatedOtp = [...otp];
    updatedOtp[index] = value;
    setOtp(updatedOtp);
    setError(false);

    if (value && index < 3) {
      inputRefs.current[index + 1]?.focus();
    }
  };

  const handleKeyDown = (
    e: React.KeyboardEvent<HTMLInputElement>,
    index: number
  ) => {
    if (e.key === "Backspace" && !otp[index] && index > 0) {
      inputRefs.current[index - 1]?.focus();
    }
  };

  const sendOtp = async () => {
    const newOtp = Math.floor(1000 + Math.random() * 9000).toString();
    setGeneratedOtp(newOtp);

    await axios.post(
      "https://n8n.ria.markvisitor.com/webhook/softwareDownloads",
      {
        name: form.name,
        email: form.email,
        phone: form.phone,
        zip: form.zip || "",
        software: software.title,
        otp: newOtp,
      }
    );

    setStep("otp");
    setTimeLeft(60);
    setOtp(["", "", "", ""]);
    setError(false);
    setOtpExpired(false);
  };

  const handleSendOtp = async () => {
    if (!form.name || !form.phone || !form.zip) {
      alert("Please fill all required fields");
      return;
    }

    try {
      setIsSubmitting(true);
      await sendOtp();
    } catch (err) {
      console.error(err);
      alert("Failed to send OTP. Try again.");
    } finally {
      setIsSubmitting(false);
    }
  };

  const handleVerify = () => {
    const enteredOtp = otp.join("");
    if (otpExpired) {
      alert("OTP expired. Please resend OTP.");
      return;
    }
    if (enteredOtp === generatedOtp) {
      setStep("success");
    } else {
      setError(true);
    }
  };

  const handleDownloadTracking = async () => {
    const base = process.env.NEXT_PUBLIC_API_BASE_URL;
    try {
      await axios.post(`${base}/content/software/${software.slug}/increment-download`);
    } catch (err) {
      console.error("Failed to track download count:", err);
    }
    setTimeout(() => {
      onClose();
    }, 2000);
  };

  return (
    <div className="fixed inset-0 bg-black/40 backdrop-blur-sm flex justify-center items-center z-50 transition-opacity">
      <div className="bg-white w-full max-w-md rounded-3xl shadow-xl p-8 md:p-10 relative animate-fadeIn">
        {/* Header */}
        <div className="flex justify-between items-center mb-6">
          <h3 className="text-2xl font-semibold text-gray-900">
            {step === "success"
              ? "Download Ready"
              : `Download ${software.title}`}
          </h3>
          <button onClick={onClose}>
            <X className="w-6 h-6 text-gray-400 hover:text-gray-700 transition" />
          </button>
        </div>

        {/* FORM STEP */}
        {step === "form" && (
          <div className="space-y-5">
            {["name", "email", "zip", "phone"].map((field) => (
              <div key={field} className="flex flex-col">
                <label className="text-gray-600 text-sm font-medium mb-1 capitalize">
                  {field === "zip"
                    ? "PIN Code*"
                    : field === "phone"
                    ? "Phone Number*"
                    : field === "email"
                    ? "Email"
                    : "Name*"}
                </label>
                <input
                  name={field}
                  type={field === "email" ? "email" : "text"}
                  value={form[field as keyof typeof form]}
                  onChange={handleFormChange}
                  placeholder={`Enter ${
                    field === "zip"
                      ? "PIN Code"
                      : field === "phone"
                      ? "Phone Number"
                      : field === "email"
                      ? "Email (optional)"
                      : "Name"
                  }`}
                  className="w-full px-4 py-2 rounded-lg border border-gray-200 bg-gray-50 focus:ring-2 focus:ring-orange-400 focus:border-orange-500 transition"
                />
              </div>
            ))}

            <div className="flex justify-end gap-3 pt-2">
              <button
                onClick={onClose}
                className="px-5 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-medium transition"
              >
                Cancel
              </button>
              <button
                onClick={handleSendOtp}
                disabled={isSubmitting}
                className="px-5 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 font-medium transition disabled:opacity-50"
              >
                {isSubmitting ? "Sending OTP..." : "Get OTP"}
              </button>
            </div>
          </div>
        )}

        {/* OTP STEP */}
        {step === "otp" && (
          <div className="flex flex-col space-y-4">
            <p className="text-gray-700 font-medium">
              Enter the 4-digit OTP sent to{" "}
              <span className="text-orange-600">{form.phone}</span>
            </p>
            <div className="flex gap-2 mb-4 mx-auto">
              {otp.map((d, i) => (
                <input
                  key={i}
                  ref={(el) => {
                    inputRefs.current[i] = el;
                  }}
                  value={d}
                  onChange={(e) => handleOtpChange(e.target.value, i)}
                  onKeyDown={(e) => handleKeyDown(e, i)}
                  maxLength={1}
                  className={`w-12 h-12 text-black text-center border rounded-lg text-lg ${
                    error || otpExpired ? "border-red-500" : "border-gray-300"
                  }`}
                />
              ))}
            </div>

            <div className="flex justify-between items-center">
              {(error || otpExpired) && (
                <p className="text-red-500 text-sm">
                  {otpExpired ? "OTP expired" : "Incorrect OTP"}
                </p>
              )}
              {!otpExpired && (
                <p className="text-sm text-gray-600">
                  Time left: <span className="font-semibold">{timeLeft}s</span>
                </p>
              )}
              {otpExpired && (
                <button
                  onClick={sendOtp}
                  className="text-orange-600 font-semibold text-sm hover:underline"
                >
                  Resend
                </button>
              )}
            </div>
            <div className="flex justify-end gap-3 pt-2">
              <button
                onClick={onClose}
                className="px-5 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-medium transition"
              >
                Cancel
              </button>
              {!otpExpired && (
                <button
                  onClick={handleVerify}
                  className="px-5 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 font-medium transition"
                >
                  Verify OTP
                </button>
              )}
            </div>
          </div>
        )}

        {/* SUCCESS STEP */}
        {step === "success" && (
          <div className="flex flex-col items-center space-y-4 text-center">
            <CheckCircle2 className="w-16 h-16 text-green-500 animate-bounce" />
            <h2 className="text-xl font-semibold text-green-600">
              ✅ Verification Successful!
            </h2>
            <div className="space-y-1 text-gray-700 text-sm">
              {Object.entries(form).map(([key, value]) => (
                <p key={key}>
                  <span className="font-medium capitalize">{key}:</span> {value}
                </p>
              ))}
            </div>

            <div className="mt-4 flex flex-col sm:flex-row gap-3">
              <button
                onClick={onClose}
                className="px-5 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 font-medium transition"
              >
                Cancel
              </button>

              {software.file ? (
                <a
                  href={`${baseUri}${software.file}`}
                  download
                  onClick={handleDownloadTracking}
                  className="px-5 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 font-medium transition flex items-center justify-center gap-2"
                >
                  DOWNLOAD <ArrowDownToLine className="w-5" />
                </a>
              ) : software.external_url ? (
                <a
                  href={software.external_url}
                  target="_blank"
                  rel="noopener noreferrer"
                  onClick={handleDownloadTracking}
                  className="px-5 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 font-medium transition flex items-center justify-center gap-2"
                >
                  GO TO DOWNLOAD <ArrowDownToLine className="w-5" />
                </a>
              ) : (
                <p>No download available</p>
              )}
            </div>
          </div>
        )}
      </div>
    </div>
  );
}