import { z } from "zod"

// ─── Auth ───────────────────────────────────────
export const loginSchema = z.object({
  email: z
    .string()
    .min(1, "Email is required")
    .email("Invalid email address"),
  password: z
    .string()
    .min(6, "Password must be at least 6 characters"),
})

export const forgotPasswordSchema = z.object({
  email: z
    .string()
    .min(1, "Email is required")
    .email("Invalid email address"),
})

export const resetPasswordSchema = z
  .object({
    password: z
      .string()
      .min(6, "Password must be at least 6 characters"),
    password_confirmation: z
      .string()
      .min(1, "Please confirm your password"),
  })
  .refine((data) => data.password === data.password_confirmation, {
    message: "Passwords do not match",
    path: ["password_confirmation"],
  })

// ─── User ───────────────────────────────────────
export const createUserSchema = z.object({
  name: z
    .string()
    .min(1, "Name is required"),
  email: z
    .string()
    .min(1, "Email is required")
    .email("Invalid email address"),
  password: z
    .string()
    .min(6, "Password must be at least 6 characters"),
  phone: z
    .string()
    .optional(),
  role_id: z
    .number({ message: "Role is required" }),
})

export const updateUserSchema = z.object({
  name: z
    .string()
    .min(1, "Name is required"),
  email: z
    .string()
    .min(1, "Email is required")
    .email("Invalid email address"),
  phone: z
    .string()
    .optional(),
  role_id: z
    .number({ message: "Role is required" }),
  status: z.enum(["active", "inactive"]),
})

// ─── Role ───────────────────────────────────────
export const createRoleSchema = z.object({
  name: z
    .string()
    .min(1, "Name is required"),
  level: z
    .string()
    .min(1, "Level is required"),
  permissions: z
    .array(z.number())
    .min(1, "Select at least one permission"),
})

export const updateRoleSchema = z.object({
  name: z
    .string()
    .min(1, "Name is required"),
  level: z
    .string()
    .min(1, "Level is required"),
  permissions: z
    .array(z.number())
    .min(1, "Select at least one permission"),
})

// ─── Inferred Types ─────────────────────────────
export type LoginFormData = z.infer<typeof loginSchema>
export type ForgotPasswordFormData = z.infer<typeof forgotPasswordSchema>
export type ResetPasswordFormData = z.infer<typeof resetPasswordSchema>
export type CreateUserFormData = z.infer<typeof createUserSchema>
export type UpdateUserFormData = z.infer<typeof updateUserSchema>
export type CreateRoleFormData = z.infer<typeof createRoleSchema>
export type UpdateRoleFormData = z.infer<typeof updateRoleSchema>