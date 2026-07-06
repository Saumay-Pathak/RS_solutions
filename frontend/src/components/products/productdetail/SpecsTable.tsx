import React from "react";

type Spec = {
  title: string;
  value: string | number | undefined;
};

interface SpecsTableProps {
  specs?: Spec[] | Record<string, string | number>;
}

export default function SpecsTable({ specs }: SpecsTableProps) {
  const specArray: Spec[] = Array.isArray(specs)
    ? specs
    : specs
    ? Object.entries(specs).map(([title, value]) => ({ title, value }))
    : [];

  return (
    <div className="rounded-xl overflow-hidden border border-[#DDDDDD] w-full mb-4 text-white bg-transparent">
      <div className="divide-y divide-[#DDDDDD]">
        {specArray.map((s, i) => (
          <div
            key={i}
            className="grid grid-cols-[1fr_auto_1fr] items-center text-sm"
          >
            {/* Key */}
            <div className="py-3 px-4 flex items-center gap-2">
              <span className="text-[#1E1410] text-sm font-[400]">
                {s.title.charAt(0).toUpperCase() + s.title.slice(1)}
              </span>
            </div>

            {/* Vertical divider */}
            <div className="h-full w-px bg-[#DDDDDD] mx-auto" />

            {/* Value */}
            <div className="py-3 px-4 text-left text-[#1E1410]">{s.value}</div>
          </div>
        ))}
      </div>
    </div>
  );
}
