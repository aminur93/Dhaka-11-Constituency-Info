import Swal from "sweetalert2"

// ─── Success ────────────────────────────────────
export const showSuccess = (message: string) => {
  Swal.fire({
    icon: "success",
    title: "Success",
    text: message,
    timer: 2000,
    showConfirmButton: false,
  })
}

// ─── Error ──────────────────────────────────────
export const showError = (message: string) => {
  Swal.fire({
    icon: "error",
    title: "Error",
    text: message,
    timer: 3000,
    showConfirmButton: false,
  })
}

// ─── Warning ────────────────────────────────────
export const showWarning = (message: string) => {
  Swal.fire({
    icon: "warning",
    title: "Warning",
    text: message,
    timer: 3000,
    showConfirmButton: false,
  })
}

// ─── Toast (corner notification) ────────────────
export const showToast = (
  message: string,
  icon: "success" | "error" | "warning" | "info" = "success"
) => {
  Swal.fire({
    toast: true,
    position: "top-end",
    icon,
    title: message,
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
  })
}

// ─── Confirm (delete এর আগে) ────────────────────
export const showConfirm = async (
  message: string,
  confirmText: string = "Yes, delete!",
  cancelText: string = "Cancel"
): Promise<boolean> => {
  const result = await Swal.fire({
    icon: "warning",
    title: "Are you sure?",
    text: message,
    showCancelButton: true,
    confirmButtonText: confirmText,
    cancelButtonText: cancelText,
    confirmButtonColor: "#e11d48",
    cancelButtonColor: "#6b7280",
  })
  return result.isConfirmed
}

// ─── Loading ─────────────────────────────────────
export const showLoading = (message: string = "Please wait...") => {
  Swal.fire({
    title: message,
    allowOutsideClick: false,
    allowEscapeKey: false,
    showConfirmButton: false,
    didOpen: () => Swal.showLoading(),
  })
}

export const hideLoading = () => {
  Swal.close()
}