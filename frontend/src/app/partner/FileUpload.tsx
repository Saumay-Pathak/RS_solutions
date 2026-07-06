"use client";

import { useRef, useState, DragEvent, useEffect, KeyboardEvent } from "react";
import Image from "next/image";

interface FileUploadProps {
  label: string;
  required?: boolean;
  file: File | null;
  onChange: (file: File | null) => void;
  accept: string[];
  maxSizeMB: number;
  progress?: number; // optional upload progress (0–100)
}

export default function FileUpload({
  label,
  required = false,
  file,
  onChange,
  accept,
  maxSizeMB,
  progress,
}: FileUploadProps) {
  const inputRef = useRef<HTMLInputElement>(null);
  const [isDragging, setIsDragging] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [previewUrl, setPreviewUrl] = useState<string | null>(null);

  /* ================= VALIDATION ================= */

  const validateFile = (file: File) => {
    if (!accept.includes(file.type)) {
      return "Unsupported file format";
    }
    if (file.size > maxSizeMB * 1024 * 1024) {
      return `File must be smaller than ${maxSizeMB}MB`;
    }
    return null;
  };

  /* ================= HANDLERS ================= */

  const handleFile = (file: File) => {
    const validationError = validateFile(file);
    if (validationError) {
      setError(validationError);
      return;
    }

    setError(null);
    onChange(file);
  };

  const handleDrop = (e: DragEvent<HTMLDivElement>) => {
    e.preventDefault();
    setIsDragging(false);

    const droppedFile = e.dataTransfer.files?.[0];
    if (droppedFile) handleFile(droppedFile);
  };

  const handleKeyDown = (e: KeyboardEvent<HTMLDivElement>) => {
    if (e.key === "Enter" || e.key === " ") {
      e.preventDefault();
      inputRef.current?.click();
    }
  };

  /* ================= PREVIEW ================= */

  useEffect(() => {
    if (file && file.type.startsWith("image/")) {
      const url = URL.createObjectURL(file);
      setPreviewUrl(url);

      return () => URL.revokeObjectURL(url);
    }
    setPreviewUrl(null);
  }, [file]);

  /* ================= RENDER ================= */

  return (
    <div className="space-y-2">
      {/* Label */}
      <label className="text-sm font-medium">
        {label} {required && <span className="text-orange-500">*</span>}
      </label>

      {/* Dropzone */}
      <div
        role="button"
        tabIndex={0}
        aria-label={`Upload ${label}`}
        onKeyDown={handleKeyDown}
        onClick={() => inputRef.current?.click()}
        onDragOver={(e) => {
          e.preventDefault();
          setIsDragging(true);
        }}
        onDragLeave={() => setIsDragging(false)}
        onDrop={handleDrop}
        className={`relative rounded-xl border-2 border-dashed p-6 text-center cursor-pointer transition-all duration-200 ease-out
          ${
            isDragging
              ? "border-orange-500 bg-orange-50 scale-[1.02]"
              : error
              ? "border-red-400 bg-red-50"
              : "border-gray-300 hover:border-orange-400"
          }
        `}
      >
        <input
          ref={inputRef}
          type="file"
          hidden
          accept={accept.join(",")}
          onChange={(e) => e.target.files && handleFile(e.target.files[0])}
        />

        {/* Empty State */}
        {!file && (
          <>
            <p className="text-sm font-medium">
              Drag & drop or click to upload
            </p>
            <p className="text-xs text-gray-500 mt-1">
              {accept.map((t) => t.split("/")[1]?.toUpperCase()).join(", ")} •
              Max {maxSizeMB}MB
            </p>
          </>
        )}

        {/* File State */}
        {file && (
          <div className="space-y-2">
            <p className="text-sm font-medium text-green-700 truncate">
              {file.name}
            </p>
            <p className="text-xs text-gray-500">
              {(file.size / 1024 / 1024).toFixed(2)} MB
            </p>

            <button
              type="button"
              onClick={(e) => {
                e.stopPropagation();
                onChange(null);
              }}
              className="text-xs text-red-500 underline"
            >
              Remove file
            </button>
          </div>
        )}

        {/* Progress */}
        {typeof progress === "number" && progress > 0 && (
          <div className="absolute bottom-2 left-2 right-2">
            <div className="w-full bg-gray-200 rounded-full h-2">
              <div
                className="bg-orange-500 h-2 rounded-full transition-all"
                style={{ width: `${progress}%` }}
              />
            </div>
          </div>
        )}
      </div>

      {/* Image Preview */}
      {previewUrl && (
        <div className="relative h-40 w-full">
          <Image
            src={previewUrl}
            alt="Preview"
            fill
            className="rounded-lg border object-contain"
          />
        </div>
      )}

      {/* Error */}
      {error && <p className="text-xs text-red-500">{error}</p>}
    </div>
  );
}
