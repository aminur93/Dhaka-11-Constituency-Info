import { useDispatch, useSelector } from "react-redux";
import type { RootState, AppDispatch } from "./index";

// Custom hooks for using the Redux store in a type-safe way
export const useAppDispatch = () => useDispatch<AppDispatch>()
export const useAppSelector = <T>(selector: (state: RootState) => T) =>
  useSelector(selector)