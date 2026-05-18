export interface AuthUser {
    id: number;
    name: string;
    email: string;
    role: string;
}

export interface AuthState {
    user: AuthUser | null;
    token: string | null;
    isAuthenticated: boolean;
}

export interface LoginRequest {
    login: string;  // → ✅ login
    password: string;
}

export interface LoginResponse {
    user: AuthUser;
    access_token: string;  // → ✅ access_token
    refresh_token: string;
    token_type: string;
    expires_in: number;
    refresh_expires_in: number;
}