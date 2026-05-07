import type { Metadata } from "next";
import ForgotPasswordForm from "./ForgotPasswordForm";

export const metadata: Metadata = {
  title: "BNP - Forgot Password",
  description: "Reset your account password",
};

export default function ForgotPasswordPage() {
  return <ForgotPasswordForm />;
}