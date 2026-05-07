import { createSlice, type PayloadAction } from "@reduxjs/toolkit";
import type { AuthState, AuthUser } from "@/types";

const TOKEN_KEY = "access_token";

function getCookie(name: string): string | null {
    if (typeof window === "undefined") return null;
    const match = document.cookie.match(new RegExp(`(^| )${name}=([^;]+)`));
    return match ? match[2] : null;
}

const initialState: AuthState = {
    user: null,
    token: getCookie(TOKEN_KEY),
    isAuthenticated: !!getCookie(TOKEN_KEY),
};

const authSlice = createSlice({
    name: "auth",
    initialState,
    reducers: {
        setCredentials: (
            state,
            action: PayloadAction<{ user: AuthUser; token: string }>
        ) => {
            state.user = action.payload.user;
            state.token = action.payload.token;
            state.isAuthenticated = true;
            document.cookie = `${TOKEN_KEY}=${action.payload.token}; path=/; max-age=${60 * 60 * 24 * 7}; SameSite=Lax`;
        },

        logout: (state) => {
            state.user = null;
            state.token = null;
            state.isAuthenticated = false;
            document.cookie = `${TOKEN_KEY}=; path=/; max-age=0`;
        },
    },
});

export const { setCredentials, logout } = authSlice.actions;
export default authSlice.reducer;