import { createApi } from "@reduxjs/toolkit/query/react";
import { customBaseQuery } from "@/store/baseQuery";
import type {
    Role,
    CreateRoleRequest,
    UpdateRoleRequest,
    PaginatedResponse,
    ApiSingleResponse
} from "@/types";

export const roleApi = createApi({
    reducerPath: "roleApi",

    baseQuery: customBaseQuery,

    tagTypes: ["Role"],

    endpoints: (builder) => ({
        getRole: builder.query<PaginatedResponse<Role> | ApiSingleResponse<Role>, { page?: number; pagination?: boolean }>({
            query: ({ page = 1, pagination = true } = {}) => pagination ? `/v1/admin/roles?page=${page}` : `/v1/admin/roles?pagination=false`,
            providesTags: ["Role"],
        }),

        getRoleById: builder.query<ApiSingleResponse<Role>, number>({
            query: (id) => `/v1/admin/roles/${id}`,
            providesTags: ["Role"],
        }),

        createRole: builder.mutation<ApiSingleResponse<Role>, CreateRoleRequest>({
            query: (data) => ({
                url: "v1/admin/roles",
                method: "POST",
                body: data,
            }),
            invalidatesTags: ["Role"],
        }),

        updateRole: builder.mutation<ApiSingleResponse<Role>, { id: number; data: UpdateRoleRequest}> ({
            query: ({id, data}) => ({
                url: `v1/admin/roles/${id}`,
                method: "PUT",
                body: data,
            }),
            invalidatesTags: ["Role"],
        }),

        deleteRole: builder.mutation<ApiSingleResponse<null>, number>({
            query: (id) => ({
                url: `v1/admin/roles/${id}`,
                method: "DELETE",
            }),
            invalidatesTags: ["Role"],
        }),
    }),
});

export const {
    useGetRoleQuery,
    useGetRoleByIdQuery,
    useCreateRoleMutation,
    useUpdateRoleMutation,
    useDeleteRoleMutation,
} = roleApi