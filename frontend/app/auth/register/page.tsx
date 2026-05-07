import type { Metadata } from "next";
import RegisterForm from "./RegisterForm";

export const metadata: Metadata = {
  title: "BNP - Register",
  description: "Create your free account",
};

export default function RegisterPage() {
  return <RegisterForm />;
}