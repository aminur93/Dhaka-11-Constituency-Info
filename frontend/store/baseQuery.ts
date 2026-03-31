// src/api/baseQuery.ts
import { fetchBaseQuery } from "@reduxjs/toolkit/query/react";
import { config } from "@/constants/config";

const baseQueryWithInterceptor = fetchBaseQuery({
  baseUrl: config.apiUrl,
  prepareHeaders: (headers) => {
    const token = localStorage.getItem("token");
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
        localStorage.removeItem("token");
        window.location.href = "/login";
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