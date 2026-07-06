"use client";
import { baseUri } from "@/services/constant";

export interface ServiceDetailProps {
  service: {
    id: string;
    slug: string;
    title: string;
    description?: string;
    image?: string | null;
    short_description?: string;
  } | null;
  error?: string | null;
}

export default function ServiceDetailClient({ service, error }: ServiceDetailProps) {
  if (error) {
    return (
      <div className="min-h-[50vh] flex items-center justify-center">
        <p className="text-red-600">{error}</p>
      </div>
    );
  }

  if (!service) {
    return (
      <div className="min-h-[50vh] flex items-center justify-center">
        <div className="text-center">
          <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-gray-900 mx-auto"></div>
          <p className="mt-4 text-gray-600">Loading service...</p>
        </div>
      </div>
    );
  }

  const imgSrc = service.image ? `${baseUri}${service.image}` : undefined;

  return (
    <section className="mb-10">
      <div className="container mx-auto px-6">
        <div className="mb-8 text-center">
          <h1 className="section-title text-3xl font-bold">{service.title}</h1>
          {service.short_description && (
            <p className="section-subtitle text-sm mx-auto">{service.short_description}</p>
          )}
        </div>

        {imgSrc && (
          <div className="mb-8 flex justify-center">
            {/* eslint-disable-next-line @next/next/no-img-element */}
            <img
              src={imgSrc}
              alt={service.title}
              className="rounded-lg shadow-md max-h-[360px] w-auto"
            />
          </div>
        )}

        {service.description && (
          <div className="prose max-w-4xl mx-auto">
            <div dangerouslySetInnerHTML={{ __html: service.description }} />
          </div>
        )}
      </div>
    </section>
  );
}