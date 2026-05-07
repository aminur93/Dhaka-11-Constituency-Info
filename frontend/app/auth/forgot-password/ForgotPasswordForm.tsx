"use client";

import { useState } from "react";
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { z } from "zod";
import { ArrowLeft, Mail, SendHorizonal } from "lucide-react";
import Link from "next/link";

const forgotSchema = z.object({
  email: z.string().min(1, "Email address is required").email("Please enter a valid email address"),
});

type ForgotFormData = z.infer<typeof forgotSchema>;

export default function ForgotPasswordForm() {
  const [submitted, setSubmitted] = useState(false);
  const [submittedEmail, setSubmittedEmail] = useState("");

  const {
    register,
    handleSubmit,
    formState: { errors, isSubmitting },
  } = useForm<ForgotFormData>({
    resolver: zodResolver(forgotSchema),
  });

  const onSubmit = async (data: ForgotFormData) => {
    await new Promise((r) => setTimeout(r, 1200));
    setSubmittedEmail(data.email);
    setSubmitted(true);
  };

  return (
    <div className="min-h-screen w-full flex items-center justify-center relative overflow-hidden bg-[#f5f4ff] px-6 py-12">

      {/* Background blobs */}
      <div className="absolute top-[-120px] right-[-120px] w-[500px] h-[500px] rounded-full bg-violet-200/50 blur-[110px] pointer-events-none" />
      <div className="absolute bottom-[-100px] left-[-100px] w-[420px] h-[420px] rounded-full bg-purple-200/40 blur-[100px] pointer-events-none" />
      <div className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[300px] rounded-full bg-indigo-100/30 blur-[80px] pointer-events-none" />

      {/* Dot pattern */}
      <div
        className="absolute inset-0 opacity-[0.3] pointer-events-none"
        style={{
          backgroundImage: "radial-gradient(circle, #a78bfa 1px, transparent 1px)",
          backgroundSize: "30px 30px",
        }}
      />

      {/* Card */}
      <div className="relative z-10 w-full max-w-[420px]">
        <div className="bg-white rounded-3xl shadow-2xl shadow-violet-100/80 border border-violet-100 p-8 md:p-10">

          {!submitted ? (
            <>
              {/* Icon */}
              <div className="flex justify-center mb-6">
                <div className="w-14 h-14 rounded-2xl bg-gradient-to-br from-violet-500 to-purple-600 flex items-center justify-center shadow-lg shadow-violet-300/40">
                  <Mail className="w-7 h-7 text-white" />
                </div>
              </div>

              {/* Heading */}
              <h1 className="text-[22px] font-bold text-slate-800 text-center mb-2 tracking-tight">
                Forgot your password?
              </h1>
              <p className="text-sm text-slate-400 text-center mb-8 leading-relaxed">
                No worries! Enter your email address and we&apos;ll send you a link to reset your password.
              </p>

              {/* Form */}
              <form onSubmit={handleSubmit(onSubmit)} noValidate className="space-y-5">
                <div>
                  <label className="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-wide">
                    Email address
                  </label>
                  <div className="relative">
                    <Mail className="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
                    <input
                      {...register("email")}
                      type="email"
                      placeholder="you@example.com"
                      autoComplete="email"
                      autoFocus
                      className={`w-full rounded-xl pl-10 pr-4 py-3 text-sm text-slate-800 placeholder-slate-300
                        bg-slate-50 border outline-none transition-all duration-200
                        focus:ring-2 focus:ring-violet-400/40 focus:border-violet-400 focus:bg-white
                        ${errors.email
                          ? "border-red-400 bg-red-50/60 focus:ring-red-300/40 focus:border-red-400"
                          : "border-slate-200 hover:border-slate-300"
                        }`}
                    />
                  </div>
                  {errors.email && (
                    <p className="mt-1.5 text-xs text-red-500">⚠ {errors.email.message}</p>
                  )}
                </div>

                {/* Submit */}
                <button
                  type="submit"
                  disabled={isSubmitting}
                  className="w-full py-3 rounded-xl text-sm font-semibold text-white
                    bg-gradient-to-r from-violet-600 to-purple-600
                    hover:from-violet-500 hover:to-purple-500
                    active:scale-[0.98] transition-all duration-200
                    shadow-lg shadow-violet-300/50
                    flex items-center justify-center gap-2
                    disabled:opacity-60 disabled:cursor-not-allowed"
                >
                  {isSubmitting ? (
                    <>
                      <svg className="w-4 h-4 animate-spin" viewBox="0 0 24 24" fill="none">
                        <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4" />
                        <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z" />
                      </svg>
                      Sending…
                    </>
                  ) : (
                    <>
                      <SendHorizonal className="w-4 h-4" />
                      Send reset link
                    </>
                  )}
                </button>
              </form>

              {/* Back to login */}
              <div className="mt-6 flex justify-center">
                <Link
                  href="/auth/login"
                  className="flex items-center gap-1.5 text-sm text-slate-400 hover:text-violet-600 transition-colors"
                >
                  <ArrowLeft className="w-4 h-4" />
                  Back to sign in
                </Link>
              </div>
            </>
          ) : (
            /* ── Success State ── */
            <>
              <div className="flex justify-center mb-6">
                <div className="w-16 h-16 rounded-full bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center shadow-lg shadow-emerald-200/60">
                  <svg className="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2.5}>
                    <path strokeLinecap="round" strokeLinejoin="round" d="M5 13l4 4L19 7" />
                  </svg>
                </div>
              </div>

              <h1 className="text-[22px] font-bold text-slate-800 text-center mb-2 tracking-tight">
                Check your email!
              </h1>
              <p className="text-sm text-slate-400 text-center mb-2 leading-relaxed">
                We&apos;ve sent a password reset link to
              </p>
              <p className="text-sm font-semibold text-violet-600 text-center mb-8 break-all">
                {submittedEmail}
              </p>

              <div className="bg-violet-50 border border-violet-100 rounded-2xl p-4 mb-6">
                <p className="text-xs text-slate-500 text-center leading-relaxed">
                  Didn&apos;t receive the email? Check your spam folder or{" "}
                  <button
                    onClick={() => setSubmitted(false)}
                    className="text-violet-600 font-semibold hover:text-violet-700 transition-colors"
                  >
                    try again
                  </button>
                  .
                </p>
              </div>

              <div className="flex justify-center">
                <Link
                  href="/auth/login"
                  className="flex items-center gap-1.5 text-sm text-slate-400 hover:text-violet-600 transition-colors"
                >
                  <ArrowLeft className="w-4 h-4" />
                  Back to sign in
                </Link>
              </div>
            </>
          )}
        </div>
      </div>
    </div>
  );
}