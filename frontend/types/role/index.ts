import { Permission } from "../permission";

export interface Role {
    id: number;
    name: string;
    level: number;
    permissions: Permission[];
}

export interface CreateRoleRequest {
    name: string;
    level: number;
    permissions: number[]
}

export interface UpdateRoleRequest {
    name?: string;
    level?: number;
    permissions?: number[]
}