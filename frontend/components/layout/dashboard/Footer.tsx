export default function Footer() {
  return (
    <footer className="h-[68px] bg-white border-t border-slate-200
      flex items-center justify-between px-8 shrink-0 shadow-sm">

      <p className="text-sm text-slate-500">
          © {new Date().getFullYear()}{" "}
          <span className="text-slate-700 font-semibold">BNP Dashboard</span>
          {" "}by{" "}
          <a
            href="https://rangerstech.com"
            target="_blank"
            rel="noopener noreferrer"
            className="text-violet-600 hover:text-violet-700 font-semibold transition-colors"
          >
            Rangerstech.com
          </a>
          . All rights reserved.
      </p>

      <div className="flex items-center gap-1">
        {[
          { label: "Privacy Policy", href: "/privacy" },
          { label: "Terms",          href: "/terms"   },
          { label: "Support",        href: "/support" },
        ].map(({ label, href }, i, arr) => (
          <span key={href} className="flex items-center">
            <a
              href={href}
              className="px-4 py-2 rounded-xl text-sm text-slate-500
                hover:text-slate-900 hover:bg-slate-100
                transition-colors duration-150 font-medium"
            >
              {label}
            </a>
            {i < arr.length - 1 && (
              <span className="w-px h-4 bg-slate-200" />
            )}
          </span>
        ))}
      </div>
    </footer>
  );
}