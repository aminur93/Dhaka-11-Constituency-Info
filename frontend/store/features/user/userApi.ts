import { createApi, fetchBaseQuery } from "@reduxjs/toolkit/query/react";
import { config } from "@/constants/config";
import { customBaseQuery } from "@/store/baseQuery";

import type {
    User,
    CreateUserRequest,
    UpdateUserRequest,
    PaginatedResponse,
    ApiSingleResponse
} from "@/types";


export const userApi = createApi({
    reducerPath: "userApi",

    baseQuery: customBaseQuery,

    tagTypes: ["User"],

    endpoints: (builder) => ({
        getUser: builder.query<
            PaginatedResponse<User> | ApiSingleResponse<User>, { page?: number; pagination?: boolean }
        >({
            query: ({ page = 1, pagination = true } = {}) => pagination ? `/v1/admin/user?page=${page}` : `/v1/admin/user?pagination=false`,
            providesTags: ["User"],
        }),

        getUserById: builder.query<ApiSingleResponse<User>, number>({
            query: (id) => `/v1/admin/user/${id}`,
            providesTags: ["User"],
        }),

        createUser: builder.mutation<ApiSingleResponse<User>, CreateUserRequest>({
            query: (data) => ({
                url: "/v1/admin/user",
                method: "POST",
                body: data,
            }),
            invalidatesTags: ["User"],
        }),

        updateUser: builder.mutation<ApiSingleResponse<User>, { id: number; data: UpdateUserRequest}> ({
            query: ({id, data}) => ({
                url: `/v1/admin/user/${id}`,
                method: "POST",
                body: data,
            }),
            invalidatesTags: ["User"],
        }),

        deleteUser: builder.mutation<ApiSingleResponse<null>, number>({
            query: (id) => ({
                url: `/v1/admin/user/${id}`,
                method: "DELETE",
            }),
            invalidatesTags: ["User"],
        }),
    }),
})

export const {
    useGetUserQuery,
    useGetUserByIdQuery,
    useCreateUserMutation,
    useUpdateUserMutation,
    useDeleteUserMutation,
} = userApi