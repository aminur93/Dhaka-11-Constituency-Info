import { configureStore } from "@reduxjs/toolkit";

// Slices
import authReducer from "./features/auth/authSlice";
import userReducer from "./features/user/userSlice";
import roleReducer from "./features/role/roleSlice";

// APIs
import { authApi } from "./features/auth/authApi";
import { userApi } from "./features/user/userApi";
import { roleApi } from "./features/role/roleApi";

export const store = configureStore({
    reducer: {
        // Slices
        auth: authReducer,
        user: userReducer,
        role: roleReducer,

        // APIs
        [authApi.reducerPath]: authApi.reducer,
        [userApi.reducerPath]: userApi.reducer,
        [roleApi.reducerPath]: roleApi.reducer,
    },
    middleware: (getDefaultMiddleware) => getDefaultMiddleware().concat(
        authApi.middleware, 
        userApi.middleware, 
        roleApi.middleware
    ),
});

// Infer the `RootState` and `AppDispatch` types from the store itself
export type RootState = ReturnType<typeof store.getState>;
export type AppDispatch = typeof store.dispatch;