"use client";
import { Fingerprint, Clock3, BarChart2, Users, Shield, Globe } from "lucide-react";
import { useEffect, useState } from "react";

type Feature = {
  Icon: React.ComponentType<{ className?: string }>;
  title: string;
  description: string;
};

const features: Feature[] = [
  {
    Icon: Fingerprint,
    title: "Fingerprint & Face Recognition",
    description:
      "Highly accurate biometric authentication for secure access and attendance management.",
  },
  {
    Icon: Clock3,
    title: "Real-time Attendance",
    description:
      "Instant monitoring of workforce attendance with notifications and reporting.",
  },
  {
    Icon: BarChart2,
    title: "Advanced Analytics",
    description:
      "Data-driven insights on workforce trends, productivity, and operational efficiency.",
  },
  {
    Icon: Users,
    title: "HR & Payroll Integration",
    description:
      "Seamless connection with HRM systems to automate payroll, leave, and workforce management.",
  },
  {
    Icon: Shield,
    title: "Security & Compliance",
    description:
      "Enterprise-grade data protection with GDPR compliance and encrypted biometric storage.",
  },
  {
    Icon: Globe,
    title: "Multi-location Management",
    description:
      "Centralized control for offices across multiple regions and time zones with consistent policies.",
  },
];

export default function FeaturesSection() {
  const [mounted, setMounted] = useState(false);

  useEffect(() => {
    setMounted(true);
  }, []);

  return (
    <section id="features" className="features-section py-12 bg-gray-100">
      <div className="container mx-auto px-4 py-15">
        {/* Header */}
        <div className="text-center mb-12">
          <h2 className="section-title-long text-2xl sm:text-3xl font-bold mb-2">
            Features that we are built on
          </h2>
          <p className="section-subtitle text-gray-600 text-sm max-w-2xl mx-auto">
            Realtime Biometrics provides comprehensive solutions for secure and efficient workforce management
          </p>
        </div>

        {/* Grid */}
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
          {features.map(({ Icon, title, description }, idx) => (
            <div
              key={idx}
              className={`feature-item relative rounded-xl bg-white p-6 shadow-md hover:shadow-xl transition-shadow duration-300 cursor-pointer ${
                mounted ? "fade-in" : "opacity-0"
              }`}
              style={{ transitionDelay: `${idx * 100}ms` }}
            >
              <div className="feature-circle w-14 h-14 rounded-full bg-orange-50 flex items-center justify-center mb-4 relative">
                <span className="feature-circle-accent absolute inset-0 rounded-full bg-orange-200 opacity-20 animate-ping" aria-hidden="true"></span>
                <Icon className="feature-circle-icon w-6 h-6 text-orange-500 relative z-10" />
              </div>
              <h3 className="feature-item-title text-black text-lg font-semibold mb-2">{title}</h3>
              <p className="feature-item-desc text-gray-600 text-sm">{description}</p>
            </div>
          ))}
        </div>
      </div>

      <style jsx>{`
        .fade-in {
          opacity: 1;
          transform: translateY(0);
          transition: opacity 0.6s ease-out, transform 0.6s ease-out;
        }
        .feature-item.opacity-0 {
          opacity: 0;
          transform: translateY(20px);
        }
        .feature-circle-accent {
          z-index: 0;
        }
      `}</style>
    </section>
  );
}
