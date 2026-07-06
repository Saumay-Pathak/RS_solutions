"use client"
import React, { useEffect, useState } from 'react';
import Image from 'next/image';
import { motion } from 'framer-motion';
import './services-design.css';
import axiosClient from '@/services/axiosClient';
import { baseUri } from '@/services/constant';

// API service type
type Service = {
  id: string;
  title: string;
  slug: string;
  short_description: string;
  description: string;
  image?: string | null;
  sort_order?: number;
  hide_from_homepage?: boolean;
};

const ServicesSections = () => {
  const [services, setServices] = useState<Service[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchServices = async () => {
      try {
        setLoading(true);
        const res = await axiosClient.get('/content/services?status=1');
        const data = res.data;
        if (data?.success && Array.isArray(data?.data)) {
          const sorted = [...data.data].sort(
            (a: Service, b: Service) => (a.sort_order ?? 0) - (b.sort_order ?? 0)
          );
          setServices(sorted);
        } else {
          setServices([]);
        }
      } catch (err) {
        console.error('Error fetching services:', err);
        setServices([]);
      } finally {
        setLoading(false);
      }
    };
    fetchServices();
  }, []);

  const placeholders = ['/images/serviceImg1.png','/images/serviceImg2.png','/images/serviceImg3.png','/images/serviceImg4.png'];

  return (
    <section className="services-section">
      <div className="container">
        {/* Header */}
        <div className="services-header text-center">
          <h2 className="services-title-long section-title font-bold text-2xl sm:text-3xl">Our Services</h2>
          <p className="services-subtitle section-subtitle text-sm">Comprehensive digital solutions engineered for impact</p>
        </div>

        {/* Services Grid */}
        <div className="services-grid">
          {loading && (
            <div className="col-span-full flex justify-center py-6">
              <div className="animate-spin rounded-full h-6 w-6 border-b-2 border-orange-500"></div>
            </div>
          )}
          {!loading && services.length === 0 && (
            <div className="col-span-full text-center text-gray-600 py-6">
              No services available at the moment.
            </div>
          )}
          {!loading && services.map((srv, index) => (
            <motion.a
              key={srv.id}
              className={`service-card service-card-${index + 1}`}
              initial={{ opacity: 0, y: 24 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true, amount: 0.2 }}
              transition={{ duration: 0.5, ease: 'easeOut' }}
              href={`/services/${srv.slug}`}
              target="_blank"
              rel="noopener noreferrer"
            >
              <div className="service-img-wrapper">
                <Image
                  src={srv.image ? `${baseUri}${srv.image}` : placeholders[index % placeholders.length]}
                  alt={srv.title}
                  width={800}
                  height={600}
                  className="service-image"
                  unoptimized
                />
                <div className="service-overlay" />
                <div className="service-content">
                  <h3 className="service-title">{srv.title}</h3>
                  <p className="service-description">{srv.short_description || srv.description}</p>
                  <div className="service-arrow">
                    <span className="read-more-text">Read More</span>
                  </div>
                </div>
              </div>
            </motion.a>
          ))}
        </div>
      </div>
    </section>
  );
};

export default ServicesSections;