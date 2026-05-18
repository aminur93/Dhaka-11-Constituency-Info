import { createApi } from "@reduxjs/toolkit/query/react";
import { customBaseQuery } from "@/store/baseQuery";
import type {
    Permission,
    CreatePermissionRequest,
    UpdatePermissionRequest,
    PaginatedResponse,
    ApiSingleResponse
} from "@/types";

export const permissionApi = createApi({
    reducerPath: "permissionApi",

    baseQuery: customBaseQuery,

    tagTypes: ["Permission"],

    endpoints: (builder) => ({
        getPermission: builder.query<PaginatedResponse<Permission> | ApiSingleResponse<Permission>, { page?: number; pagination?: boolean }>({
            query: ({ page = 1, pagination = true } = {}) =>
                    pagination
                        ? `/v1/admin/permission?page=${page}`
                        : `/v1/admin/permission?pagination=false`,
            providesTags: ["Permission"],
        }),

        getPermissionById: builder.query<ApiSingleResponse<Permission>, number>({
            query: (id) => `/v1/admin/permission/${id}`,
            providesTags: ["Permission"],
        }),

        createPermission: builder.mutation<ApiSingleResponse<Permission>, CreatePermissionRequest>({
            query: (data) => ({
                url: "v1/admin/permission",
                method: "POST",
                body: data,
            }),
            invalidatesTags: ["Permission"],
        }),

        updatePermission: builder.mutation<ApiSingleResponse<Permission>, { id: number; data: UpdatePermissionRequest}> ({
            query: ({id, data}) => ({
                url: `v1/admin/permission/${id}`,
                method: "PUT",
                body: data,
            }),
            invalidatesTags: ["Permission"],
        }),

        deletePermission: builder.mutation<ApiSingleResponse<null>, number>({
            query: (id) => ({
                url: `v1/admin/permission/${id}`,
                method: "DELETE",
            }),
            invalidatesTags: ["Permission"],
        }),
    }),
})

export const {
    useGetPermissionQuery,
    useGetPermissionByIdQuery,
    useCreatePermissionMutation,
    useUpdatePermissionMutation,
    useDeletePermissionMutation,
} = permissionApi