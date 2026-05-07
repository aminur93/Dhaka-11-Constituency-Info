"use client";

import { useState } from "react";
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { z } from "zod";
import { useRouter, useSearchParams } from "next/navigation";
import { Eye, EyeOff, LayoutDashboard } from "lucide-react";
import { useLoginMutation } from "@/store/features/auth/authApi";
import { setCredentials } from "@/store/features/auth/authSlice";
import { useAppDispatch } from "@/store/hook";

const loginSchema = z.object({
  email: z.string().min(1, "Email address is required").email("Invalid email address"),
  password: z.string().min(1, "Password is required"),
  rememberMe: z.boolean().optional(),
});

type LoginFormData = z.infer<typeof loginSchema>;

export default function LoginForm() {
  const [showPassword, setShowPassword] = useState(false);
  const [serverError, setServerError] = useState<string | null>(null);

  const router = useRouter();
  const searchParams = useSearchParams();
  const dispatch = useAppDispatch();
  const [login] = useLoginMutation();

  const {
    register,
    handleSubmit,
    formState: { errors, isSubmitting },
  } = useForm<LoginFormData>({
    resolver: zodResolver(loginSchema),
  });

  const onSubmit = async (data: LoginFormData) => {
    setServerError(null);
    try {
      const res = await login({ email: data.email, password: data.password }).unwrap();
      dispatch(setCredentials({ user: res.data.user, token: res.data.token }));
      const redirectTo = searchParams.get("redirect") || "/dashboard";
      router.replace(redirectTo);
    } catch (err: unknown) {
      const error = err as { data?: { message?: string } };
      setServerError(error?.data?.message || "Invalid email or password");
    }
  };

  return (
    <div className="min-h-screen w-full flex">

      {/* ── LEFT PANEL ── */}
      <div className="hidden lg:flex w-[45%] relative overflow-hidden bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 flex-col items-center justify-center p-12">
        <div className="absolute top-[-80px] left-[-80px] w-[400px] h-[400px] rounded-full bg-violet-600/25 blur-[120px] pointer-events-none" />
        <div className="absolute bottom-[-60px] right-[-60px] w-[350px] h-[350px] rounded-full bg-indigo-500/20 blur-[100px] pointer-events-none" />
        <div
          className="absolute inset-0 opacity-[0.07] pointer-events-none"
          style={{
            backgroundImage: "radial-gradient(circle, #ffffff 1px, transparent 1px)",
            backgroundSize: "26px 26px",
          }}
        />
        <div className="relative z-10 text-center max-w-sm">
          <div className="w-14 h-14 rounded-2xl bg-gradient-to-br from-violet-500 to-purple-700 flex items-center justify-center mx-auto mb-8 shadow-2xl shadow-purple-900/60">
            <LayoutDashboard className="w-7 h-7 text-white" />
          </div>
          <h2 className="text-[28px] font-bold text-white mb-3 leading-snug">
            Welcome back to{" "}
            <span className="text-transparent bg-clip-text bg-gradient-to-r from-violet-400 to-purple-300">
              Your Dashboard
            </span>
          </h2>
          <p className="text-slate-400 text-sm leading-relaxed">
            Manage your projects, track progress, and collaborate with your team — all in one place.
          </p>
          <div className="mt-10 flex gap-10 justify-center">
            {[["10K+", "Users"], ["99.9%", "Uptime"], ["4.9★", "Rating"]].map(([val, label]) => (
              <div key={label} className="text-center">
                <div className="text-white font-bold text-xl">{val}</div>
                <div className="text-slate-500 text-xs mt-1">{label}</div>
              </div>
            ))}
          </div>
        </div>
      </div>

      {/* ── RIGHT PANEL ── */}
      <div className="flex-1 min-h-screen flex items-center justify-center relative overflow-hidden bg-[#f5f4ff] px-6 py-10">
        <div className="absolute top-[-100px] right-[-100px] w-[450px] h-[450px] rounded-full bg-violet-200/50 blur-[100px] pointer-events-none" />
        <div className="absolute bottom-[-80px] left-[-80px] w-[380px] h-[380px] rounded-full bg-purple-200/40 blur-[90px] pointer-events-none" />
        <div
          className="absolute inset-0 opacity-[0.35] pointer-events-none"
          style={{
            backgroundImage: "radial-gradient(circle, #a78bfa 1px, transparent 1px)",
            backgroundSize: "30px 30px",
          }}
        />

        <div className="relative z-10 w-full max-w-[420px]">
          <div className="bg-white rounded-3xl shadow-2xl shadow-violet-100/80 border border-violet-100 p-8 md:p-10">

            {/* Mobile logo */}
            <div className="lg:hidden flex justify-center mb-6">
              <div className="w-10 h-10 rounded-xl bg-gradient-to-br from-violet-500 to-purple-700 flex items-center justify-center shadow-lg">
                <LayoutDashboard className="w-5 h-5 text-white" />
              </div>
            </div>

            <h1 className="text-[22px] font-bold text-slate-800 mb-1 tracking-tight">
              Sign in to Dashboard
            </h1>
            <p className="text-sm text-slate-400 mb-7">
              Welcome back! Please sign in to continue.
            </p>

            {/* Server Error */}
            {serverError && (
              <div className="mb-5 px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-sm text-red-600">
                ⚠ {serverError}
              </div>
            )}

            <form onSubmit={handleSubmit(onSubmit)} noValidate className="space-y-5">

              {/* Email */}
              <div>
                <label className="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-wide">
                  Email address
                </label>
                <input
                  {...register("email")}
                  type="email"
                  placeholder="you@example.com"
                  autoComplete="email"
                  className={`w-full rounded-xl px-4 py-3 text-sm text-slate-800 placeholder-slate-300
                    bg-slate-50 border outline-none transition-all duration-200
                    focus:ring-2 focus:ring-violet-400/40 focus:border-violet-400 focus:bg-white
                    ${errors.email
                      ? "border-red-400 bg-red-50/60 focus:ring-red-300/40 focus:border-red-400"
                      : "border-slate-200 hover:border-slate-300"
                    }`}
                />
                {errors.email && (
                  <p className="mt-1.5 text-xs text-red-500">⚠ {errors.email.message}</p>
                )}
              </div>

              {/* Password */}
              <div>
                <label className="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-wide">
                  Password
                </label>
                <div className="relative">
                  <input
                    {...register("password")}
                    type={showPassword ? "text" : "password"}
                    placeholder="••••••••"
                    autoComplete="current-password"
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

              {/* Remember + Forgot */}
              <div className="flex items-center justify-between">
                <label className="flex items-center gap-2 cursor-pointer select-none">
                  <input
                    {...register("rememberMe")}
                    type="checkbox"
                    className="w-4 h-4 rounded border-slate-300 accent-violet-600 cursor-pointer"
                  />
                  <span className="text-sm text-slate-500">Remember me</span>
                </label>
                <a href="/auth/forgot-password" className="text-sm text-violet-600 hover:text-violet-700 font-medium transition-colors">
                  Forgot password?
                </a>
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
                  disabled:opacity-60 disabled:cursor-not-allowed"
              >
                {isSubmitting ? (
                  <span className="flex items-center justify-center gap-2">
                    <svg className="animate-spin w-4 h-4" viewBox="0 0 24 24" fill="none">
                      <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4" />
                      <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z" />
                    </svg>
                    Signing in…
                  </span>
                ) : "Sign in"}
              </button>
            </form>

            {/* Divider */}
            <div className="flex items-center gap-3 my-6">
              <div className="flex-1 h-px bg-slate-200" />
              <span className="text-[11px] text-slate-400 uppercase tracking-widest font-medium">or continue with</span>
              <div className="flex-1 h-px bg-slate-200" />
            </div>

            {/* Social */}
            <div className="grid grid-cols-2 gap-3">
              <button
                type="button"
                className="flex items-center justify-center gap-2 py-2.5 rounded-xl border border-slate-200
                  bg-slate-50 hover:bg-white hover:border-violet-200 hover:shadow-sm
                  text-sm text-slate-600 font-medium transition-all duration-200"
              >
                <svg className="w-4 h-4 shrink-0" viewBox="0 0 24 24">
                  <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4" />
                  <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853" />
                  <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05" />
                  <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335" />
                </svg>
                Google
              </button>
              <button
                type="button"
                className="flex items-center justify-center gap-2 py-2.5 rounded-xl border border-slate-200
                  bg-slate-50 hover:bg-white hover:border-violet-200 hover:shadow-sm
                  text-sm text-slate-600 font-medium transition-all duration-200"
              >
                <svg className="w-4 h-4 fill-slate-700 shrink-0" viewBox="0 0 24 24">
                  <path d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" />
                </svg>
                GitHub
              </button>
            </div>

            <p className="text-center text-sm text-slate-400 mt-6">
              Don&apos;t have an account?{" "}
              <a href="/auth/register" className="text-violet-600 hover:text-violet-700 font-semibold transition-colors">
                Start your free trial
              </a>
            </p>
          </div>
        </div>
      </div>
    </div>
  );
}