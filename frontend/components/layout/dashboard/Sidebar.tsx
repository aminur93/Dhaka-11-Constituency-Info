"use client";

import { useState } from "react";
import Link from "next/link";
import { usePathname } from "next/navigation";
import {
  LayoutDashboard, User, Settings, Network, ShieldCheck,
  Store, Users, Bot, FileText, Plus, Minus,
  ChevronRight, ChevronLeft, Sparkles, FolderKanban, X,
} from "lucide-react";

type Child    = { label: string; href: string };
type NavItem  = { label: string; icon: React.ElementType; href?: string; badge?: string; children?: Child[] };
type NavGroup = { section: string; items: NavItem[] };

const NAV: NavGroup[] = [
  {
    section: "MAIN",
    items: [
      { label: "Dashboards", icon: LayoutDashboard, href: "/dashboard" },
    ],
  },
  {
    section: "USER",
    items: [
      {
        label: "Public Profile", icon: User,
        children: [
          { label: "Overview",     href: "/profile" },
          { label: "Edit Profile", href: "/profile/edit" },
        ],
      },
      {
        label: "My Account", icon: Settings,
        children: [
          { label: "Account Info", href: "/account" },
          { label: "Security",     href: "/account/security" },
        ],
      },
      {
        label: "Network", icon: Network,
        children: [
          { label: "Connections", href: "/network" },
          { label: "Requests",    href: "/network/requests" },
        ],
      },
      {
        label: "Authentication", icon: ShieldCheck,
        children: [
          { label: "Login",           href: "/auth/login" },
          { label: "Register",        href: "/auth/register" },
          { label: "Forgot Password", href: "/auth/forgot-password" },
        ],
      },
    ],
  },
  {
    section: "APPS",
    items: [
      {
        label: "User Management", icon: Users,
        children: [
          { label: "Permissions", href: "/dashboard/user-management/permission" },
          { label: "Roles",       href: "/dashboard/user-management/role" },
          { label: "Users",  href: "/dashboard/user-management/user" },
        ],
      },
      {
        label: "Store - Client", icon: Store,
        children: [
          { label: "Products", href: "/store/client" },
          { label: "Orders",   href: "/store/client/orders" },
        ],
      },
      {
        label: "Project Planning", icon: FolderKanban,
        children: [
          { label: "Board",    href: "/projects" },
          { label: "Timeline", href: "/projects/timeline" },
        ],
      },
      { label: "Store - Admin",     icon: Store,    badge: "Soon" },
      { label: "Store - Services",  icon: Store,    badge: "Soon" },
      { label: "AI Prompt",         icon: Bot,      badge: "Soon" },
      { label: "Invoice Generator", icon: FileText, badge: "Soon" },
    ],
  },
];

function Accordion({ open, children }: { open: boolean; children: React.ReactNode }) {
  return (
    <div className="grid transition-all duration-300 ease-in-out"
      style={{ gridTemplateRows: open ? "1fr" : "0fr" }}>
      <div className="overflow-hidden">{children}</div>
    </div>
  );
}

interface SidebarProps {
  mobileOpen: boolean;
  onMobileClose: () => void;
}

export default function Sidebar({ mobileOpen, onMobileClose }: SidebarProps) {
  const pathname = usePathname();

  const getInitialOpenMenus = () => {
    const open: string[] = [];
    NAV.forEach((group) => {
      group.items.forEach((item) => {
        if (item.children?.some((c) => c.href === pathname)) open.push(item.label);
      });
    });
    return open;
  };

  const [mini, setMini]           = useState(false);
  const [openMenus, setOpenMenus] = useState<string[]>(getInitialOpenMenus);
  const [hovered, setHovered]     = useState<string | null>(null);

  const toggle = (label: string) =>
    setOpenMenus((p) => p.includes(label) ? p.filter((l) => l !== label) : [...p, label]);

  const isActive = (item: NavItem) =>
    item.children
      ? item.children.some((c) => c.href === pathname)
      : item.href === pathname;

  const sidebarContent = (isMobile = false) => (
    <>
      {/* Logo */}
      <div className={`flex items-center h-[68px] shrink-0 border-b border-white/[0.08]
        ${mini && !isMobile ? "justify-center px-4" : "px-5"}`}>
        <div className="w-9 h-9 rounded-xl bg-gradient-to-br from-violet-500 to-purple-700
          flex items-center justify-center shrink-0 shadow-lg shadow-purple-900/40">
          <Sparkles className="w-5 h-5 text-white" />
        </div>
        {(!mini || isMobile) && (
          <span className="flex-1 ml-3 text-white font-bold text-lg tracking-tight select-none">
            BNP
          </span>
        )}
        {/* Mobile close button */}
        {isMobile && (
          <button onClick={onMobileClose}
            className="ml-auto w-8 h-8 flex items-center justify-center rounded-lg
              text-zinc-400 hover:text-white hover:bg-white/10 transition-colors">
            <X className="w-4 h-4" />
          </button>
        )}
      </div>

      {/* Nav */}
      <nav className="flex-1 overflow-y-auto overflow-x-hidden py-4 px-3
        [&::-webkit-scrollbar]:w-[3px]
        [&::-webkit-scrollbar-track]:bg-transparent
        [&::-webkit-scrollbar-thumb]:bg-white/10
        [&::-webkit-scrollbar-thumb]:rounded-full
        hover:[&::-webkit-scrollbar-thumb]:bg-white/20">

        {NAV.map((group, gi) => (
          <div key={group.section} className={gi > 0 ? "mt-7" : ""}>
            {(!mini || isMobile) ? (
              <p className="text-[10px] font-bold text-zinc-600 uppercase
                tracking-[0.14em] px-3 pb-2.5 pt-1 select-none">
                {group.section}
              </p>
            ) : (
              <div className="border-t border-white/[0.07] mx-2 my-4" />
            )}

            <div className="space-y-[3px]">
              {group.items.map((item) => {
                const Icon     = item.icon;
                const hasKids  = !!item.children?.length;
                const isOpen   = openMenus.includes(item.label);
                const active   = isActive(item);
                const disabled = !!item.badge;
                const isDirect = !hasKids && !!item.href && !disabled;
                const showMini = mini && !isMobile;

                return (
                  <div key={item.label} className="relative"
                    onMouseEnter={() => showMini && setHovered(item.label)}
                    onMouseLeave={() => showMini && setHovered(null)}>

                    {isDirect ? (
                      <Link href={item.href!}
                        onClick={isMobile ? onMobileClose : undefined}
                        className={`relative w-full flex items-center gap-3
                          px-3 py-[11px] rounded-xl text-[13px] font-medium
                          transition-all duration-200 select-none
                          ${showMini ? "justify-center" : ""}
                          ${active
                            ? "bg-violet-600/20 text-violet-400"
                            : "text-zinc-400 hover:bg-white/[0.06] hover:text-zinc-100"
                          }`}>
                        {active && (
                          <span className="absolute left-0 top-1/2 -translate-y-1/2
                            w-[3px] h-6 bg-violet-500 rounded-r-full" />
                        )}
                        <Icon className="w-[18px] h-[18px] shrink-0" />
                        {!showMini && <span className="flex-1 text-left truncate">{item.label}</span>}
                        {showMini && hovered === item.label && (
                          <span className="absolute left-full ml-3 px-3 py-1.5 rounded-lg
                            bg-zinc-900 border border-white/10 text-white text-xs
                            whitespace-nowrap shadow-xl pointer-events-none z-50">
                            {item.label}
                          </span>
                        )}
                      </Link>
                    ) : (
                      <button disabled={disabled}
                        onClick={() => hasKids && !showMini && toggle(item.label)}
                        className={`relative w-full flex items-center gap-3
                          px-3 py-[11px] rounded-xl text-[13px] font-medium
                          transition-all duration-200 select-none
                          ${showMini ? "justify-center" : ""}
                          ${active && !disabled
                            ? "bg-violet-600/20 text-violet-400"
                            : disabled
                              ? "text-zinc-700 cursor-not-allowed"
                              : "text-zinc-400 hover:bg-white/[0.06] hover:text-zinc-100"
                          }`}>
                        {active && !disabled && (
                          <span className="absolute left-0 top-1/2 -translate-y-1/2
                            w-[3px] h-6 bg-violet-500 rounded-r-full" />
                        )}
                        <Icon className="w-[18px] h-[18px] shrink-0" />
                        {!showMini && (
                          <>
                            <span className="flex-1 text-left truncate">{item.label}</span>
                            {disabled && (
                              <span className="text-[10px] bg-zinc-900 text-zinc-600
                                px-2 py-0.5 rounded-md font-semibold border border-white/[0.06]">
                                {item.badge}
                              </span>
                            )}
                            {hasKids && !disabled && (
                              <span className={`w-5 h-5 flex items-center justify-center
                                rounded-full transition-all duration-200 shrink-0
                                ${isOpen ? "bg-violet-600/25 text-violet-400" : "bg-white/[0.07] text-zinc-500"}`}>
                                {isOpen ? <Minus className="w-[10px] h-[10px]" /> : <Plus className="w-[10px] h-[10px]" />}
                              </span>
                            )}
                          </>
                        )}
                        {showMini && hovered === item.label && (
                          <span className="absolute left-full ml-3 px-3 py-1.5 rounded-lg
                            bg-zinc-900 border border-white/10 text-white text-xs
                            whitespace-nowrap shadow-xl pointer-events-none z-50">
                            {item.label}
                          </span>
                        )}
                      </button>
                    )}

                    {/* Accordion children */}
                    {hasKids && !showMini && (
                      <Accordion open={isOpen}>
                        <div className="ml-[14px] pl-4 pt-1 pb-2
                          border-l border-white/[0.08] space-y-[2px]">
                          {item.children!.map((child) => {
                            const ca = pathname === child.href;
                            return (
                              <Link key={child.href} href={child.href}
                                onClick={isMobile ? onMobileClose : undefined}
                                className={`flex items-center gap-3 px-3 py-[9px] rounded-xl
                                  text-[12.5px] font-medium transition-all duration-150
                                  ${ca
                                    ? "bg-violet-600/15 text-violet-400"
                                    : "text-zinc-500 hover:text-zinc-100 hover:bg-white/[0.05]"
                                  }`}>
                                <span className={`w-[6px] h-[6px] rounded-full shrink-0
                                  transition-colors duration-200
                                  ${ca ? "bg-violet-500" : "bg-zinc-700"}`} />
                                {child.label}
                              </Link>
                            );
                          })}
                        </div>
                      </Accordion>
                    )}

                    {/* Mini flyout */}
                    {showMini && hasKids && hovered === item.label && (
                      <div className="absolute left-full top-0 ml-3 w-52
                        bg-zinc-950 border border-white/[0.1]
                        rounded-xl shadow-2xl shadow-black/70 py-2 z-50">
                        <p className="text-[10px] font-bold text-zinc-600 uppercase
                          tracking-widest px-4 pt-1 pb-2 select-none">{item.label}</p>
                        <div className="h-px bg-white/[0.06] mx-3 mb-2" />
                        {item.children!.map((child) => {
                          const ca = pathname === child.href;
                          return (
                            <Link key={child.href} href={child.href}
                              className={`flex items-center gap-3 px-4 py-[9px]
                                text-[12px] font-medium transition-colors duration-150
                                ${ca ? "text-violet-400 bg-violet-600/10" : "text-zinc-400 hover:text-white hover:bg-white/[0.05]"}`}>
                              <span className={`w-[6px] h-[6px] rounded-full shrink-0
                                ${ca ? "bg-violet-500" : "bg-zinc-700"}`} />
                              {child.label}
                            </Link>
                          );
                        })}
                      </div>
                    )}
                  </div>
                );
              })}
            </div>
          </div>
        ))}
      </nav>

      {/* User */}
      <div className="shrink-0 border-t border-white/[0.08] p-4">
        {(mini && !isMobile) ? (
          <div className="flex justify-center">
            <div className="w-9 h-9 rounded-full bg-gradient-to-br from-violet-500 to-purple-600
              flex items-center justify-center text-white text-xs font-bold
              ring-2 ring-white/10 hover:ring-violet-500/40
              cursor-pointer transition-all duration-200">
              JD
            </div>
          </div>
        ) : (
          <div className="flex items-center gap-3 px-3 py-3 rounded-xl
            hover:bg-white/[0.05] cursor-pointer transition-all duration-200 group">
            <div className="w-9 h-9 rounded-full shrink-0
              bg-gradient-to-br from-violet-500 to-purple-600
              flex items-center justify-center text-white text-xs font-bold
              ring-2 ring-white/[0.12]">
              JD
            </div>
            <div className="flex-1 min-w-0">
              <p className="text-[13px] font-semibold text-zinc-200 truncate leading-none mb-1">John Doe</p>
              <p className="text-[11px] text-zinc-600 truncate leading-none">john@example.com</p>
            </div>
            <Settings className="w-4 h-4 text-zinc-700 shrink-0
              group-hover:text-zinc-400 group-hover:rotate-45 transition-all duration-300" />
          </div>
        )}
      </div>
    </>
  );

  return (
    <>
      {/* ── Desktop Sidebar ── */}
      <aside className={`hidden lg:flex fixed top-0 left-0 z-40 flex-col h-screen
        bg-black border-r border-white/[0.08]
        transition-all duration-300 ease-in-out
        ${mini ? "w-[72px]" : "w-[260px]"}`}>
        {sidebarContent(false)}
      </aside>

      {/* Desktop toggle button */}
      <button
        onClick={() => setMini((v) => !v)}
        className={`hidden lg:flex fixed top-[34px] -translate-y-1/2 z-50
          w-6 h-6 rounded-full bg-white border-2 border-slate-200
          items-center justify-center shadow-md hover:shadow-lg
          hover:border-violet-400 hover:text-violet-600 text-slate-500
          transition-all duration-300
          ${mini ? "left-[59px]" : "left-[247px]"}`}
        title={mini ? "Expand sidebar" : "Collapse sidebar"}>
        {mini ? <ChevronRight className="w-3.5 h-3.5" /> : <ChevronLeft className="w-3.5 h-3.5" />}
      </button>

      {/* Desktop spacer */}
      <div className={`hidden lg:block shrink-0 transition-all duration-300 ${mini ? "w-[72px]" : "w-[260px]"}`} />

      {/* ── Mobile Sidebar (drawer) ── */}
      <aside className={`lg:hidden fixed top-0 left-0 z-40 flex flex-col h-screen w-[280px]
        bg-black border-r border-white/[0.08]
        transition-transform duration-300 ease-in-out
        ${mobileOpen ? "translate-x-0" : "-translate-x-full"}`}>
        {sidebarContent(true)}
      </aside>
    </>
  );
}