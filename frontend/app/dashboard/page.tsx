import Dashboard from "@/components/layout/dashboard/Dashboard";
import { Metadata } from "next";

export const metadata: Metadata = {
  title: "BNP - Dashboard"
};

export default function DashboardPage() {
  return <Dashboard />;
}