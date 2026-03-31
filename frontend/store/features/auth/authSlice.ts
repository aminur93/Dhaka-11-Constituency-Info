import { createSlice, type PayloadAction } from "@reduxjs/toolkit";
import type { AuthState, AuthUser } from "@/types";

const initialState: AuthState = {
    user: null,
    token: typeof window !== "undefined" ? localStorage.getItem("token") : null,
    isAuthenticated: !!(typeof window !== "undefined" && localStorage.getItem("token")),
}

const authSlice = createSlice({
    name: "auth",
    initialState,
    reducers: {
        setCredentials: (
            state, 
            action: PayloadAction<{user: AuthUser, token: string}> 
        ) => {
            state.user = action.payload.user;
            state.token = action.payload.token;
            state.isAuthenticated = true;
            localStorage.setItem("token", action.payload.token);
        },

        logout: (state) => {
            state.user = null;
            state.token = null;
            state.isAuthenticated = false;
            localStorage.removeItem("token");
        },
    },
});

export const { setCredentials, logout } = authSlice.actions;
export default authSlice.reducer;