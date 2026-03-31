import { configureStore } from "@reduxjs/toolkit";

// Slices
import authReducer from "./features/auth/authSlice";
import userReducer from "./features/user/userSlice";
import roleReducer from "./features/role/roleSlice";
import permissionReducer from "./features/permission/permissionSlice";

// APIs
import { authApi } from "./features/auth/authApi";
import { userApi } from "./features/user/userApi";
import { roleApi } from "./features/role/roleApi";
import { permissionApi } from "./features/permission/permissionApi";

export const store = configureStore({
    reducer: {
        // Slices
        auth: authReducer,
        user: userReducer,
        role: roleReducer,
        permission: permissionReducer,

        // APIs
        [authApi.reducerPath]: authApi.reducer,
        [userApi.reducerPath]: userApi.reducer,
        [roleApi.reducerPath]: roleApi.reducer,
        [permissionApi.reducerPath]: permissionApi.reducer,
    },
    middleware: (getDefaultMiddleware) => getDefaultMiddleware().concat(
        authApi.middleware, 
        userApi.middleware, 
        roleApi.middleware,
        permissionApi.middleware
    ),
});

// Infer the `RootState` and `AppDispatch` types from the store itself
export type RootState = ReturnType<typeof store.getState>;
export type AppDispatch = typeof store.dispatch;