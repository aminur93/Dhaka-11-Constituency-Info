import { createSlice, type PayloadAction } from "@reduxjs/toolkit";

interface RoleState {
    selectedRoleId: number | null;
}

const initialState: RoleState = {
    selectedRoleId: null,
}

const roleSlice = createSlice({
    name: "role",
    initialState,
    reducers: {
        setSelectedRole: (state, action: PayloadAction<number>) => {
            state.selectedRoleId = action.payload;
        },
        clearSelectedRole: (state) => {
            state.selectedRoleId = null;
        },
    },
})

export const { setSelectedRole, clearSelectedRole } = roleSlice.actions;
export default roleSlice.reducer;