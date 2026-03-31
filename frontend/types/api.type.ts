// ─── Pagination Links ────────────────────────────
export interface PaginationLink {
  url: string | null
  label: string
  page: number | null
  active: boolean
}

export interface PaginationMeta {
  current_page: number
  from: number
  last_page: number
  links: PaginationLink[]
  path: string
  per_page: number
  to: number
  total: number
}

export interface PaginationLinks {
  first: string | null
  last: string | null
  prev: string | null
  next: string | null
}

// ─── With Pagination ─────────────────────────────
export interface PaginatedResponse<T> {
  data: T[]
  links: PaginationLinks
  meta: PaginationMeta
  success: boolean
  message: string
  status: number
}

// ─── Without Pagination ──────────────────────────
export interface ApiResponse<T> {
  data: T[]
  success: boolean
  message: string
  status: number
}

// ─── Single Item Response ─────────────────────────
export interface ApiSingleResponse<T> {
  data: T
  success: boolean
  message: string
  status: number
}