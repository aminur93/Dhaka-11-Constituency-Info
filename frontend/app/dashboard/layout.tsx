"use client";

import { useState, useEffect } from "react";
import Sidebar from "@/components/layout/dashboard/Sidebar";
import Header from "@/components/layout/dashboard/Header";
import Footer from "@/components/layout/dashboard/Footer";
import { ArrowUp } from "lucide-react";

export default function DashboardLayout({ children }: { children: React.ReactNode }) {
  const [mobileOpen, setMobileOpen] = useState(false);
  const [showTop, setShowTop]       = useState(false);

  useEffect(() => {
    const el = document.getElementById("main-scroll");
    if (!el) return;
    const handleScroll = () => setShowTop(el.scrollTop > 100);
    el.addEventListener("scroll", handleScroll);
    return () => el.removeEventListener("scroll", handleScroll);
  }, []);

  const scrollToTop = () => {
    document.getElementById("main-scroll")?.scrollTo({ top: 0, behavior: "smooth" });
  };

  return (
    <div className="flex h-screen overflow-hidden bg-slate-50">
      {mobileOpen && (
        <div className="fixed inset-0 z-30 bg-black/50 lg:hidden"
          onClick={() => setMobileOpen(false)} />
      )}

      <Sidebar mobileOpen={mobileOpen} onMobileClose={() => setMobileOpen(false)} />

      <div className="flex flex-col flex-1 min-w-0 overflow-y-auto" id="main-scroll">
        <Header onMobileMenuOpen={() => setMobileOpen(true)} />
        <main className="flex-1" style={{ paddingLeft: "16px" }}>
          {children}
        </main>
        <Footer />
      </div>

      {/* ── Scroll to Top ── */}
      {showTop && (
        <button
          onClick={scrollToTop}
          className="fixed bottom-20 right-6 z-50 w-10 h-10 flex items-center justify-center
            rounded-full bg-violet-600/50 hover:bg-violet-600 text-white
            shadow-lg shadow-violet-300/50 transition-all duration-200
            cursor-pointer active:scale-95">
          <ArrowUp className="w-4 h-4" />
        </button>
      )}
    </div>
  );
}