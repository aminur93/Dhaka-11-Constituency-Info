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
    email: string;
    password: string;
}

export interface LoginResponse {
    user: AuthUser;
    token: string;
}