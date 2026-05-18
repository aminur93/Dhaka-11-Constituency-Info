"use client";

import { useState, useMemo, useRef, useEffect } from "react";
import {
  Plus, Trash2, Pencil, Search, Users,
  ChevronLeft, ChevronRight, ChevronUp, ChevronDown,
  X, Loader2, AlertTriangle, MoreVertical, SlidersHorizontal,
  Upload, User as UserIcon, Eye, EyeOff,
} from "lucide-react";
import {
  useGetUserQuery,
  useCreateUserMutation,
  useUpdateUserMutation,
  useDeleteUserMutation,
} from "@/store/features/user/userApi";
import { useGetRoleQuery } from "@/store/features/role/roleApi";
import type { User, Role } from "@/types";
import { showToast } from "@/lib/alert";

const PER_PAGE_OPTIONS = [5, 10, 20, 30, 50];
const USER_TYPES = ["admin", "manager", "viewer", "editor", "subcription"];

// ── Avatar ────────────────────────────────────────────────
function Avatar({ src, name }: { src?: string; name: string }) {
  const initials = name.split(" ").map((n) => n[0]).join("").slice(0, 2).toUpperCase();
  const colors   = ["bg-violet-500", "bg-blue-500", "bg-emerald-500", "bg-rose-500", "bg-amber-500"];
  const color    = colors[name.charCodeAt(0) % colors.length];

  if (src) {
    return <img src={src} alt={name} className="w-8 h-8 rounded-full object-cover ring-2 ring-white shrink-0" />;
  }
  return (
    <div className={`w-8 h-8 rounded-full ${color} flex items-center justify-center
      text-white text-xs font-bold shrink-0 ring-2 ring-white`}>
      {initials}
    </div>
  );
}

// ── Image Upload ──────────────────────────────────────────
function ImageUpload({
  value, onChange, preview, onPreviewChange,
}: {
  value: File | null;
  onChange: (f: File | null) => void;
  preview: string;
  onPreviewChange: (url: string) => void;
}) {
  const inputRef = useRef<HTMLInputElement>(null);

  const handleFile = (file: File) => {
    onChange(file);
    onPreviewChange(URL.createObjectURL(file));
  };

  return (
    <div className="flex items-center gap-4">
      <div className="w-16 h-16 rounded-xl border-2 border-dashed border-slate-200
        flex items-center justify-center overflow-hidden bg-slate-50 shrink-0">
        {preview ? (
          <img src={preview} alt="preview" className="w-full h-full object-cover" />
        ) : (
          <UserIcon className="w-6 h-6 text-slate-300" />
        )}
      </div>
      <div className="flex-1">
        <button type="button" onClick={() => inputRef.current?.click()}
          className="flex items-center gap-2 px-3 py-2 rounded-lg border border-slate-200
            bg-white text-sm font-medium text-slate-600 hover:border-violet-300
            hover:text-violet-600 transition-colors cursor-pointer">
          <Upload className="w-3.5 h-3.5" />
          {preview ? "Change photo" : "Upload photo"}
        </button>
        <p className="text-xs text-slate-400 mt-1">JPG, PNG up to 2MB</p>
      </div>
      <input ref={inputRef} type="file" accept="image/*" className="hidden"
        onChange={(e) => { const f = e.target.files?.[0]; if (f) handleFile(f); }} />
    </div>
  );
}

// ── Password Input ────────────────────────────────────────
function PasswordInput({
  value, onChange, placeholder, className,
}: {
  value: string; onChange: (v: string) => void; placeholder?: string; className?: string;
}) {
  const [show, setShow] = useState(false);
  return (
    <div className="relative">
      <input value={value} onChange={(e) => onChange(e.target.value)}
        type={show ? "text" : "password"}
        placeholder={placeholder ?? "••••••••"}
        className={`${className} pr-10`} />
      <button type="button" onClick={() => setShow((v) => !v)}
        className="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors">
        {show ? <EyeOff className="w-4 h-4" /> : <Eye className="w-4 h-4" />}
      </button>
    </div>
  );
}

// ── Modal: Create / Edit ──────────────────────────────────
interface UserModalProps {
  mode: "create" | "edit";
  initial?: User;
  roles: Role[];
  onClose: () => void;
}

function UserModal({ mode, initial, roles, onClose }: UserModalProps) {
  const [name, setName]               = useState(initial?.name      ?? "");
  const [email, setEmail]             = useState(initial?.email     ?? "");
  const [phone, setPhone]             = useState(initial?.phone     ?? "");
  const [password, setPassword]       = useState("");
  const [confirmPassword, setConfirm] = useState("");
  const [userType, setUserType]       = useState(initial?.user_type ?? "");
  const [roleId, setRoleId]           = useState<number | "">(initial?.roles?.[0]?.id ?? "");
  const [imageFile, setImageFile]     = useState<File | null>(null);
  const [preview, setPreview]         = useState(initial?.image_url ?? "");
  const [errors, setErrors]           = useState<Record<string, string>>({});

  const [create, { isLoading: creating }] = useCreateUserMutation();
  const [update, { isLoading: updating }] = useUpdateUserMutation();
  const isLoading = creating || updating;

  const validate = () => {
    const e: Record<string, string> = {};
    if (!name.trim())  e.name     = "Name is required.";
    if (!email.trim()) e.email    = "Email is required.";
    if (!phone.trim()) e.phone    = "Phone is required.";
    if (!userType)     e.userType = "User type is required.";
    if (!roleId)       e.roleId   = "Role is required.";
    if (mode === "create") {
      if (!password)                e.password = "Password is required.";
      else if (password.length < 8) e.password = "Password must be at least 8 characters.";
      if (!confirmPassword)         e.confirm  = "Please confirm your password.";
      else if (password !== confirmPassword) e.confirm = "Passwords do not match.";
    } else {
      if (password && password.length < 8) e.password = "Password must be at least 8 characters.";
      if (password && password !== confirmPassword) e.confirm = "Passwords do not match.";
    }
    setErrors(e);
    return Object.keys(e).length === 0;
  };

  const handleSubmit = async () => {
    if (!validate()) return;
    try {
      const formData = new FormData();
      formData.append("name",      name.trim());
      formData.append("email",     email.trim());
      formData.append("phone",     phone.trim());
      formData.append("user_type", userType);
      formData.append("role_id",   String(roleId));
      if (password) {
        formData.append("password",              password);
        formData.append("password_confirmation", confirmPassword);
      }
      if (imageFile) formData.append("image", imageFile);

      if (mode === "create") {
        await create(formData as any).unwrap();
        showToast("User created successfully!", "success");
      } else {
        formData.append("_method", "PUT");
        await update({ id: initial!.id, data: formData as any }).unwrap();
        showToast("User updated successfully!", "success");
      }
      onClose();
    } catch (err: any) {
      const msg = err?.data?.message ?? "Something went wrong.";
      showToast(msg, "error");
    }
  };

  const inputClass = (field: string) =>
    `w-full rounded-xl px-4 py-2.5 text-sm text-slate-800 placeholder-slate-300
    bg-slate-50 border outline-none transition-all duration-200
    focus:ring-2 focus:ring-violet-400/40 focus:border-violet-400 focus:bg-white
    ${errors[field] ? "border-red-400 bg-red-50/60" : "border-slate-200 hover:border-slate-300"}`;

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center px-4">
      <div className="absolute inset-0 bg-black/40 backdrop-blur-sm" onClick={onClose} />
      <div className="relative w-full max-w-lg bg-white rounded-2xl shadow-2xl border border-slate-200
        overflow-hidden max-h-[90vh] flex flex-col">

        {/* Header */}
        <div className="flex items-center justify-between px-6 py-4 border-b border-slate-100 shrink-0">
          <div className="flex items-center gap-3">
            <div className="w-8 h-8 rounded-lg bg-violet-100 flex items-center justify-center">
              <Users className="w-4 h-4 text-violet-600" />
            </div>
            <h2 className="text-sm font-bold text-slate-800">
              {mode === "create" ? "Add User" : "Edit User"}
            </h2>
          </div>
          <button onClick={onClose}
            className="w-7 h-7 flex items-center justify-center rounded-lg
              text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors cursor-pointer">
            <X className="w-4 h-4" />
          </button>
        </div>

        {/* Body */}
        <div className="px-6 py-5 overflow-y-auto flex-1 space-y-4">

          <ImageUpload value={imageFile} onChange={setImageFile} preview={preview} onPreviewChange={setPreview} />

          <div>
            <label className="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-wide">Name</label>
            <input value={name} onChange={(e) => setName(e.target.value)}
              placeholder="e.g. John Doe" className={inputClass("name")} />
            {errors.name && <p className="mt-1 text-xs text-red-500">⚠ {errors.name}</p>}
          </div>

          <div className="grid grid-cols-2 gap-4">
            <div>
              <label className="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-wide">Email</label>
              <input value={email} onChange={(e) => setEmail(e.target.value)}
                type="email" placeholder="you@example.com" className={inputClass("email")} />
              {errors.email && <p className="mt-1 text-xs text-red-500">⚠ {errors.email}</p>}
            </div>
            <div>
              <label className="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-wide">Phone</label>
              <input value={phone} onChange={(e) => setPhone(e.target.value)}
                placeholder="01XXXXXXXXX" className={inputClass("phone")} />
              {errors.phone && <p className="mt-1 text-xs text-red-500">⚠ {errors.phone}</p>}
            </div>
          </div>

          <div className="grid grid-cols-2 gap-4">
            <div>
              <label className="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-wide">
                Password
                {mode === "edit" && <span className="normal-case font-normal text-slate-400 ml-1">(optional)</span>}
              </label>
              <PasswordInput value={password} onChange={setPassword} className={inputClass("password")} />
              {errors.password && <p className="mt-1 text-xs text-red-500">⚠ {errors.password}</p>}
            </div>
            <div>
              <label className="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-wide">
                Confirm Password
              </label>
              <PasswordInput value={confirmPassword} onChange={setConfirm} className={inputClass("confirm")} />
              {errors.confirm && <p className="mt-1 text-xs text-red-500">⚠ {errors.confirm}</p>}
            </div>
          </div>

          <div className="grid grid-cols-2 gap-4">
            <div>
              <label className="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-wide">User Type</label>
              <select value={userType} onChange={(e) => setUserType(e.target.value)}
                className={`${inputClass("userType")} cursor-pointer`}>
                <option value="">Select type</option>
                {USER_TYPES.map((t) => <option key={t} value={t} className="capitalize">{t}</option>)}
              </select>
              {errors.userType && <p className="mt-1 text-xs text-red-500">⚠ {errors.userType}</p>}
            </div>
            <div>
              <label className="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-wide">Role</label>
              <select value={roleId} onChange={(e) => setRoleId(Number(e.target.value))}
                className={`${inputClass("roleId")} cursor-pointer`}>
                <option value="">Select role</option>
                {roles.map((r) => <option key={r.id} value={r.id}>{r.name}</option>)}
              </select>
              {errors.roleId && <p className="mt-1 text-xs text-red-500">⚠ {errors.roleId}</p>}
            </div>
          </div>
        </div>

        {/* Footer */}
        <div className="flex items-center justify-end gap-3 px-6 py-4 border-t border-slate-100 bg-slate-50 shrink-0">
          <button onClick={onClose}
            className="px-4 py-2 rounded-xl text-sm font-medium text-slate-600
              hover:bg-slate-200 transition-colors border border-slate-200 cursor-pointer">
            Cancel
          </button>
          <button onClick={handleSubmit} disabled={isLoading}
            className="flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-white
              bg-gradient-to-r from-violet-600 to-purple-600 hover:from-violet-500 hover:to-purple-500
              disabled:opacity-60 disabled:cursor-not-allowed transition-all shadow-sm cursor-pointer">
            {isLoading && <Loader2 className="w-3.5 h-3.5 animate-spin" />}
            {mode === "create" ? "Add User" : "Save Changes"}
          </button>
        </div>
      </div>
    </div>
  );
}

// ── Modal: Delete Confirm ─────────────────────────────────
interface DeleteModalProps {
  ids: number[];
  onClose: () => void;
  onSuccess: () => void;
}

function DeleteModal({ ids, onClose, onSuccess }: DeleteModalProps) {
  const [deleteUser, { isLoading }] = useDeleteUserMutation();

  const handleDelete = async () => {
    try {
      await Promise.all(ids.map((id) => deleteUser(id).unwrap()));
      showToast(
        ids.length > 1 ? `${ids.length} users deleted!` : "User deleted!",
        "success"
      );
      onSuccess();
      onClose();
    } catch (err: any) {
      const msg = err?.data?.message ?? "Failed to delete user.";
      showToast(msg, "error");
    }
  };

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center px-4">
      <div className="absolute inset-0 bg-black/40 backdrop-blur-sm" onClick={onClose} />
      <div className="relative w-full max-w-sm bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden">
        <div className="px-6 pt-6 pb-4 text-center">
          <div className="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-4">
            <AlertTriangle className="w-6 h-6 text-red-500" />
          </div>
          <h2 className="text-base font-bold text-slate-800 mb-2">
            Delete {ids.length > 1 ? `${ids.length} users` : "user"}?
          </h2>
          <p className="text-sm text-slate-400 leading-relaxed">
            This action cannot be undone. The selected{" "}
            {ids.length > 1 ? "users" : "user"} will be permanently removed.
          </p>
        </div>
        <div className="flex items-center gap-3 px-6 py-4 border-t border-slate-100 bg-slate-50">
          <button onClick={onClose}
            className="flex-1 px-4 py-2 rounded-xl text-sm font-medium text-slate-600
              hover:bg-slate-200 transition-colors border border-slate-200 cursor-pointer">
            Cancel
          </button>
          <button onClick={handleDelete} disabled={isLoading}
            className="flex-1 flex items-center justify-center gap-2 px-4 py-2 rounded-xl
              text-sm font-semibold text-white bg-red-500 hover:bg-red-600
              disabled:opacity-60 disabled:cursor-not-allowed transition-all cursor-pointer">
            {isLoading && <Loader2 className="w-3.5 h-3.5 animate-spin" />}
            Delete
          </button>
        </div>
      </div>
    </div>
  );
}

// ── Row action menu ───────────────────────────────────────
function RowActions({ onEdit, onDelete }: { onEdit: () => void; onDelete: () => void }) {
  const [open, setOpen] = useState(false);
  const ref = useRef<HTMLDivElement>(null);

  useEffect(() => {
    const handler = (e: MouseEvent) => {
      if (ref.current && !ref.current.contains(e.target as Node)) setOpen(false);
    };
    document.addEventListener("mousedown", handler);
    return () => document.removeEventListener("mousedown", handler);
  }, []);

  return (
    <div ref={ref} className="relative flex justify-end">
      <button onClick={() => setOpen((v) => !v)}
        className="w-7 h-7 flex items-center justify-center rounded-lg
          text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors cursor-pointer">
        <MoreVertical className="w-4 h-4" />
      </button>
      {open && (
        <div
          style={{ position: "fixed", right: "34px", zIndex: 9999 }}
          className="w-36 bg-white border border-slate-200 rounded-xl shadow-lg py-1.5">
          <button onClick={() => { onEdit(); setOpen(false); }}
            className="w-full flex items-center gap-2.5 px-4 py-2 text-sm text-slate-600
              hover:text-slate-900 hover:bg-slate-50 transition-colors cursor-pointer">
            <Pencil className="w-3.5 h-3.5 text-slate-400" />
            Edit
          </button>
          <button onClick={() => { onDelete(); setOpen(false); }}
            className="w-full flex items-center gap-2.5 px-4 py-2 text-sm text-red-500
              hover:text-red-600 hover:bg-red-50 transition-colors cursor-pointer">
            <Trash2 className="w-3.5 h-3.5" />
            Delete
          </button>
        </div>
      )}
    </div>
  );
}

// ── Sort Icon ─────────────────────────────────────────────
function SortIcon({ field, sortField, sortDir }: { field: string; sortField: string; sortDir: "asc" | "desc" }) {
  return (
    <span className="flex flex-col ml-0.5">
      <ChevronUp className={`w-2.5 h-2.5 -mb-[1px] transition-colors
        ${sortField === field && sortDir === "asc" ? "text-violet-600" : "text-slate-300"}`} />
      <ChevronDown className={`w-2.5 h-2.5 transition-colors
        ${sortField === field && sortDir === "desc" ? "text-violet-600" : "text-slate-300"}`} />
    </span>
  );
}

// ── Main Page ─────────────────────────────────────────────
export default function UserPage() {
  const [page, setPage]           = useState(1);
  const [perPage, setPerPage]     = useState(10);
  const [search, setSearch]       = useState("");
  const [sortField, setSortField] = useState<"name" | "email">("name");
  const [sortDir, setSortDir]     = useState<"asc" | "desc">("asc");
  const [selected, setSelected]   = useState<number[]>([]);

  const [showCreate, setShowCreate] = useState(false);
  const [editTarget, setEditTarget] = useState<User | null>(null);
  const [deleteIds, setDeleteIds]   = useState<number[] | null>(null);

  const { data, isLoading, isFetching } = useGetUserQuery({ page, pagination: true });
  const { data: roleData } = useGetRoleQuery({ pagination: false });

  const allRoles: Role[] = useMemo(() => {
    if (!roleData) return [];
    const list = "data" in roleData && Array.isArray(roleData.data) ? roleData.data as Role[] : [];
    return list.filter((r) => r.name.toLowerCase() !== "superadmin");
  }, [roleData]);

  const users: User[] = useMemo(() => {
    if (!data) return [];
    if ("data" in data && Array.isArray(data.data)) return data.data as User[];
    return [];
  }, [data]);

  const total: number = useMemo(() => {
    if (!data) return 0;
    return (data as any).meta?.total ?? 0;
  }, [data]);

  const totalPages: number = useMemo(() => {
    if (!data) return 1;
    return (data as any).meta?.last_page ?? 1;
  }, [data]);

  const handleSort = (field: "name" | "email") => {
    if (sortField === field) setSortDir((d) => d === "asc" ? "desc" : "asc");
    else { setSortField(field); setSortDir("asc"); }
  };

  const filtered = useMemo(() => {
    const result = users.filter((u) =>
      u.name.toLowerCase().includes(search.toLowerCase()) ||
      u.email.toLowerCase().includes(search.toLowerCase()) ||
      u.phone?.toLowerCase().includes(search.toLowerCase())
    );
    return result.sort((a, b) => {
      const va = a[sortField].toLowerCase();
      const vb = b[sortField].toLowerCase();
      return sortDir === "asc" ? va.localeCompare(vb) : vb.localeCompare(va);
    });
  }, [users, search, sortField, sortDir]);

  const allSelected  = filtered.length > 0 && filtered.every((u) => selected.includes(u.id));
  const someSelected = filtered.some((u) => selected.includes(u.id)) && !allSelected;

  const toggleAll = () => setSelected(allSelected ? [] : filtered.map((u) => u.id));
  const toggleOne = (id: number) =>
    setSelected((prev) => prev.includes(id) ? prev.filter((x) => x !== id) : [...prev, id]);

  const pageNumbers = useMemo(() => {
    const pages: (number | "...")[] = [];
    if (totalPages <= 3) {
      for (let i = 1; i <= totalPages; i++) pages.push(i);
    } else if (page <= 2) {
      pages.push(1, 2, 3, "...");
    } else if (page >= totalPages - 1) {
      pages.push("...", totalPages - 2, totalPages - 1, totalPages);
    } else {
      pages.push("...", page - 1, page, page + 1, "...");
    }
    return pages;
  }, [page, totalPages]);

  const from = total === 0 ? 0 : (page - 1) * perPage + 1;
  const to   = Math.min(page * perPage, total);

  const userTypeBadge = (type: string) => {
    const map: Record<string, string> = {
      superadmin: "bg-violet-100 text-violet-700 border-violet-200",
      admin:      "bg-blue-100 text-blue-700 border-blue-200",
      moderator:  "bg-amber-100 text-amber-700 border-amber-200",
      editor:     "bg-emerald-100 text-emerald-700 border-emerald-200",
      viewer:     "bg-slate-100 text-slate-600 border-slate-200",
    };
    return map[type?.toLowerCase()] ?? "bg-slate-100 text-slate-600 border-slate-200";
  };

  return (
    <div className="px-4 sm:px-6 lg:px-8 py-6 sm:py-8">

      {/* ── Page Header ── */}
      <div className="flex flex-wrap items-center justify-between gap-4 mb-6">
        <div>
          <h1 className="text-xl sm:text-2xl font-bold text-slate-800">Users</h1>
          <p className="text-sm text-slate-400 mt-0.5">Manage system users</p>
        </div>
        {selected.length > 0 ? (
          <button onClick={() => setDeleteIds(selected)}
            className="flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold
              text-white bg-red-500 hover:bg-red-600 transition-all shadow-sm cursor-pointer">
            <Trash2 className="w-4 h-4" />
            Delete ({selected.length})
          </button>
        ) : (
          <button onClick={() => setShowCreate(true)}
            className="flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold
              text-white bg-violet-600 hover:bg-violet-700 transition-all shadow-sm cursor-pointer">
            <Plus className="w-4 h-4" />
            Add User
          </button>
        )}
      </div>

      {/* ── Table Card ── */}
      <div className="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">

        {/* Toolbar */}
        <div className="flex flex-wrap items-center justify-between gap-3 px-5 py-3.5 border-b border-slate-100">
          <p className="text-sm text-slate-500">
            Showing <span className="font-bold text-slate-700">{filtered.length}</span> of{" "}
            <span className="font-bold text-slate-700">{total}</span> users
          </p>
          <div className="flex flex-wrap items-center gap-2">
            <div className="flex items-center gap-2 bg-slate-50 border border-slate-200 rounded-lg px-3 py-1.5">
              <Search className="w-3.5 h-3.5 text-slate-400 shrink-0" />
              <input value={search} onChange={(e) => setSearch(e.target.value)}
                placeholder="Search users…"
                className="w-36 sm:w-44 bg-transparent text-sm text-slate-700 placeholder-slate-400 outline-none" />
            </div>
            <button className="flex items-center gap-2 px-3.5 py-1.5 rounded-lg border border-slate-200
              bg-white text-sm font-medium text-slate-500 hover:border-slate-300 hover:text-slate-700
              transition-colors cursor-pointer">
              <SlidersHorizontal className="w-3.5 h-3.5" />
              Filters
            </button>
          </div>
        </div>

        {/* Table */}
        <div className="overflow-visible">
          <table className="w-full min-w-[600px] overflow-x-auto">
            <thead>
              <tr className="border-b border-slate-100 bg-slate-50/50">
                <th className="w-10 px-4 py-3 text-left">
                  <input type="checkbox" checked={allSelected}
                    ref={(el) => { if (el) el.indeterminate = someSelected; }}
                    onChange={toggleAll}
                    className="w-4 h-4 rounded border-slate-300 accent-violet-600 cursor-pointer" />
                </th>
                <th className="px-4 py-3 text-left">
                  <div onClick={() => handleSort("name")}
                    className="flex items-center gap-1 cursor-pointer select-none w-fit
                      text-xs font-semibold text-slate-500 uppercase tracking-wide hover:text-slate-700 transition-colors">
                    Member <SortIcon field="name" sortField={sortField} sortDir={sortDir} />
                  </div>
                </th>
                <th className="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Phone</th>
                <th className="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Role</th>
                <th className="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Type</th>
                <th className="px-4 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wide">Actions</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-50">
              {isLoading || isFetching ? (
                <tr>
                  <td colSpan={6} className="px-4 py-16 text-center">
                    <Loader2 className="w-6 h-6 animate-spin text-violet-500 mx-auto" />
                  </td>
                </tr>
              ) : filtered.length === 0 ? (
                <tr>
                  <td colSpan={6} className="px-4 py-16 text-center">
                    <Users className="w-10 h-10 text-slate-200 mx-auto mb-3" />
                    <p className="text-sm text-slate-400">No users found</p>
                  </td>
                </tr>
              ) : (
                filtered.map((user) => (
                  <tr key={user.id} className="hover:bg-slate-50/60 transition-colors">
                    <td className="px-4 py-3">
                      <input type="checkbox" checked={selected.includes(user.id)}
                        onChange={() => toggleOne(user.id)}
                        className="w-4 h-4 rounded border-slate-300 accent-violet-600 cursor-pointer" />
                    </td>
                    <td className="px-4 py-3">
                      <div className="flex items-center gap-3">
                        <Avatar src={user.image_url} name={user.name} />
                        <div className="min-w-0">
                          <p className="text-sm font-semibold text-slate-800 truncate">{user.name}</p>
                          <p className="text-xs text-slate-400 truncate">{user.email}</p>
                        </div>
                      </div>
                    </td>
                    <td className="px-4 py-3">
                      <span className="text-sm text-slate-600">{user.phone}</span>
                    </td>
                    <td className="px-4 py-3">
                      {user.roles?.length > 0 ? (
                        <span className="inline-flex items-center px-2 py-0.5 rounded-md
                          bg-violet-50 text-xs font-semibold text-violet-600 border border-violet-100">
                          {user.roles[0].name}
                        </span>
                      ) : (
                        <span className="text-xs text-slate-400">—</span>
                      )}
                    </td>
                    <td className="px-4 py-3">
                      <span className={`inline-flex items-center px-2 py-0.5 rounded-md
                        text-xs font-semibold border capitalize ${userTypeBadge(user.user_type)}`}>
                        {user.user_type}
                      </span>
                    </td>
                    <td className="px-4 py-3">
                      <RowActions
                        onEdit={() => setEditTarget(user)}
                        onDelete={() => setDeleteIds([user.id])}
                      />
                    </td>
                  </tr>
                ))
              )}
            </tbody>
          </table>
        </div>

        {/* ── Pagination ── */}
        <div className="flex flex-wrap items-center justify-between gap-3 px-5 py-3.5 border-t border-slate-100">
          <div className="flex items-center gap-2 text-sm text-slate-500">
            <span>Show</span>
            <select value={perPage} onChange={(e) => { setPerPage(Number(e.target.value)); setPage(1); }}
              className="bg-white border border-slate-200 text-slate-700 font-bold rounded-lg
                px-2 py-1 text-sm outline-none cursor-pointer hover:border-slate-300 transition-colors">
              {PER_PAGE_OPTIONS.map((n) => <option key={n} value={n}>{n}</option>)}
            </select>
            <span>per page</span>
          </div>

          <div className="flex items-center gap-1.5">
            <span className="text-sm font-bold text-slate-700 mr-2">{from}-{to} of {total}</span>
            <button onClick={() => setPage((p) => Math.max(1, p - 1))} disabled={page === 1}
              className="w-7 h-7 flex items-center justify-center rounded-lg border border-slate-200
                text-slate-500 hover:text-slate-800 hover:border-slate-300 cursor-pointer
                disabled:opacity-40 disabled:cursor-not-allowed transition-colors">
              <ChevronLeft className="w-3.5 h-3.5" />
            </button>
            {pageNumbers.map((p, i) =>
              p === "..." ? (
                <span key={`dots-${i}`} className="w-7 h-7 flex items-center justify-center text-sm text-slate-400">…</span>
              ) : (
                <button key={`page-${p}`} onClick={() => setPage(p as number)}
                  className={`w-7 h-7 flex items-center justify-center rounded-lg text-sm font-medium
                    transition-colors cursor-pointer
                    ${page === p ? "bg-violet-600 text-white shadow-sm" : "border border-slate-200 text-slate-600 hover:border-violet-300 hover:text-violet-600"}`}>
                  {p}
                </button>
              )
            )}
            <button onClick={() => setPage((p) => Math.min(totalPages, p + 1))} disabled={page === totalPages}
              className="w-7 h-7 flex items-center justify-center rounded-lg border border-slate-200
                text-slate-500 hover:text-slate.800 hover:border-slate-300 cursor-pointer
                disabled:opacity-40 disabled:cursor-not-allowed transition-colors">
              <ChevronRight className="w-3.5 h-3.5" />
            </button>
          </div>
        </div>
      </div>

      {/* ── Modals ── */}
      {showCreate && (
        <UserModal mode="create" roles={allRoles} onClose={() => setShowCreate(false)} />
      )}
      {editTarget && (
        <UserModal mode="edit" initial={editTarget} roles={allRoles} onClose={() => setEditTarget(null)} />
      )}
      {deleteIds && (
        <DeleteModal ids={deleteIds} onClose={() => setDeleteIds(null)} onSuccess={() => setSelected([])} />
      )}
    </div>
  );
}