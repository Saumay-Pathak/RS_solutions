import React from 'react';
import Layout from "@/components/layout/Layout";
import AdvancedBreadcrumb from "@/components/common/Bredacrumb";
import { careerService } from '@/services/careerService';
import { Metadata } from 'next';
import { notFound } from 'next/navigation';
import { Calendar, MapPin, Briefcase, Clock, Share2, Mail, ArrowLeft, Send, CheckCircle2 } from 'lucide-react';
import Link from 'next/link';

interface PageProps {
  params: Promise<{ id: string }>;
}

export async function generateMetadata({ params }: PageProps): Promise<Metadata> {
  const { id } = await params;
  try {
    const response = await careerService.getJobOpeningById(id);
    if (response.success) {
      return {
        title: `${response.data.title} | Careers | Realtime Biometrics`,
        description: response.data.description.replace(/<[^>]*>?/gm, '').substring(0, 160),
      };
    }
  } catch (error) {
    console.error(error);
  }
  return {
    title: 'Job Opening | Realtime Biometrics',
  };
}

const JobDetailPage = async ({ params }: PageProps) => {
  const { id } = await params;
  let job;

  try {
    const response = await careerService.getJobOpeningById(id);
    if (response.success) {
      job = response.data;
    }
  } catch (error) {
    console.error('Error fetching job details:', error);
  }

  if (!job) {
    notFound();
  }

  const breadcrumbItems = [
    { label: "Home", href: "/" },
    { label: "Careers", href: "/careers" },
    { label: job.title, href: `/careers/${id}` },
  ];

  const handleApplyNow = `mailto:career@realtimebiometrics.com?subject=${encodeURIComponent(`Application for ${job.title}`)}&body=${encodeURIComponent(`Dear Hiring Team,\n\nI am writing to apply for the ${job.title} position.\n\nBest regards.`)}`;

  return (
    <Layout>
      <div className="bg-[#fcfcfd] min-h-screen pb-20">
        <div className="bg-white border-b border-gray-100">
           <AdvancedBreadcrumb items={breadcrumbItems} />
        </div>
        
        {/* Hero Section */}
        <div className="relative overflow-hidden bg-white pt-16 pb-12 border-b border-gray-100">
          <div className="absolute top-0 right-0 -translate-y-1/2 translate-x-1/4 w-[500px] h-[500px] bg-orange-50 rounded-full blur-3xl opacity-50" />
          
          <div className="container mx-auto px-4 relative z-10">
            <div className="max-w-5xl mx-auto">
              <Link 
                href="/careers" 
                className="inline-flex items-center gap-2 text-gray-500 hover:text-orange-600 transition-colors mb-8 group"
              >
                <ArrowLeft size={18} className="group-hover:-translate-x-1 transition-transform" />
                <span className="font-medium">Back to All Jobs</span>
              </Link>

              <div className="flex flex-col md:flex-row md:items-center justify-between gap-8">
                <div className="flex-1">
                  <div className="flex flex-wrap items-center gap-3 mb-6">
                    <span className="px-4 py-1.5 bg-orange-50 text-orange-600 rounded-full text-xs font-bold uppercase tracking-wider border border-orange-100">
                      {job.employment_type}
                    </span>
                    {job.is_active && (
                      <span className="px-4 py-1.5 bg-emerald-50 text-emerald-600 rounded-full text-xs font-bold uppercase tracking-wider border border-emerald-100 flex items-center gap-2">
                        <span className="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                        Active
                      </span>
                    )}
                  </div>
                  
                  <h1 className="text-4xl md:text-5xl font-extrabold text-gray-900 mb-6 tracking-tight leading-tight">
                    {job.title}
                  </h1>
                  
                  <div className="flex flex-wrap items-center gap-x-8 gap-y-4 text-gray-600">
                    <div className="flex items-center gap-2.5">
                      <div className="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center text-orange-600">
                        <MapPin size={18} />
                      </div>
                      <span className="font-medium">{job.location}</span>
                    </div>
                    
                    <div className="flex items-center gap-2.5">
                      <div className="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center text-orange-600">
                        <Calendar size={18} />
                      </div>
                      <span className="font-medium whitespace-nowrap">
                        Posted on {new Date(job.created_at).toLocaleDateString('en-IN', { 
                          month: 'long', 
                          day: 'numeric',
                          year: 'numeric'
                        })}
                      </span>
                    </div>
                  </div>
                </div>

                <div className="flex flex-col sm:flex-row items-center gap-4 shrink-0 h-fit md:self-end">
                  <a 
                    href={handleApplyNow}
                    className="inline-flex items-center justify-center gap-3 bg-orange-600 hover:bg-orange-700 text-white font-bold h-14 px-10 rounded-2xl transition-all shadow-xl shadow-orange-200 hover:scale-[1.02] active:scale-[0.98] whitespace-nowrap"
                  >
                    Apply Now
                    <Send size={18} />
                  </a>
                  <button className="inline-flex items-center justify-center bg-white border border-gray-200 rounded-2xl font-bold text-gray-600 hover:bg-gray-50 transition-all h-14 w-14 shrink-0">
                    <Share2 size={20} />
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div className="container mx-auto px-4 py-16">
          <div className="max-w-5xl mx-auto">
            <div className="grid grid-cols-1 lg:grid-cols-12 gap-12">
              {/* Main Content */}
              <div className="lg:col-span-8">
                <div className="bg-white rounded-[2rem] border border-gray-100 p-8 md:p-12 shadow-sm">
                  <h2 className="text-2xl font-bold text-gray-900 mb-10 flex items-center gap-4">
                    <div className="w-1.5 h-8 bg-orange-600 rounded-full" />
                    Job Description
                  </h2>
                  
                  <div 
                    className="prose max-w-none"
                    dangerouslySetInnerHTML={{ __html: job.description }}
                  />

                  <div className="mt-16 pt-10 border-t border-gray-100">
                    <h3 className="text-xl font-bold text-gray-900 mb-6">Why join Realtime Biometrics?</h3>
                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                      {[
                        "Innovative & Fast-paced Environment",
                        "Cutting-edge Technology Stack",
                        "Health & Wellness Benefits",
                        "Continuous Learning Opportunities",
                        "Collaborative Culture",
                        "Flexible Working Hours"
                      ].map((benefit, i) => (
                        <div key={i} className="flex items-center gap-3 text-gray-700">
                          <CheckCircle2 className="text-emerald-500 shrink-0" size={20} />
                          <span className="font-medium text-sm">{benefit}</span>
                        </div>
                      ))}
                    </div>
                  </div>
                </div>
              </div>

              {/* Sidebar */}
              <div className="lg:col-span-4 space-y-8">
                <div className="bg-gray-900 rounded-[2rem] p-8 text-white shadow-2xl shadow-gray-200 sticky top-8">
                  <h3 className="text-xl font-bold mb-8">Role Overview</h3>
                  
                  <div className="space-y-8">
                    <div className="flex gap-4">
                      <div className="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center shrink-0">
                        <Clock className="text-orange-400" size={20} />
                      </div>
                      <div>
                        <p className="text-gray-400 text-xs font-bold uppercase tracking-wider mb-1">Employment Type</p>
                        <p className="font-semibold">{job.employment_type}</p>
                      </div>
                    </div>

                    <div className="flex gap-4">
                      <div className="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center shrink-0">
                        <MapPin className="text-orange-400" size={20} />
                      </div>
                      <div>
                        <p className="text-gray-400 text-xs font-bold uppercase tracking-wider mb-1">Location</p>
                        <p className="font-semibold">{job.location}</p>
                      </div>
                    </div>

                    <div className="flex gap-4">
                      <div className="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center shrink-0">
                        <Mail className="text-orange-400" size={20} />
                      </div>
                      <div>
                        <p className="text-gray-400 text-xs font-bold uppercase tracking-wider mb-1">Contact Email</p>
                        <p className="font-semibold text-sm truncate">career@realtimebiometrics.com</p>
                      </div>
                    </div>

                    <div className="pt-6">
                       <a 
                        href={handleApplyNow}
                        className="w-full inline-flex items-center justify-center gap-2 bg-white text-gray-900 font-bold py-4 px-6 rounded-xl hover:bg-orange-500 hover:text-white transition-all group"
                      >
                        Apply for this position
                        <ArrowLeft size={18} className="rotate-180 group-hover:translate-x-1 transition-transform" />
                      </a>
                    </div>
                  </div>
                </div>

                <div className="bg-orange-50 rounded-[2rem] p-8 border border-orange-100">
                  <h4 className="font-bold text-orange-900 mb-2">Need help?</h4>
                  <p className="text-orange-800/70 text-sm mb-6">Our recruiting team is here to support you throughout the process.</p>
                  <Link href="/contact" className="text-orange-600 font-bold text-sm hover:underline flex items-center gap-1">
                    Contact Recruitment <ArrowLeft size={14} className="rotate-180" />
                  </Link>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </Layout>
  );
};

export default JobDetailPage;
