"use client";

import React, { useState } from "react";
import Footer from "./Footer";
import Header from "./Header";
interface LayoutProps {
  children: React.ReactNode;
}

const Layout = ({ children }: LayoutProps) => {
  const [isMegaMenuOpen, setIsMegaMenuOpen] = useState(false);
  return (
    <div className="flex flex-col min-h-screen">
      <Header setIsMegaMenuOpen={setIsMegaMenuOpen}  />
      <main className={`flex-grow page-main transition-all duration-300 ease-in-out bg-white
          ${isMegaMenuOpen ? "blur-sm brightness-80 bg-black/40" : ""}`}>{children}</main>
      <Footer />
    </div>
  );
};

export default Layout;
