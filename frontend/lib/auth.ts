const TOKEN_KEY = "token"
const USER_KEY = "user"

// ─── Token ──────────────────────────────────────
export const getToken = (): string | null => {
  if (typeof window === "undefined") return null
  return localStorage.getItem(TOKEN_KEY)
}

export const setToken = (token: string): void => {
  localStorage.setItem(TOKEN_KEY, token)
}

export const removeToken = (): void => {
  localStorage.removeItem(TOKEN_KEY)
}

// ─── User ───────────────────────────────────────
export const getUser = () => {
  if (typeof window === "undefined") return null
  const user = localStorage.getItem(USER_KEY)
  return user ? JSON.parse(user) : null
}

export const setUser = (user: object): void => {
  localStorage.setItem(USER_KEY, JSON.stringify(user))
}

export const removeUser = (): void => {
  localStorage.removeItem(USER_KEY)
}

// ─── Auth Check ─────────────────────────────────
export const isAuthenticated = (): boolean => {
  return !!getToken()
}

// ─── Clear All ──────────────────────────────────
export const clearAuth = (): void => {
  removeToken()
  removeUser()
}