"use client";

import { useState } from "react";
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { z } from "zod";
import { useSearchParams, useRouter } from "next/navigation";
import { ArrowLeft, KeyRound, Eye, EyeOff } from "lucide-react";
import Link from "next/link";
import { useResetPasswordMutation } from "@/store/features/auth/authApi";

const resetSchema = z.object({
  password: z.string().min(8, "Password must be at least 8 characters"),
  password_confirmation: z.string().min(1, "Please confirm your password"),
}).refine((data) => data.password === data.password_confirmation, {
  message: "Passwords do not match",
  path: ["password_confirmation"],
});

type ResetFormData = z.infer<typeof resetSchema>;

export default function ResetPasswordForm() {
  const [showPassword, setShowPassword]     = useState(false);
  const [showConfirm, setShowConfirm]       = useState(false);
  const [serverError, setServerError]       = useState<string | null>(null);
  const [submitted, setSubmitted]           = useState(false);

  const searchParams = useSearchParams();
  const router       = useRouter();
  const token        = searchParams.get("token") ?? "";
  const email        = searchParams.get("email") ?? "";

  const [resetPassword] = useResetPasswordMutation();

  const {
    register,
    handleSubmit,
    formState: { errors, isSubmitting },
  } = useForm<ResetFormData>({
    resolver: zodResolver(resetSchema),
  });

  const onSubmit = async (data: ResetFormData) => {
    setServerError(null);
    try {
      await resetPassword({
        token,
        email,
        password: data.password,
        password_confirmation: data.password_confirmation,
      }).unwrap();
      setSubmitted(true);
      setTimeout(() => router.replace("/auth/login"), 3000);
    } catch (err: unknown) {
      const error = err as { data?: { message?: string } };
      setServerError(error?.data?.message || "Something went wrong. Please try again.");
    }
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
                <div className="w-14 h-14 rounded-2xl bg-gradient-to-br from-violet-500 to-purple-600
                  flex items-center justify-center shadow-lg shadow-violet-300/40">
                  <KeyRound className="w-7 h-7 text-white" />
                </div>
              </div>

              {/* Heading */}
              <h1 className="text-[22px] font-bold text-slate-800 text-center mb-2 tracking-tight">
                Set new password
              </h1>
              <p className="text-sm text-slate-400 text-center mb-8 leading-relaxed">
                Your new password must be at least 8 characters long.
              </p>

              {/* Server Error */}
              {serverError && (
                <div className="mb-5 px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-sm text-red-600">
                  ⚠ {serverError}
                </div>
              )}

              {/* Invalid token warning */}
              {(!token || !email) && (
                <div className="mb-5 px-4 py-3 rounded-xl bg-amber-50 border border-amber-200 text-sm text-amber-600">
                  ⚠ Invalid or expired reset link. Please request a new one.
                </div>
              )}

              <form onSubmit={handleSubmit(onSubmit)} noValidate className="space-y-5">

                {/* New Password */}
                <div>
                  <label className="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-wide">
                    New Password
                  </label>
                  <div className="relative">
                    <input
                      {...register("password")}
                      type={showPassword ? "text" : "password"}
                      placeholder="••••••••"
                      autoComplete="new-password"
                      autoFocus
                      className={`w-full rounded-xl px-4 py-3 pr-11 text-sm text-slate-800 placeholder-slate-300
                        bg-slate-50 border outline-none transition-all duration-200
                        focus:ring-2 focus:ring-violet-400/40 focus:border-violet-400 focus:bg-white
                        ${errors.password
                          ? "border-red-400 bg-red-50/60 focus:ring-red-300/40 focus:border-red-400"
                          : "border-slate-200 hover:border-slate-300"
                        }`}
                    />
                    <button
                      type="button"
                      onClick={() => setShowPassword((v) => !v)}
                      className="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors"
                      tabIndex={-1}
                    >
                      {showPassword ? <EyeOff className="w-4 h-4" /> : <Eye className="w-4 h-4" />}
                    </button>
                  </div>
                  {errors.password && (
                    <p className="mt-1.5 text-xs text-red-500">⚠ {errors.password.message}</p>
                  )}
                </div>

                {/* Confirm Password */}
                <div>
                  <label className="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-wide">
                    Confirm Password
                  </label>
                  <div className="relative">
                    <input
                      {...register("password_confirmation")}
                      type={showConfirm ? "text" : "password"}
                      placeholder="••••••••"
                      autoComplete="new-password"
                      className={`w-full rounded-xl px-4 py-3 pr-11 text-sm text-slate-800 placeholder-slate-300
                        bg-slate-50 border outline-none transition-all duration-200
                        focus:ring-2 focus:ring-violet-400/40 focus:border-violet-400 focus:bg-white
                        ${errors.password_confirmation
                          ? "border-red-400 bg-red-50/60 focus:ring-red-300/40 focus:border-red-400"
                          : "border-slate-200 hover:border-slate-300"
                        }`}
                    />
                    <button
                      type="button"
                      onClick={() => setShowConfirm((v) => !v)}
                      className="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors"
                      tabIndex={-1}
                    >
                      {showConfirm ? <EyeOff className="w-4 h-4" /> : <Eye className="w-4 h-4" />}
                    </button>
                  </div>
                  {errors.password_confirmation && (
                    <p className="mt-1.5 text-xs text-red-500">⚠ {errors.password_confirmation.message}</p>
                  )}
                </div>

                {/* Submit */}
                <button
                  type="submit"
                  disabled={isSubmitting || !token || !email}
                  className="w-full py-3 rounded-xl text-sm font-semibold text-white
                    bg-gradient-to-r from-violet-600 to-purple-600
                    hover:from-violet-500 hover:to-purple-500
                    active:scale-[0.98] transition-all duration-200
                    shadow-lg shadow-violet-300/50
                    flex items-center justify-center gap-2
                    disabled:opacity-60 disabled:cursor-not-allowed cursor-pointer"
                >
                  {isSubmitting ? (
                    <>
                      <svg className="w-4 h-4 animate-spin" viewBox="0 0 24 24" fill="none">
                        <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4" />
                        <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z" />
                      </svg>
                      Resetting…
                    </>
                  ) : (
                    <>
                      <KeyRound className="w-4 h-4" />
                      Reset password
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
                <div className="w-16 h-16 rounded-full bg-gradient-to-br from-emerald-400 to-teal-500
                  flex items-center justify-center shadow-lg shadow-emerald-200/60">
                  <svg className="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2.5}>
                    <path strokeLinecap="round" strokeLinejoin="round" d="M5 13l4 4L19 7" />
                  </svg>
                </div>
              </div>

              <h1 className="text-[22px] font-bold text-slate-800 text-center mb-2 tracking-tight">
                Password reset!
              </h1>
              <p className="text-sm text-slate-400 text-center mb-6 leading-relaxed">
                Your password has been reset successfully.
                Redirecting you to sign in…
              </p>

              <div className="bg-violet-50 border border-violet-100 rounded-2xl p-4 mb-6">
                <p className="text-xs text-slate-500 text-center leading-relaxed">
                  You will be redirected to the login page in a few seconds.
                </p>
              </div>

              <div className="flex justify-center">
                <Link
                  href="/auth/login"
                  className="flex items-center gap-2 py-3 px-6 rounded-xl text-sm font-semibold text-white
                    bg-gradient-to-r from-violet-600 to-purple-600
                    hover:from-violet-500 hover:to-purple-500
                    transition-all duration-200 shadow-lg shadow-violet-300/50"
                >
                  Go to sign in
                </Link>
              </div>
            </>
          )}
        </div>
      </div>
    </div>
  );
}