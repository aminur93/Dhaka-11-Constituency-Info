"use client";

import { useState, useMemo, useRef, useEffect } from "react";
import {
  Plus, Trash2, Pencil, Search, Shield,
  ChevronLeft, ChevronRight, ChevronUp, ChevronDown,
  X, Loader2, AlertTriangle, MoreVertical, SlidersHorizontal,
} from "lucide-react";
import {
  useGetRoleQuery,
  useCreateRoleMutation,
  useUpdateRoleMutation,
  useDeleteRoleMutation,
} from "@/store/features/role/roleApi";
import { useGetPermissionQuery } from "@/store/features/permission/permissionApi";
import type { Role, Permission, CreateRoleRequest, UpdateRoleRequest } from "@/types";
import { showToast } from "@/lib/alert";

const PER_PAGE_OPTIONS = [5, 10, 20, 30, 50];

// ── Permission Group Card ─────────────────────────────────
function PermissionGroupCard({
  title, permissions, selected, onToggle,
}: {
  title: string; permissions: Permission[]; selected: number[]; onToggle: (id: number) => void;
}) {
  const allChecked  = permissions.every((p) => selected.includes(p.id));
  const someChecked = permissions.some((p) => selected.includes(p.id)) && !allChecked;

  const toggleAll = () => {
    if (allChecked) {
      permissions.forEach((p) => { if (selected.includes(p.id)) onToggle(p.id); });
    } else {
      permissions.forEach((p) => { if (!selected.includes(p.id)) onToggle(p.id); });
    }
  };

  const getAction = (name: string) => name.split(".")[1] ?? name;

  return (
    <div className="bg-white border border-slate-200 rounded-xl p-4 hover:border-slate-300 transition-colors">
      <div className="flex items-center gap-2.5 mb-3 pb-3 border-b border-slate-100">
        <input type="checkbox" checked={allChecked}
          ref={(el) => { if (el) el.indeterminate = someChecked; }}
          onChange={toggleAll}
          className="w-4 h-4 rounded border-slate-300 accent-violet-600 cursor-pointer" />
        <span className="text-sm font-bold text-slate-700 capitalize">{title}</span>
        <span className="ml-auto text-xs text-slate-400">
          {permissions.filter((p) => selected.includes(p.id)).length}/{permissions.length}
        </span>
      </div>
      <div className="flex flex-wrap gap-3">
        {permissions.map((perm) => (
          <label key={perm.id} className="flex items-center gap-1.5 cursor-pointer select-none">
            <input type="checkbox" checked={selected.includes(perm.id)}
              onChange={() => onToggle(perm.id)}
              className="w-3.5 h-3.5 rounded border-slate-300 accent-violet-600 cursor-pointer" />
            <span className="text-xs font-medium text-slate-600 capitalize">
              {getAction(perm.name)}
            </span>
          </label>
        ))}
      </div>
    </div>
  );
}

// ── Modal: Create / Edit ──────────────────────────────────
interface RoleModalProps {
  mode: "create" | "edit";
  initial?: Role;
  allPermissions: Permission[];
  onClose: () => void;
}

function RoleModal({ mode, initial, allPermissions, onClose }: RoleModalProps) {
  const [name, setName]         = useState(initial?.name  ?? "");
  const [level, setLevel]       = useState(initial?.level ?? 1);
  const [selectedPerms, setSelectedPerms] = useState<number[]>(
    initial?.permissions?.map((p) => p.id) ?? []
  );
  const [errors, setErrors] = useState<{ name?: string; level?: string }>({});

  const [create, { isLoading: creating }] = useCreateRoleMutation();
  const [update, { isLoading: updating }] = useUpdateRoleMutation();
  const isLoading = creating || updating;

  const groups = useMemo(() => {
    const map: Record<string, Permission[]> = {};
    allPermissions.forEach((p) => {
      const key = p.title.toLowerCase();
      if (!map[key]) map[key] = [];
      map[key].push(p);
    });
    return map;
  }, [allPermissions]);

  const togglePerm = (id: number) =>
    setSelectedPerms((prev) => prev.includes(id) ? prev.filter((x) => x !== id) : [...prev, id]);

  const selectAll = () => setSelectedPerms(allPermissions.map((p) => p.id));
  const clearAll  = () => setSelectedPerms([]);

  const validate = () => {
    const e: typeof errors = {};
    if (!name.trim()) e.name = "Role name is required.";
    if (!level || level < 1) e.level = "Level must be at least 1.";
    setErrors(e);
    return Object.keys(e).length === 0;
  };

  const handleSubmit = async () => {
    if (!validate()) return;
    try {
      if (mode === "create") {
        await create({ name: name.trim(), level, permissions: selectedPerms } as CreateRoleRequest).unwrap();
        showToast("Role created successfully!", "success");
      } else {
        await update({ id: initial!.id, data: { name: name.trim(), level, permissions: selectedPerms } as UpdateRoleRequest }).unwrap();
        showToast("Role updated successfully!", "success");
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
      <div className="relative w-full max-w-2xl bg-white rounded-2xl shadow-2xl border border-slate-200
        overflow-hidden max-h-[90vh] flex flex-col">

        {/* Header */}
        <div className="flex items-center justify-between px-6 py-4 border-b border-slate-100 shrink-0">
          <div className="flex items-center gap-3">
            <div className="w-8 h-8 rounded-lg bg-violet-100 flex items-center justify-center">
              <Shield className="w-4 h-4 text-violet-600" />
            </div>
            <h2 className="text-sm font-bold text-slate-800">
              {mode === "create" ? "Add Role" : "Edit Role"}
            </h2>
          </div>
          <button onClick={onClose}
            className="w-7 h-7 flex items-center justify-center rounded-lg
              text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors cursor-pointer">
            <X className="w-4 h-4" />
          </button>
        </div>

        {/* Body */}
        <div className="px-6 py-5 overflow-y-auto flex-1 space-y-5">

          {/* Name + Level */}
          <div className="grid grid-cols-2 gap-4">
            <div>
              <label className="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-wide">Role Name</label>
              <input value={name} onChange={(e) => setName(e.target.value)} placeholder="e.g. Admin"
                className={`w-full rounded-xl px-4 py-2.5 text-sm text-slate-800 placeholder-slate-300
                  bg-slate-50 border outline-none transition-all duration-200
                  focus:ring-2 focus:ring-violet-400/40 focus:border-violet-400 focus:bg-white
                  ${errors.name ? "border-red-400 bg-red-50/60" : "border-slate-200 hover:border-slate-300"}`}
              />
              {errors.name && <p className="mt-1 text-xs text-red-500">⚠ {errors.name}</p>}
            </div>
            <div>
              <label className="block text-xs font-semibold text-slate-500 mb-1.5 uppercase tracking-wide">Level</label>
              <input type="number" min={1} value={level} onChange={(e) => setLevel(Number(e.target.value))} placeholder="e.g. 1"
                className={`w-full rounded-xl px-4 py-2.5 text-sm text-slate-800 placeholder-slate-300
                  bg-slate-50 border outline-none transition-all duration-200
                  focus:ring-2 focus:ring-violet-400/40 focus:border-violet-400 focus:bg-white
                  ${errors.level ? "border-red-400 bg-red-50/60" : "border-slate-200 hover:border-slate-300"}`}
              />
              {errors.level && <p className="mt-1 text-xs text-red-500">⚠ {errors.level}</p>}
            </div>
          </div>

          {/* Permissions */}
          <div>
            <div className="flex items-center justify-between mb-3">
              <label className="text-xs font-semibold text-slate-500 uppercase tracking-wide">
                Permissions
                <span className="ml-2 text-violet-600 font-bold normal-case">
                  ({selectedPerms.length}/{allPermissions.length} selected)
                </span>
              </label>
              <div className="flex items-center gap-2">
                <button onClick={selectAll}
                  className="text-xs text-violet-600 hover:text-violet-700 font-semibold cursor-pointer transition-colors">
                  Select all
                </button>
                <span className="text-slate-300">|</span>
                <button onClick={clearAll}
                  className="text-xs text-slate-400 hover:text-slate-600 font-semibold cursor-pointer transition-colors">
                  Clear all
                </button>
              </div>
            </div>
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
              {Object.entries(groups).map(([title, perms]) => (
                <PermissionGroupCard
                  key={title} title={title} permissions={perms}
                  selected={selectedPerms} onToggle={togglePerm}
                />
              ))}
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
            {mode === "create" ? "Add Role" : "Save Changes"}
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
  const [deleteRole, { isLoading }] = useDeleteRoleMutation();

  const handleDelete = async () => {
    try {
      await Promise.all(ids.map((id) => deleteRole(id).unwrap()));
      showToast(
        ids.length > 1 ? `${ids.length} roles deleted!` : "Role deleted!",
        "success"
      );
      onSuccess();
      onClose();
    } catch (err: any) {
      const msg = err?.data?.message ?? "Failed to delete role.";
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
            Delete {ids.length > 1 ? `${ids.length} roles` : "role"}?
          </h2>
          <p className="text-sm text-slate-400 leading-relaxed">
            This action cannot be undone. The selected{" "}
            {ids.length > 1 ? "roles" : "role"} will be permanently removed.
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
export default function RolePage() {
  const [page, setPage]           = useState(1);
  const [perPage, setPerPage]     = useState(10);
  const [search, setSearch]       = useState("");
  const [sortField, setSortField] = useState<"name" | "level">("name");
  const [sortDir, setSortDir]     = useState<"asc" | "desc">("asc");
  const [selected, setSelected]   = useState<number[]>([]);

  const [showCreate, setShowCreate] = useState(false);
  const [editTarget, setEditTarget] = useState<Role | null>(null);
  const [deleteIds, setDeleteIds]   = useState<number[] | null>(null);

  const { data, isLoading, isFetching } = useGetRoleQuery({ page, pagination: true });
  const { data: permData } = useGetPermissionQuery({ pagination: false });

  const allPermissions: Permission[] = useMemo(() => {
    if (!permData) return [];
    if ("data" in permData && Array.isArray(permData.data)) return permData.data as Permission[];
    return [];
  }, [permData]);

  const roles: Role[] = useMemo(() => {
    if (!data) return [];
    if ("data" in data && Array.isArray(data.data)) return data.data as Role[];
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

  const handleSort = (field: "name" | "level") => {
    if (sortField === field) setSortDir((d) => d === "asc" ? "desc" : "asc");
    else { setSortField(field); setSortDir("asc"); }
  };

  const filtered = useMemo(() => {
    const result = roles.filter((r) =>
      r.name.toLowerCase().includes(search.toLowerCase())
    );
    return result.sort((a, b) => {
      if (sortField === "level") return sortDir === "asc" ? a.level - b.level : b.level - a.level;
      return sortDir === "asc"
        ? a.name.toLowerCase().localeCompare(b.name.toLowerCase())
        : b.name.toLowerCase().localeCompare(a.name.toLowerCase());
    });
  }, [roles, search, sortField, sortDir]);

  const allSelected  = filtered.length > 0 && filtered.every((r) => selected.includes(r.id));
  const someSelected = filtered.some((r) => selected.includes(r.id)) && !allSelected;

  const toggleAll = () => setSelected(allSelected ? [] : filtered.map((r) => r.id));
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
          <h1 className="text-xl sm:text-2xl font-bold text-slate-800">Roles</h1>
          <p className="text-sm text-slate-400 mt-0.5">Manage system roles and permissions</p>
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
            Add Role
          </button>
        )}
      </div>

      {/* ── Table Card ── */}
      <div className="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">

        {/* Toolbar */}
        <div className="flex flex-wrap items-center justify-between gap-3 px-5 py-3.5 border-b border-slate-100">
          <p className="text-sm text-slate-500">
            Showing <span className="font-bold text-slate-700">{filtered.length}</span> of{" "}
            <span className="font-bold text-slate-700">{total}</span> roles
          </p>
          <div className="flex flex-wrap items-center gap-2">
            <div className="flex items-center gap-2 bg-slate-50 border border-slate-200 rounded-lg px-3 py-1.5">
              <Search className="w-3.5 h-3.5 text-slate-400 shrink-0" />
              <input value={search} onChange={(e) => setSearch(e.target.value)}
                placeholder="Search roles…"
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
                    Name <SortIcon field="name" sortField={sortField} sortDir={sortDir} />
                  </div>
                </th>
                <th className="px-4 py-3 text-left">
                  <div onClick={() => handleSort("level")}
                    className="flex items-center gap-1 cursor-pointer select-none w-fit
                      text-xs font-semibold text-slate-500 uppercase tracking-wide hover:text-slate-700 transition-colors">
                    Level <SortIcon field="level" sortField={sortField} sortDir={sortDir} />
                  </div>
                </th>
                <th className="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">
                  Permissions
                </th>
                <th className="px-4 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wide">
                  Actions
                </th>
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-50">
              {isLoading || isFetching ? (
                <tr>
                  <td colSpan={5} className="px-4 py-16 text-center">
                    <Loader2 className="w-6 h-6 animate-spin text-violet-500 mx-auto" />
                  </td>
                </tr>
              ) : filtered.length === 0 ? (
                <tr>
                  <td colSpan={5} className="px-4 py-16 text-center">
                    <Shield className="w-10 h-10 text-slate-200 mx-auto mb-3" />
                    <p className="text-sm text-slate-400">No roles found</p>
                  </td>
                </tr>
              ) : (
                filtered.map((role) => (
                  <tr key={role.id} className="hover:bg-slate-50/60 transition-colors">
                    <td className="px-4 py-3">
                      <input type="checkbox" checked={selected.includes(role.id)}
                        onChange={() => toggleOne(role.id)}
                        className="w-4 h-4 rounded border-slate-300 accent-violet-600 cursor-pointer" />
                    </td>
                    <td className="px-4 py-3">
                      <span className="text-sm font-semibold text-slate-800">{role.name}</span>
                    </td>
                    <td className="px-4 py-3">
                      <span className="inline-flex items-center px-2 py-0.5 rounded-md
                        bg-violet-50 text-xs font-semibold text-violet-600 border border-violet-100">
                        Level {role.level}
                      </span>
                    </td>
                    <td className="px-4 py-3">
                      <div className="flex flex-wrap gap-1 max-w-sm">
                        {role.permissions?.slice(0, 3).map((p) => (
                          <span key={p.id} className="inline-flex items-center px-2 py-0.5 rounded-md
                            bg-slate-100 text-xs font-mono text-slate-600">
                            {p.name}
                          </span>
                        ))}
                        {role.permissions?.length > 3 && (
                          <span className="inline-flex items-center px-2 py-0.5 rounded-md
                            bg-slate-100 text-xs text-slate-500 font-medium">
                            +{role.permissions.length - 3} more
                          </span>
                        )}
                      </div>
                    </td>
                    <td className="px-4 py-3">
                      <RowActions
                        onEdit={() => setEditTarget(role)}
                        onDelete={() => setDeleteIds([role.id])}
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
      {showCreate && (
        <RoleModal mode="create" allPermissions={allPermissions} onClose={() => setShowCreate(false)} />
      )}
      {editTarget && (
        <RoleModal mode="edit" initial={editTarget} allPermissions={allPermissions} onClose={() => setEditTarget(null)} />
      )}
      {deleteIds && (
        <DeleteModal ids={deleteIds} onClose={() => setDeleteIds(null)} onSuccess={() => setSelected([])} />
      )}
    </div>
  );
}