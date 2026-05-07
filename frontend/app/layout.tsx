import type { Metadata } from "next";
import Providers from "./providers";
import "./globals.css";

export const metadata: Metadata = {
    title: "BNP",
    description: "BNP is a web application built with Next.js, React, and Redux Toolkit. It provides a user-friendly interface for managing tasks and projects efficiently.",
};

export default function RootLayout({ children }: { children: React.ReactNode }) {
    return (
        <html lang="en">
            <body>
                <Providers>
                    {children}
                </Providers>
            </body>
        </html>
    );
}