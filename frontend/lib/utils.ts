// lib/utils.ts

import { clsx, type ClassValue } from "clsx";
import { twMerge } from "tailwind-merge";

// ─── shadcn cn() helper ─────────────────────────
export function cn(...inputs: ClassValue[]) {
  return twMerge(clsx(inputs))
}

// ─── Storage Image URL ──────────────────────────
export function storageUrl(path: string): string {
  return `${process.env.NEXT_PUBLIC_STORAGE_URL}/${path}`
}

// ─── Date Format ────────────────────────────────
export function formatDate(date: string): string {
  return new Date(date).toLocaleDateString("en-GB", {
    day: "2-digit",
    month: "short",
    year: "numeric",
  })
}

// ─── Truncate Text ──────────────────────────────
export function truncate(text: string, length: number = 50): string {
  if (text.length <= length) return text
  return text.slice(0, length) + "..."
}

// ─── Capitalize ─────────────────────────────────
export function capitalize(text: string): string {
  return text.charAt(0).toUpperCase() + text.slice(1).toLowerCase()
}

// ─── Slug Generate ──────────────────────────────
export function slugify(text: string): string {
  return text
    .toLowerCase()
    .replace(/\s+/g, "-")
    .replace(/[^\w-]+/g, "")
}