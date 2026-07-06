import React from "react";
import Image from "next/image";

// You can replace 'your-fingerprint-image-url.jpg' with the actual path or URL of your image
const FINGERPRINT_IMAGE_URL = "/images/image.png";

const AboutRealtimeBiometrics: React.FC = () => {
  return (
    <div className="flex justify-center items-center bg-white px-0 p-2 sm:p-8">
      {/* Card Container */}
      <div
        className="
          flex flex-col md:flex-row 
          max-w-6xl w-full 
          bg-white 
          rounded-2xl 
          shadow-xl 
          overflow-hidden
        ">
        {/* Image Section */}
        <div
          className="
            relative 
            flex-shrink-0 
            md:w-5/12 
            lg:w-2/5 
            h-64 md:h-auto 
            bg-gray-800 
            rounded-t-2xl md:rounded-l-2xl md:rounded-t-none
          ">
          <Image
            src={FINGERPRINT_IMAGE_URL}
            alt="Fingerprint scan"
            width={500}
            height={300}
            priority
            className="w-full h-full object-cover"
          />
        </div>

        {/* Text Content Section */}
        <div
          className="
            flex-1 
            p-6 sm:p-10 lg:p-12 
            flex flex-col justify-center
          ">
          {/* Title */}
          <h2 className="section-title mb-2 lg:mb-6">About R S Solutions - Realtime Biometrics</h2>

          {/* Text Paragraphs (Flex container for side-by-side on desktop) */}
          <div className="flex flex-col lg:flex-row lg:gap-6">
            {/* Main Text */}
            <p className="text-base lg:text-lg text-[#1E1410] lg:w-2/3 leading-relaxed">
              This is a growing market. Security incidents in schools grab the
              headlines, emotions and budget allocations. How to address
              security concerns leaves room for many opinions and strategies.
            </p>

            {/* Sidebar Text */}
            <p className="text-[14px] text-base text-gray-500 lg:w-1/3 lg:text-[14px] lg:pl-4 py-1">
              Relationship selling past experience with school districts and
              government sourcing requirements make this opportunity a complex
              environment to grow your business.
            </p>
          </div>
        </div>
      </div>
    </div>
  );
};

export default AboutRealtimeBiometrics;
