"use client";

import { useState, useMemo, useRef, useEffect } from "react";
import {
  Plus, Trash2, Pencil, Search, ShieldCheck,
  ChevronLeft, ChevronRight, ChevronUp, ChevronDown,
  X, Loader2, AlertTriangle, MoreVertical, SlidersHorizontal,
} from "lucide-react";
import {
  useGetPermissionQuery,
  useCreatePermissionMutation,
  useUpdatePermissionMutation,
  useDeletePermissionMutation,
} from "@/store/features/permission/permissionApi";
import type { Permission, CreatePermissionRequest, UpdatePermissionRequest } from "@/types";
import { showToast } from "@/lib/alert";

const PER_PAGE_OPTIONS = [5, 10, 20, 30, 50];

// ── Dropdown Filter ───────────────────────────────────────
function FilterDropdown({
  label, options, value, onChange,
}: {
  label: string; options: string[]; value: string; onChange: (v: string) => void;
}) {
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
    <div ref={ref} className="relative">
      <button
        onClick={() => setOpen((v) => !v)}
        className={`flex items-center gap-2 px-3.5 py-1.5 rounded-lg border text-sm font-medium
          transition-colors cursor-pointer
          ${value
            ? "border-violet-300 bg-violet-50 text-violet-700"
            : "border-slate-200 bg-white text-slate-500 hover:border-slate-300 hover:text-slate-700"
          }`}>
        {value || label}
        <ChevronDown className="w-3.5 h-3.5" />
      </button>
      {open && (
        <div className="absolute left-0 top-full mt-1 w-44 bg-white border border-slate-200
          rounded-xl shadow-lg py-1.5 z-20">
          <button
            onClick={() => { onChange(""); setOpen(false); }}
            className={`w-full text-left px-4 py-2 text-sm transition-colors cursor-pointer
              ${!value ? "text-violet-600 bg-violet-50 font-medium" : "text-slate-500 hover:bg-slate-50"}`}>
            All
          </button>
          {options.map((opt) => (
            <button key={opt} onClick={() => { onChange(opt); setOpen(false); }}
              className={`w-full text-left px-4 py-2 text-sm transition-colors cursor-pointer
                ${value === opt ? "text-violet-600 bg-violet-50 font-medium" : "text-slate-600 hover:bg-slate-50"}`}>
              {opt}
            </button>
          ))}
        </div>
      )}
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
      <button
        onClick={() => setOpen((v) => !v)}
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

// ── Modal: Create / Edit ──────────────────────────────────
interface PermissionModalProps {
  mode: "create" | "edit";
  initial?: Permission;
  onClose: () => void;
}

function PermissionModal({ mode, initial, onClose }: PermissionModalProps) {
  const [title, setTitle]   = useState(initial?.title ?? "");
  const [name, setName]     = useState(initial?.name  ?? "");
  const [errors, setErrors] = useState<{ title?: string; name?: string }>({});

  const [create, { isLoading: creating }] = useCreatePermissionMutation();
  const [update, { isLoading: updating }] = useUpdatePermissionMutation();
  const isLoading = creating || updating;

  const validate = () => {
    const e: typeof errors = {};
    if (!title.trim()) e.title = "Title is required.";
    if (!name.trim())  e.name  = "Name is required.";
    setErrors(e);
    return Object.keys(e).length === 0;
  };

  const handleSubmit = async () => {
    if (!validate()) return;
    try {
      if (mode === "create") {
        await create({ title: title.trim(), name: name.trim() } as CreatePermissionRequest).unwrap();
        showToast("Permission created successfully!", "success");
      } else {
        await update({ id: initial!.id, data: { title: title.trim(), name: name.trim() } as UpdatePermissionRequest }).unwrap();
        showToast("Permission updated successfully!", "success");
      }
      onClose();
    } catch (err: any) {
      const msg = err?.data?.message ?? "Something went wrong.";
      showToast(msg, "error");
    }
  };

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center px-4">
      <div className="absolute inset-0 bg-black/40 backdrop-blur-sm" onClick={onClose} />
      <div className="relative w-full max-w-md bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden">
        <div className="flex items-center justify-between px-6 py-4 border-b border-slate-100">
          <div className="flex items-center gap-3">
            <div className="w-8 h-8 rounded-lg bg-violet-100 flex items-center justify-center">
              <ShieldCheck className="w-4 h-4 text-violet-600" />
            </div>
            <h2 className="text-sm font-bold text-slate-800">
              {mode === "create" ? "Add Permission" : "Edit Permission"}
            </h2>
          </div>
          <button onClick={onClose}
            className="w-7 h-7 flex items-center justify-center rounded-lg
              text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors cursor-pointer">
            <X className="w-4 h-4" />
          </button>
        </div>

        <div className="px-6 py-5 space-y-4">
          <div>
            <label className="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-wide">Title</label>
            <input value={title} onChange={(e) => setTitle(e.target.value)} placeholder="e.g. Create User"
              className={`w-full rounded-xl px-4 py-2.5 text-sm text-slate-800 placeholder-slate-300
                bg-slate-50 border outline-none transition-all duration-200
                focus:ring-2 focus:ring-violet-400/40 focus:border-violet-400 focus:bg-white
                ${errors.title ? "border-red-400 bg-red-50/60" : "border-slate-200 hover:border-slate-300"}`}
            />
            {errors.title && <p className="mt-1 text-xs text-red-500">⚠ {errors.title}</p>}
          </div>
          <div>
            <label className="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-wide">Name</label>
            <input value={name} onChange={(e) => setName(e.target.value)} placeholder="e.g. permission.create"
              className={`w-full rounded-xl px-4 py-2.5 text-sm text-slate-800 placeholder-slate-300
                bg-slate-50 border outline-none transition-all duration-200
                focus:ring-2 focus:ring-violet-400/40 focus:border-violet-400 focus:bg-white
                ${errors.name ? "border-red-400 bg-red-50/60" : "border-slate-200 hover:border-slate-300"}`}
            />
            {errors.name && <p className="mt-1 text-xs text-red-500">⚠ {errors.name}</p>}
          </div>
        </div>

        <div className="flex items-center justify-end gap-3 px-6 py-4 border-t border-slate-100 bg-slate-50">
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
            {mode === "create" ? "Add Permission" : "Save Changes"}
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
  const [deletePermission, { isLoading }] = useDeletePermissionMutation();

  const handleDelete = async () => {
    try {
      await Promise.all(ids.map((id) => deletePermission(id).unwrap()));
      showToast(
        ids.length > 1 ? `${ids.length} permissions deleted!` : "Permission deleted!",
        "success"
      );
      onSuccess();
      onClose();
    } catch (err: any) {
      const msg = err?.data?.message ?? "Failed to delete permission.";
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
            Delete {ids.length > 1 ? `${ids.length} permissions` : "permission"}?
          </h2>
          <p className="text-sm text-slate-400 leading-relaxed">
            This action cannot be undone. The selected{" "}
            {ids.length > 1 ? "permissions" : "permission"} will be permanently removed.
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
export default function PermissionPage() {
  const [page, setPage]               = useState(1);
  const [perPage, setPerPage]         = useState(10);
  const [search, setSearch]           = useState("");
  const [filterTitle, setFilterTitle] = useState("");
  const [sortField, setSortField]     = useState<"title" | "name">("title");
  const [sortDir, setSortDir]         = useState<"asc" | "desc">("asc");
  const [selected, setSelected]       = useState<number[]>([]);

  const [showCreate, setShowCreate] = useState(false);
  const [editTarget, setEditTarget] = useState<Permission | null>(null);
  const [deleteIds, setDeleteIds]   = useState<number[] | null>(null);

  const { data, isLoading, isFetching } = useGetPermissionQuery({ page, pagination: true });

  const permissions: Permission[] = useMemo(() => {
    if (!data) return [];
    if ("data" in data && Array.isArray(data.data)) return data.data as Permission[];
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

  const titleOptions = useMemo(() =>
    [...new Set(permissions.map((p) => p.title))].sort()
  , [permissions]);

  const handleSort = (field: "title" | "name") => {
    if (sortField === field) setSortDir((d) => d === "asc" ? "desc" : "asc");
    else { setSortField(field); setSortDir("asc"); }
  };

  const filtered = useMemo(() => {
    const result = permissions.filter((p) => {
      const matchSearch = p.title.toLowerCase().includes(search.toLowerCase()) ||
        p.name.toLowerCase().includes(search.toLowerCase());
      const matchTitle = filterTitle ? p.title === filterTitle : true;
      return matchSearch && matchTitle;
    });
    return result.sort((a, b) => {
      const va = a[sortField].toLowerCase();
      const vb = b[sortField].toLowerCase();
      return sortDir === "asc" ? va.localeCompare(vb) : vb.localeCompare(va);
    });
  }, [permissions, search, filterTitle, sortField, sortDir]);

  const allSelected  = filtered.length > 0 && filtered.every((p) => selected.includes(p.id));
  const someSelected = filtered.some((p) => selected.includes(p.id)) && !allSelected;

  const toggleAll = () =>
    setSelected(allSelected ? [] : filtered.map((p) => p.id));

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

  return (
    <div className="px-4 sm:px-6 lg:px-8 py-6 sm:py-8">

      {/* ── Page Header ── */}
      <div className="flex flex-wrap items-center justify-between gap-4 mb-6">
        <div>
          <h1 className="text-xl sm:text-2xl font-bold text-slate-800">Permissions</h1>
          <p className="text-sm text-slate-400 mt-0.5">Manage system permissions</p>
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
            Add Permission
          </button>
        )}
      </div>

      {/* ── Table Card ── */}
      <div className="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">

        {/* Toolbar */}
        <div className="flex flex-wrap items-center justify-between gap-3 px-5 py-3.5 border-b border-slate-100">
          <p className="text-sm text-slate-500">
            Showing <span className="font-bold text-slate-700">{filtered.length}</span> of{" "}
            <span className="font-bold text-slate-700">{total}</span> permissions
          </p>
          <div className="flex flex-wrap items-center gap-2">
            <div className="flex items-center gap-2 bg-slate-50 border border-slate-200 rounded-lg px-3 py-1.5">
              <Search className="w-3.5 h-3.5 text-slate-400 shrink-0" />
              <input value={search} onChange={(e) => setSearch(e.target.value)}
                placeholder="Search permissions…"
                className="w-36 sm:w-44 bg-transparent text-sm text-slate-700 placeholder-slate-400 outline-none" />
            </div>
            <FilterDropdown label="Select a title" options={titleOptions} value={filterTitle} onChange={setFilterTitle} />
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
                  <div onClick={() => handleSort("title")}
                    className="flex items-center gap-1 cursor-pointer select-none w-fit
                      text-xs font-semibold text-slate-500 uppercase tracking-wide hover:text-slate-700 transition-colors">
                    Title <SortIcon field="title" sortField={sortField} sortDir={sortDir} />
                  </div>
                </th>
                <th className="px-4 py-3 text-left">
                  <div onClick={() => handleSort("name")}
                    className="flex items-center gap-1 cursor-pointer select-none w-fit
                      text-xs font-semibold text-slate-500 uppercase tracking-wide hover:text-slate-700 transition-colors">
                    Name <SortIcon field="name" sortField={sortField} sortDir={sortDir} />
                  </div>
                </th>
                <th className="px-4 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wide">
                  Actions
                </th>
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-50">
              {isLoading || isFetching ? (
                <tr>
                  <td colSpan={4} className="px-4 py-16 text-center">
                    <Loader2 className="w-6 h-6 animate-spin text-violet-500 mx-auto" />
                  </td>
                </tr>
              ) : filtered.length === 0 ? (
                <tr>
                  <td colSpan={4} className="px-4 py-16 text-center">
                    <ShieldCheck className="w-10 h-10 text-slate-200 mx-auto mb-3" />
                    <p className="text-sm text-slate-400">No permissions found</p>
                  </td>
                </tr>
              ) : (
                filtered.map((perm) => (
                  <tr key={perm.id} className="hover:bg-slate-50/60 transition-colors">
                    <td className="px-4 py-3">
                      <input type="checkbox" checked={selected.includes(perm.id)}
                        onChange={() => toggleOne(perm.id)}
                        className="w-4 h-4 rounded border-slate-300 accent-violet-600 cursor-pointer" />
                    </td>
                    <td className="px-4 py-3">
                      <span className="text-sm font-medium text-slate-800">{perm.title}</span>
                    </td>
                    <td className="px-4 py-3">
                      <span className="inline-flex items-center px-2 py-0.5 rounded-md
                        bg-slate-100 text-xs font-mono text-slate-600">
                        {perm.name}
                      </span>
                    </td>
                    <td className="px-4 py-3">
                      <RowActions
                        onEdit={() => setEditTarget(perm)}
                        onDelete={() => setDeleteIds([perm.id])}
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
                text-slate-500 hover:text-slate-800 hover:border-slate-300 cursor-pointer
                disabled:opacity-40 disabled:cursor-not-allowed transition-colors">
              <ChevronRight className="w-3.5 h-3.5" />
            </button>
          </div>
        </div>
      </div>

      {/* ── Modals ── */}
      {showCreate && <PermissionModal mode="create" onClose={() => setShowCreate(false)} />}
      {editTarget && <PermissionModal mode="edit" initial={editTarget} onClose={() => setEditTarget(null)} />}
      {deleteIds  && <DeleteModal ids={deleteIds} onClose={() => setDeleteIds(null)} onSuccess={() => setSelected([])} />}
    </div>
  );
}