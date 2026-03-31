export interface Permission {
    id: number;
    title: string;
    name: string
}

export interface CreatePermissionRequest {
  name: string
  title: string
}

export interface UpdatePermissionRequest {
  name?: string
  title?: string
}