import { Role } from "../role";

export interface User {
    id: number;
    name: string;
    email: string;
    phone: string;
    image: string;
    image_url: string;
    user_type: string;
    password: string;
    roles: Role;
}

export interface CreateUserRequest {
    name: string;
    email: string;
    phone: string;
    image: string;
    user_type: string;
    password: string;
    role_id: number;
}

export interface UpdateUserRequest {
    name?: string;
    email?: string;
    phone?: string;
    image?: string;
    user_type?: string;
    password?: string;
    role_id?: number;
}