// src/api/baseQuery.ts
import { fetchBaseQuery } from "@reduxjs/toolkit/query/react";
import { config } from "@/constants/config";

const baseQueryWithInterceptor = fetchBaseQuery({
  baseUrl: config.apiUrl,
  prepareHeaders: (headers) => {
    const match = document.cookie.match(/(^| )access_token=([^;]+)/);
    const token = match ? match[2] : null;
    if (token) {
        headers.set("Authorization", `Bearer ${token}`);
    }
    headers.set("Accept", "application/json");
    return headers;
},
});

export const customBaseQuery = async (args: any, api: any, extraOptions: any) => {
  try {
    const result = await baseQueryWithInterceptor(args, api, extraOptions);

    if (result.error) {
      const status = result.error.status;

      if (status === 401) {
          document.cookie = "access_token=; path=/; max-age=0";
          window.location.href = "/auth/login";
      }

      if (status === 403) {
        window.location.href = "/forbidden";
      }

      if (status === 500) {
        console.log("Server error:", (result.error.data as any)?.message || result.error);
        window.location.href = "/error";
      }
    }

    return result;
  } catch (error) {
    console.error("Unexpected error:", error);
    return { error };
  }
};