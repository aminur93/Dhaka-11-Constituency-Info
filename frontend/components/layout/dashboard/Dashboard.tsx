"use client";

import { useState } from "react";
import {
  TrendingUp, TrendingDown, MoreVertical,
  ArrowUpRight, Calendar, Store, Share2,
  Users, Play, Camera, Music,
} from "lucide-react";

const STATS = [
  { icon: Users,  color: "bg-blue-500",  label: "Amazing mates",   value: "9.3k", trend: "+12%",  up: true  },
  { icon: Play,   color: "bg-red-500",   label: "Lessons Views",   value: "24k",  trend: "+8.5%", up: true  },
  { icon: Camera, color: "bg-pink-500",  label: "New subscribers", value: "608",  trend: "-2.1%", up: false },
  { icon: Music,  color: "bg-slate-600", label: "Stream audience", value: "2.5k", trend: "+5.3%", up: true  },
];

const HIGHLIGHTS = [
  { icon: Store,  label: "Online Store", value: "$172k", trend: "+3.9%", up: true  },
  { icon: Share2, label: "Facebook",     value: "$85k",  trend: "-0.7%", up: false },
  { icon: Camera, label: "Instagram",    value: "$36k",  trend: "+8.2%", up: true  },
];

function EarningsChart() {
  const data   = [62, 58, 55, 48, 52, 44, 50, 48, 52, 58, 62, 80];
  const months = ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];
  const w = 560, h = 160;
  const pad = { top: 16, right: 16, bottom: 28, left: 48 };
  const cW = w - pad.left - pad.right;
  const cH = h - pad.top  - pad.bottom;
  const min = Math.min(...data) - 10;
  const max = Math.max(...data) + 5;

  const x = (i: number) => pad.left + (i / (data.length - 1)) * cW;
  const y = (v: number) => pad.top  + cH - ((v - min) / (max - min)) * cH;

  const linePath = data.map((v, i) => `${i === 0 ? "M" : "L"} ${x(i)} ${y(v)}`).join(" ");
  const areaPath = `${linePath} L ${x(data.length - 1)} ${h - pad.bottom} L ${x(0)} ${h - pad.bottom} Z`;
  const yLabels  = ["$0k","$20k","$40k","$60k","$80k","$100k"];

  return (
    <svg viewBox={`0 0 ${w} ${h}`} className="w-full h-full">
      <defs>
        <linearGradient id="areaGrad" x1="0" y1="0" x2="0" y2="1">
          <stop offset="0%"   stopColor="#7c3aed" stopOpacity="0.15" />
          <stop offset="100%" stopColor="#7c3aed" stopOpacity="0.01" />
        </linearGradient>
      </defs>
      {yLabels.map((label, i) => {
        const yPos = pad.top + cH - (i / (yLabels.length - 1)) * cH;
        return (
          <g key={label}>
            <line x1={pad.left} x2={w - pad.right} y1={yPos} y2={yPos}
              stroke="#e2e8f0" strokeWidth="1" strokeDasharray="4 3" />
            <text x={pad.left - 8} y={yPos + 4} textAnchor="end" fontSize="9" fill="#94a3b8">{label}</text>
          </g>
        );
      })}
      <path d={areaPath} fill="url(#areaGrad)" />
      <path d={linePath} fill="none" stroke="#7c3aed" strokeWidth="2.5"
        strokeLinecap="round" strokeLinejoin="round" />
      {data.map((v, i) => (
        <circle key={i} cx={x(i)} cy={y(v)} r="3.5" fill="white" stroke="#7c3aed" strokeWidth="2" />
      ))}
      {months.map((m, i) => (
        <text key={m} x={x(i)} y={h - 5} textAnchor="middle" fontSize="9" fill="#94a3b8">{m}</text>
      ))}
    </svg>
  );
}

export default function Dashboard() {
  const [referralsOnly, setReferralsOnly] = useState(true);
  const [period, setPeriod]               = useState("1 month");

  return (
    <div className="bg-slate-50 px-4 sm:px-6 lg:px-8 py-6 sm:py-8 space-y-5 sm:space-y-7">

      {/* ── Page title ── */}
      <div className="flex flex-wrap items-start justify-between gap-3">
        <div>
          <h1 className="text-xl sm:text-2xl font-bold text-slate-800">Dashboard</h1>
          <p className="text-sm text-slate-400 mt-1">Central Hub for Personal Customization</p>
        </div>
        <button className="flex items-center gap-2 sm:gap-2.5 bg-white border border-slate-200
          text-slate-600 text-xs sm:text-sm font-medium px-3 sm:px-4 py-2 sm:py-2.5 rounded-xl
          hover:bg-slate-50 hover:border-slate-300 transition-all shadow-sm whitespace-nowrap">
          <Calendar className="w-4 h-4 text-slate-400 shrink-0" />
          <span className="hidden sm:inline">Jan 20, 2025 – Feb 09, 2025</span>
          <span className="sm:hidden">Jan – Feb 2025</span>
        </button>
      </div>

      {/* ── Row 1: Stats + Banner ── */}
      <div className="grid grid-cols-12 gap-4 sm:gap-5">

        {/* Stat cards */}
        <div className="col-span-12 lg:col-span-5 grid grid-cols-2 gap-4 sm:gap-5">
          {STATS.map(({ icon: Icon, color, label, value, trend, up }) => (
            <div key={label} className="bg-white border border-slate-200 rounded-2xl p-4 sm:p-5
              flex flex-col gap-3 sm:gap-4 hover:shadow-md hover:border-slate-300
              transition-all duration-200 shadow-sm">
              <div className={`w-9 h-9 sm:w-10 sm:h-10 rounded-xl ${color}
                flex items-center justify-center shrink-0 shadow-sm`}>
                <Icon className="w-4 h-4 sm:w-5 sm:h-5 text-white" />
              </div>
              <div>
                <p className="text-xl sm:text-2xl font-bold text-slate-800">{value}</p>
                <p className="text-xs text-slate-400 mt-1">{label}</p>
              </div>
              <span className={`text-[12px] font-semibold flex items-center gap-1
                ${up ? "text-emerald-500" : "text-red-500"}`}>
                {up ? <TrendingUp className="w-3.5 h-3.5" /> : <TrendingDown className="w-3.5 h-3.5" />}
                {trend}
              </span>
            </div>
          ))}
        </div>

        {/* Banner */}
        <div className="col-span-12 lg:col-span-7 bg-white border border-slate-200
          rounded-2xl p-6 sm:p-8 flex gap-6 items-center overflow-hidden relative shadow-sm">
          <div className="absolute top-0 right-0 w-72 h-72
            bg-violet-50 rounded-full blur-[80px] pointer-events-none" />

          <div className="flex-1 relative z-10">
            <div className="flex -space-x-2 mb-4 sm:mb-5">
              {["bg-orange-400","bg-blue-400","bg-slate-400","bg-emerald-400"].map((c, i) => (
                <div key={i} className={`w-8 h-8 sm:w-10 sm:h-10 rounded-full ${c}
                  border-2 border-white flex items-center justify-center
                  text-white text-xs font-bold shadow-sm`}>
                  {["A","B","C","D"][i]}
                </div>
              ))}
            </div>
            <h3 className="text-lg sm:text-xl font-bold text-slate-800 leading-snug mb-2 sm:mb-3">
              Connect Today &amp; Join the{" "}
              <span className="text-transparent bg-clip-text bg-gradient-to-r from-violet-600 to-purple-500">
                BNP Network
              </span>
            </h3>
            <p className="text-sm text-slate-500 leading-relaxed mb-4 sm:mb-5 max-w-xs">
              Enhance your projects with premium themes and templates.
              Join the community today for top-quality designs.
            </p>
            <button className="flex items-center gap-2 text-sm text-violet-600
              font-semibold hover:text-violet-700 transition-colors group">
              Get Started
              <ArrowUpRight className="w-4 h-4 group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-transform" />
            </button>
          </div>

          {/* Mini card preview */}
          <div className="hidden xl:block w-52 shrink-0 relative z-10">
            <div className="bg-slate-50 border border-slate-200 rounded-2xl p-5 shadow-sm">
              <p className="text-xs font-bold text-slate-700 mb-4">Sign in</p>
              <div className="space-y-2.5">
                <div className="h-8 bg-slate-200/70 rounded-xl" />
                <div className="h-8 bg-slate-200/70 rounded-xl" />
                <div className="h-8 bg-gradient-to-r from-violet-500 to-purple-500 rounded-xl" />
              </div>
            </div>
          </div>
        </div>
      </div>

      {/* ── Row 2: Highlights + Earnings ── */}
      <div className="grid grid-cols-12 gap-4 sm:gap-5">

        {/* Highlights */}
        <div className="col-span-12 lg:col-span-5 bg-white border border-slate-200
          rounded-2xl p-5 sm:p-7 shadow-sm">
          <div className="flex items-center justify-between mb-5 sm:mb-6">
            <h3 className="text-sm font-bold text-slate-800">Highlights</h3>
            <button className="w-8 h-8 flex items-center justify-center rounded-lg
              text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors">
              <MoreVertical className="w-4 h-4" />
            </button>
          </div>

          <div className="mb-5 sm:mb-6">
            <p className="text-xs text-slate-400 mb-2">All time sales</p>
            <div className="flex items-center gap-3">
              <p className="text-2xl sm:text-3xl font-bold text-slate-800">$295.7k</p>
              <span className="text-xs bg-emerald-50 text-emerald-600 px-2.5 py-1
                rounded-full font-semibold border border-emerald-100">+2.7%</span>
            </div>
          </div>

          <div className="flex h-2.5 rounded-full overflow-hidden mb-3 gap-0.5">
            <div className="bg-emerald-500 flex-[5]" />
            <div className="bg-red-400 flex-[3]" />
            <div className="bg-violet-500 flex-[2]" />
          </div>
          <div className="flex gap-4 sm:gap-5 mb-6 sm:mb-7 flex-wrap">
            {["BNP Core","Bundle","BNP Nest"].map((l, i) => (
              <div key={l} className="flex items-center gap-1.5">
                <div className={`w-2 h-2 rounded-full ${["bg-emerald-500","bg-red-400","bg-violet-500"][i]}`} />
                <span className="text-xs text-slate-400">{l}</span>
              </div>
            ))}
          </div>

          <div className="space-y-1">
            {HIGHLIGHTS.map(({ icon: Icon, label, value, trend, up }) => (
              <div key={label} className="flex items-center gap-3 sm:gap-3.5 py-3
                border-b border-slate-100 last:border-0">
                <div className="w-8 h-8 rounded-xl bg-slate-100 flex items-center justify-center shrink-0">
                  <Icon className="w-4 h-4 text-slate-500" />
                </div>
                <span className="flex-1 text-sm text-slate-700 font-medium">{label}</span>
                <span className="text-sm font-bold text-slate-800">{value}</span>
                <span className={`text-xs font-semibold flex items-center gap-1
                  ${up ? "text-emerald-500" : "text-red-500"}`}>
                  {up ? <TrendingUp className="w-3.5 h-3.5" /> : <TrendingDown className="w-3.5 h-3.5" />}
                  {trend}
                </span>
              </div>
            ))}
          </div>
        </div>

        {/* Earnings */}
        <div className="col-span-12 lg:col-span-7 bg-white border border-slate-200
          rounded-2xl p-5 sm:p-7 shadow-sm">
          <div className="flex flex-wrap items-center justify-between gap-3 mb-5 sm:mb-6">
            <h3 className="text-sm font-bold text-slate-800">Earnings</h3>
            <div className="flex flex-wrap items-center gap-3 sm:gap-4">
              <div className="flex items-center gap-2 sm:gap-2.5">
                <span className="text-xs text-slate-500 font-medium">Referrals only</span>
                <button
                  onClick={() => setReferralsOnly((v) => !v)}
                  style={{ width: 36, height: 20 }}
                  className={`relative rounded-full transition-colors duration-200
                    ${referralsOnly ? "bg-violet-600" : "bg-slate-200"}`}>
                  <span className={`absolute top-[2px] w-4 h-4 bg-white rounded-full shadow
                    transition-all duration-200
                    ${referralsOnly ? "left-[18px]" : "left-[2px]"}`} />
                </button>
              </div>
              <select
                value={period}
                onChange={(e) => setPeriod(e.target.value)}
                className="text-xs bg-slate-50 border border-slate-200 text-slate-600
                  rounded-xl px-3 py-2 outline-none cursor-pointer
                  hover:border-slate-300 transition-colors font-medium">
                {["1 week","1 month","3 months","1 year"].map((p) => (
                  <option key={p} value={p}>{p}</option>
                ))}
              </select>
            </div>
          </div>

          <div className="h-44 sm:h-48">
            <EarningsChart />
          </div>
        </div>
      </div>
    </div>
  );
}