import type { Metadata } from "next";
import LoginForm from "./LoginForm";

export const metadata: Metadata = {
  title: "BNP - Login",
  description: "Sign in to your dashboard",
};

export default function LoginPage() {
  return <LoginForm />;
}