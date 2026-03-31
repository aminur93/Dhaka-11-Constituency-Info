import { createSlice, type PayloadAction } from "@reduxjs/toolkit";

interface UserState {
    selectedUserId: number | null;
}

const initialState: UserState = {
    selectedUserId: null,
}

const userSlice = createSlice({
    name: "user",
    initialState,
    reducers: {
        setSelectedUser: (state, action: PayloadAction<number>) => {
            state.selectedUserId = action.payload;
        },
        clearSelectedUser: (state) => {
            state.selectedUserId = null;
        },
    },
});

export const { setSelectedUser, clearSelectedUser } = userSlice.actions;
export default userSlice.reducer;

