import axios from "axios";
import { config } from "@/constants/config";

const axiosInstance = axios.create({
    baseURL: config.apiUrl,
    timeout: 10000,
    headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
    }
});

axiosInstance.interceptors.request.use(
    (reqConfig) => {
        const token = localStorage.getItem("token");

        if (token)
        {
            reqConfig.headers.Authorization = `Bearer ${token}`;
        }

        return reqConfig;
    },

    (error) => Promise.reject(error)
)

axiosInstance.interceptors.response.use(
    (response) => response,
    (error) => {

        const status = error.response?.status;

        if (status === 401)
        {
            localStorage.removeItem("token");
            window.location.href = "/login";
        }

        if (status === 403)
        {
            window.location.href = "/forbidden";
        }

        if (status === 500)
        {
            window.location.href = "/error";
            console.log("Server error: ", error.response?.data?.message || error.message);
        }

        return Promise.reject(error);
    },
)


export default axiosInstance;