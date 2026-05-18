"use client";

import { useRouter } from "next/navigation";
import { useLogoutMutation } from "@/store/features/auth/authApi";
import { logout } from "@/store/features/auth/authSlice";
import { useAppDispatch } from "@/store/hook";

import { useState, useRef, useEffect } from "react";
import Link from "next/link";
import {
  Search, Bell, MessageSquare, LayoutGrid,
  ChevronDown, ChevronRight, X,
  User, Settings, Globe, Moon, LogOut,
  FileText, Check, Archive, Inbox, Menu,
} from "lucide-react";

const NAV_LINKS = [
  { label: "Home",           href: "/dashboard" },
  { label: "Profiles",       children: [{ label: "Public Profile", href: "/profile" }, { label: "Edit Profile", href: "/profile/edit" }] },
  { label: "My Account",     children: [{ label: "Account Info",   href: "/account"  }, { label: "Security",    href: "/account/security" }] },
  { label: "Network",        children: [{ label: "Connections",    href: "/network"  }, { label: "Requests",    href: "/network/requests" }] },
  { label: "Store",          children: [{ label: "Products",       href: "/store/client" }, { label: "Orders", href: "/store/client/orders" }] },
  { label: "Authentication", children: [{ label: "Login",          href: "/auth/login" }, { label: "Register", href: "/auth/register" }] },
  { label: "Help",           href: "/help" },
];

const NOTIFICATIONS = [
  {
    id: 1, avatar: "JL", color: "bg-blue-500",
    content: <><strong>Joe Lincoln</strong> mentioned you in <span className="text-violet-600 font-medium">Latest Trends</span> topic</>,
    sub: "18 mins ago · Web Design 2024",
    reply: true,
    replyText: "@Cody For an expert opinion, check out what Mike has to say on this topic!",
  },
  {
    id: 2, avatar: "LA", color: "bg-rose-500",
    content: <><strong>Leslie Alexander</strong> added new tags to <span className="text-violet-600 font-medium">Web Redesign 2024</span></>,
    sub: "53 mins ago · ACME",
    tags: ["Client-Request", "Figma", "Redesign"],
  },
  {
    id: 3, avatar: "GH", color: "bg-emerald-500",
    content: <><strong>Guy Hawkins</strong> requested access to <span className="text-violet-600 font-medium">AirSpace</span> project</>,
    sub: "14 hours ago · Dev Team",
    actions: true,
  },
  {
    id: 4, avatar: "JP", color: "bg-amber-500",
    content: <><strong>Jane Perez</strong> invites you to review a file.</>,
    sub: "3 hours ago · 742kb",
    file: "Launch_nov24.pptx",
  },
  {
    id: 5, avatar: "RP", color: "bg-indigo-500",
    content: <><strong>Raymond Pawell</strong> posted a new article <span className="text-violet-600 font-medium">2024 Roadmap</span></>,
    sub: "Yesterday · Product",
  },
];

// ① Props interface যোগ করুন
interface HeaderProps {
  onMobileMenuOpen: () => void;
}

function useOutsideClick(ref: React.RefObject<HTMLElement | null>, cb: () => void) {
  useEffect(() => {
    const handler = (e: MouseEvent) => {
      if (ref.current && !ref.current.contains(e.target as Node)) cb();
    };
    document.addEventListener("mousedown", handler);
    return () => document.removeEventListener("mousedown", handler);
  }, [ref, cb]);
}

export default function Header({ onMobileMenuOpen }: HeaderProps) {
  const [openNav,     setOpenNav]     = useState<string | null>(null);
  const [searchOpen,  setSearchOpen]  = useState(false);
  const [showProfile, setShowProfile] = useState(false);
  const [showNotif,   setShowNotif]   = useState(false);
  const [darkMode,    setDarkMode]    = useState(false);
  const [notifTab,    setNotifTab]    = useState<"All"|"Inbox"|"Team"|"Following">("All");

  const navRef     = useRef<HTMLDivElement>(null);
  const profileRef = useRef<HTMLDivElement>(null);
  const notifRef   = useRef<HTMLDivElement>(null);  // wraps button + panel together

  useOutsideClick(navRef,     () => setOpenNav(null));
  useOutsideClick(profileRef, () => setShowProfile(false));
  useOutsideClick(notifRef,   () => setShowNotif(false));

  const router = useRouter();
  const dispatch = useAppDispatch();
  const [logoutApi] = useLogoutMutation();

  const handleLogout = async () => {
    try {

      await logoutApi().unwrap();

    } catch {
      
    } finally {
      dispatch(logout());
      router.replace("/auth/login");
    }
  };

  return (
    <header className="h-[68px] bg-white border-b border-slate-200 flex items-center px-4 lg:px-8 gap-3 lg:gap-6 shrink-0 sticky top-0 z-30 shadow-sm">

      {/* Mobile menu button */}
      <button
          onClick={onMobileMenuOpen}
          className="lg:hidden w-10 h-10 flex items-center justify-center rounded-xl
            text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition-colors shrink-0"
      >
        <Menu className="w-5 h-5" />
      </button>
     
      {/* ── Nav links ── */}
      <nav ref={navRef} className="flex items-center gap-0.5 flex-1">
        {NAV_LINKS.map((link) => (
          <div key={link.label} className="relative">
            {link.children ? (
              <>
                <button
                  onClick={() => setOpenNav(openNav === link.label ? null : link.label)}
                  className={`flex items-center gap-1.5 px-4 py-2 rounded-xl
                    text-sm font-medium transition-all duration-150
                    ${openNav === link.label
                      ? "text-violet-600 bg-violet-50"
                      : "text-slate-600 hover:text-slate-900 hover:bg-slate-100"
                    }`}
                >
                  {link.label}
                  <ChevronDown className={`w-3.5 h-3.5 transition-transform duration-200
                    ${openNav === link.label ? "rotate-180 text-violet-500" : "text-slate-400"}`}
                  />
                </button>

                {openNav === link.label && (
                  <div className="absolute top-full left-0 mt-2 w-52
                    bg-white border border-slate-200 rounded-2xl
                    shadow-xl shadow-slate-200/70 py-2 z-50">
                    {link.children.map((child) => (
                      <Link
                        key={child.href}
                        href={child.href}
                        onClick={() => setOpenNav(null)}
                        className="flex items-center gap-3 px-5 py-2.5
                          text-sm text-slate-600 hover:text-slate-900 hover:bg-slate-50
                          transition-colors"
                      >
                        <span className="w-1.5 h-1.5 rounded-full bg-slate-300 shrink-0" />
                        {child.label}
                      </Link>
                    ))}
                  </div>
                )}
              </>
            ) : (
              <Link
                href={link.href!}
                className="px-4 py-2 rounded-xl text-sm font-medium
                  text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition-colors"
              >
                {link.label}
              </Link>
            )}
          </div>
        ))}
      </nav>

      {/* ── Right actions ── */}
      <div className="flex items-center gap-2">

        {/* Search */}
        <div className="relative">
          {searchOpen ? (
            <div className="flex items-center gap-2 bg-slate-100 border border-slate-200
              rounded-xl px-3.5 py-2">
              <Search className="w-4 h-4 text-slate-400 shrink-0" />
              <input
                autoFocus
                onBlur={() => setSearchOpen(false)}
                type="text"
                placeholder="Search…"
                className="w-44 bg-transparent text-sm text-slate-700
                  placeholder-slate-400 outline-none"
              />
            </div>
          ) : (
            <button
              onClick={() => setSearchOpen(true)}
              className="w-10 h-10 flex items-center justify-center rounded-xl
                text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition-colors"
            >
              <Search className="w-[18px] h-[18px]" />
            </button>
          )}
        </div>

        {/* ── Notifications — button + panel wrapped in ONE ref div ── */}
        <div ref={notifRef} className="relative">
          <button
            onClick={() => {
              setShowNotif((v) => !v);
              setShowProfile(false);
            }}
            className="relative w-10 h-10 flex items-center justify-center rounded-xl
              text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition-colors"
          >
            <Bell className="w-[18px] h-[18px]" />
            <span className="absolute top-2 right-2 w-2 h-2
              bg-violet-500 rounded-full ring-2 ring-white" />
          </button>

          {showNotif && (
            <div className="absolute right-0 top-full mt-3 w-[400px]
              bg-white border border-slate-200 rounded-2xl
              shadow-2xl shadow-slate-300/50 z-50">

              {/* Panel header */}
              <div className="flex items-center justify-between px-6 pt-5 pb-4
                border-b border-slate-100">
                <h3 className="text-sm font-bold text-slate-800">Notifications</h3>
                <button
                  onClick={() => setShowNotif(false)}
                  className="w-7 h-7 flex items-center justify-center rounded-lg
                    text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors"
                >
                  <X className="w-4 h-4" />
                </button>
              </div>

              {/* Tabs */}
              <div className="flex items-center gap-1 px-5 pt-3 pb-2">
                {(["All","Inbox","Team","Following"] as const).map((tab) => (
                  <button
                    key={tab}
                    onClick={() => setNotifTab(tab)}
                    className={`flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg
                      text-xs font-semibold transition-colors
                      ${notifTab === tab
                        ? "text-violet-600 bg-violet-50"
                        : "text-slate-500 hover:text-slate-700 hover:bg-slate-50"
                      }`}
                  >
                    {tab}
                    {tab === "Inbox" && (
                      <span className="text-[10px] bg-violet-100 text-violet-600
                        px-1.5 py-0.5 rounded-full font-bold leading-none">3</span>
                    )}
                  </button>
                ))}
                <button className="ml-auto w-7 h-7 flex items-center justify-center
                  rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors">
                  <Settings className="w-3.5 h-3.5" />
                </button>
              </div>

              {/* Notification list */}
              <div className="overflow-y-auto max-h-[380px] divide-y divide-slate-50
                [&::-webkit-scrollbar]:w-[3px]
                [&::-webkit-scrollbar-thumb]:bg-slate-200
                [&::-webkit-scrollbar-thumb]:rounded-full">
                {NOTIFICATIONS.map((n) => (
                  <div key={n.id} className="px-6 py-4 hover:bg-slate-50 transition-colors">
                    <div className="flex gap-3.5">
                      <div className={`w-9 h-9 rounded-full ${n.color} flex items-center
                        justify-center text-white text-xs font-bold shrink-0 mt-0.5`}>
                        {n.avatar}
                      </div>
                      <div className="flex-1 min-w-0">
                        <p className="text-[13px] text-slate-700 leading-snug">{n.content}</p>
                        <p className="text-[11px] text-slate-400 mt-1">{n.sub}</p>

                        {n.reply && n.replyText && (
                          <div className="mt-2.5 p-3 bg-slate-50 rounded-xl border border-slate-100">
                            <p className="text-[12px] text-slate-500 leading-relaxed">{n.replyText}</p>
                            <div className="mt-2 flex items-center gap-2 bg-white border border-slate-200
                              rounded-lg px-3 py-2">
                              <span className="text-xs text-slate-400 flex-1">Reply</span>
                              <MessageSquare className="w-3.5 h-3.5 text-slate-300" />
                            </div>
                          </div>
                        )}

                        {n.tags && (
                          <div className="mt-2 flex flex-wrap gap-1.5">
                            {n.tags.map((t) => (
                              <span key={t} className="text-[11px] bg-slate-100 text-slate-600
                                px-2.5 py-0.5 rounded-md font-medium">{t}</span>
                            ))}
                          </div>
                        )}

                        {n.actions && (
                          <div className="mt-2.5 flex gap-2">
                            <button className="px-4 py-1.5 text-xs font-semibold
                              bg-slate-100 text-slate-600 hover:bg-slate-200
                              rounded-lg transition-colors">Decline</button>
                            <button className="px-4 py-1.5 text-xs font-semibold
                              bg-slate-900 text-white hover:bg-slate-700
                              rounded-lg transition-colors">Accept</button>
                          </div>
                        )}

                        {n.file && (
                          <div className="mt-2.5 flex items-center gap-3 p-3
                            bg-slate-50 border border-slate-100 rounded-xl">
                            <div className="w-8 h-8 rounded-lg bg-red-100 flex items-center
                              justify-center shrink-0">
                              <FileText className="w-4 h-4 text-red-500" />
                            </div>
                            <div>
                              <p className="text-[12px] font-semibold text-slate-700">{n.file}</p>
                              <p className="text-[10px] text-slate-400">Edited 39 mins ago</p>
                            </div>
                          </div>
                        )}
                      </div>
                    </div>
                  </div>
                ))}
              </div>

              {/* Footer */}
              <div className="flex border-t border-slate-100">
                <button className="flex-1 flex items-center justify-center gap-2
                  py-3.5 text-xs font-semibold text-slate-500
                  hover:text-slate-800 hover:bg-slate-50 transition-colors
                  rounded-bl-2xl border-r border-slate-100">
                  <Archive className="w-3.5 h-3.5" />Archive all
                </button>
                <button className="flex-1 flex items-center justify-center gap-2
                  py-3.5 text-xs font-semibold text-slate-500
                  hover:text-slate-800 hover:bg-slate-50 transition-colors rounded-br-2xl">
                  <Check className="w-3.5 h-3.5" />Mark all as read
                </button>
              </div>
            </div>
          )}
        </div>

        {/* Messages */}
        <button className="relative w-10 h-10 flex items-center justify-center rounded-xl
          text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition-colors">
          <MessageSquare className="w-[18px] h-[18px]" />
          <span className="absolute top-2 right-2 w-2 h-2
            bg-emerald-500 rounded-full ring-2 ring-white" />
        </button>

        {/* Grid */}
        <button className="w-10 h-10 flex items-center justify-center rounded-xl
          text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition-colors">
          <LayoutGrid className="w-[18px] h-[18px]" />
        </button>

        {/* Divider */}
        <div className="w-px h-6 bg-slate-200 mx-1" />

        {/* Profile */}
        <div ref={profileRef} className="relative">
          <button
            onClick={() => { setShowProfile((v) => !v); setShowNotif(false); }}
            className="w-10 h-10 rounded-full bg-gradient-to-br from-violet-500 to-purple-600
              flex items-center justify-center text-white text-sm font-bold
              ring-2 ring-white hover:ring-violet-300 shadow-sm
              transition-all duration-200"
          >
            JD
          </button>

          {showProfile && (
            <div className="absolute right-0 top-full mt-3 w-[250px]
              bg-white border border-slate-200 rounded-2xl
              shadow-2xl shadow-slate-300/50 z-50 overflow-hidden">

              {/* User info */}
              <div className="flex items-center gap-3.5 px-5 py-4 border-b border-slate-100">
                <div className="w-11 h-11 rounded-full bg-gradient-to-br from-violet-500 to-purple-600
                  flex items-center justify-center text-white text-sm font-bold shrink-0">
                  JD
                </div>
                <div className="flex-1 min-w-0">
                  <p className="text-sm font-bold text-slate-800 truncate">John Doe</p>
                  <p className="text-xs text-slate-400 truncate">john@example.com</p>
                </div>
                <span className="text-[10px] font-bold bg-violet-100 text-violet-600
                  px-2 py-0.5 rounded-full border border-violet-200 shrink-0">Pro</span>
              </div>

              {/* Menu */}
              <div className="py-2">
                {[
                  { icon: User,     label: "Public Profile", href: "/profile" },
                  { icon: User,     label: "My Profile",     href: "/profile/me" },
                  { icon: Settings, label: "My Account",     href: "/account", chevron: true },
                  { icon: Inbox,    label: "Dev Forum",      href: "/forum" },
                ].map(({ icon: Icon, label, href, chevron }) => (
                  <Link
                    key={label}
                    href={href}
                    onClick={() => setShowProfile(false)}
                    className="flex items-center gap-3.5 px-5 py-3
                      text-sm text-slate-600 hover:text-slate-900 hover:bg-slate-50
                      transition-colors"
                  >
                    <Icon className="w-4 h-4 text-slate-400 shrink-0" />
                    <span className="flex-1">{label}</span>
                    {chevron && <ChevronRight className="w-3.5 h-3.5 text-slate-300" />}
                  </Link>
                ))}
              </div>

              {/* Language */}
              <div className="border-t border-slate-100 px-5 py-3.5 flex items-center gap-3.5">
                <Globe className="w-4 h-4 text-slate-400 shrink-0" />
                <span className="flex-1 text-sm text-slate-600">Language</span>
                <div className="flex items-center gap-1.5 text-sm text-slate-500">
                  English <span>🇺🇸</span>
                </div>
              </div>

              {/* Dark Mode */}
              <div className="border-t border-slate-100 px-5 py-3.5 flex items-center gap-3.5">
                <Moon className="w-4 h-4 text-slate-400 shrink-0" />
                <span className="flex-1 text-sm text-slate-600">Dark Mode</span>
                <button
                  onClick={() => setDarkMode((v) => !v)}
                  style={{ width: 40, height: 22 }}
                  className={`relative rounded-full transition-colors duration-200
                    ${darkMode ? "bg-violet-600" : "bg-slate-200"}`}
                >
                  <span className={`absolute top-[3px] w-4 h-4 bg-white rounded-full shadow
                    transition-all duration-200
                    ${darkMode ? "left-[20px]" : "left-[3px]"}`}
                  />
                </button>
              </div>

              {/* Log out */}
              <div className="border-t border-slate-100 p-3">
                <button onClick={handleLogout} className="w-full flex items-center justify-center gap-2
                  py-2.5 rounded-xl text-sm font-semibold
                  text-slate-600 hover:text-red-600 hover:bg-red-50
                  transition-colors cursor-pointer">
                  <LogOut className="w-4 h-4" />Log out
                </button>
              </div>
            </div>
          )}
        </div>
      </div>
    </header>
  );
}