import React from "react";

type Props = {
  title: string;
  subtitle?: string;
};

const Title = ({ title, subtitle }: Props) => {
  return (
    <div className="bg-white">
      <div className="max-w-7xl mx-auto px-4 py-6 md:py-8 text-center">
        <h1 className="section-title-long text-3xl font-bold">{title}</h1>
        {subtitle && (
          <p className="section-subtitle text-sm">{subtitle}</p>
        )}
      </div>
    </div>
  );
};

export default Title;
