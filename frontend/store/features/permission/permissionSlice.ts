import { createSlice, type PayloadAction } from "@reduxjs/toolkit"

interface PermissionState {
  selectedPermissionId: number | null
}

const initialState: PermissionState = {
  selectedPermissionId: null,
}

const permissionSlice = createSlice({
  name: "permission",
  initialState,
  reducers: {
    setSelectedPermission: (state, action: PayloadAction<number>) => {
      state.selectedPermissionId = action.payload
    },
    clearSelectedPermission: (state) => {
      state.selectedPermissionId = null
    },
  },
})

export const { setSelectedPermission, clearSelectedPermission } = permissionSlice.actions
export default permissionSlice.reducer