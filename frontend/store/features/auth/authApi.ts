import { createApi, fetchBaseQuery } from "@reduxjs/toolkit/query/react";
import { config } from "@/constants/config";
import type { ApiSingleResponse, LoginRequest, LoginResponse } from "@/types";

export const authApi = createApi({
    reducerPath: "authApi",
    baseQuery: fetchBaseQuery({
        baseUrl: config.apiUrl,
        prepareHeaders: (headers) => {
            const token = localStorage.getItem("token");
            if (token) 
            {
                headers.set("Authorization", `Bearer ${token}`);
                headers.set("Accept", "application/json");
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
    })
})

export const { useLoginMutation, useLogoutMutation } = authApi;
