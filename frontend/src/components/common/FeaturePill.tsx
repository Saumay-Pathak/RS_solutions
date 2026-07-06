import React from "react";

type FeaturePillProps = {
  icon?: React.ReactNode;
  label: string;
};

export default function FeaturePill({ icon, label }: FeaturePillProps) {
  return (
    <div className="flex items-center gap-2 bg-white/80 ring-1 ring-gray-100 p-2 rounded-full shadow-sm">
      <div className="w-8 h-8 flex items-center justify-center bg-[#BEF4BE] rounded-full text-[#0E890E] text-sm">
        {icon ?? "✓"}
      </div>
      <span className="text-sm w-[50%] text-nowrap font-[500] text-[#1E1410]">
        {label}
      </span>
    </div>
  );
}
