import { createApi, fetchBaseQuery } from "@reduxjs/toolkit/query/react";
import { config } from "@/constants/config";
import type { ApiSingleResponse, LoginRequest, LoginResponse } from "@/types";

export const authApi = createApi({
    reducerPath: "authApi",
    baseQuery: fetchBaseQuery({
        baseUrl: config.apiUrl,
        prepareHeaders: (headers) => {
            const match = document.cookie.match(/(^| )access_token=([^;]+)/);
            const token = match ? match[2] : null;
            headers.set("Accept", "application/json"); // ✅ সবসময়
            if (token) {
                headers.set("Authorization", `Bearer ${token}`);
            }
            return headers;
        },
    }),
    endpoints: (builder) => ({
        login: builder.mutation<ApiSingleResponse<LoginResponse>, LoginRequest>({
            query: (credentials) => ({
                url: "v1/auth/login",
                method: "POST",
                body: credentials,
            }),
        }),
        logout: builder.mutation<ApiSingleResponse<null>, void>({
            query: () => ({
                url: "v1/auth/logout",
                method: "POST",
            }),
        }),
        forgotPassword: builder.mutation<ApiSingleResponse<null>, { email: string }>({
            query: (body) => ({
                url: "v1/auth/forgot-password",
                method: "POST",
                body,
            }),
        }),
        resetPassword: builder.mutation<ApiSingleResponse<null>, {
            token: string;
            email: string;
            password: string;
            password_confirmation: string;
            }>({
            query: (body) => ({
                url: "v1/auth/reset-password",
                method: "POST",
                body,
            }),
        }),
    })
})

export const { useLoginMutation, useLogoutMutation, useForgotPasswordMutation, useResetPasswordMutation } = authApi;
