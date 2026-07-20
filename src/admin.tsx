import { createRoot } from "react-dom/client";
import "./index.css";
import { QueryClient, QueryClientProvider } from "@tanstack/react-query";
import SettingsPage from "./components/admin/SettingsPage";

const queryClient = new QueryClient();

createRoot(document.getElementById("tavus-settings")!).render(
  <QueryClientProvider client={queryClient}>
    <SettingsPage />
  </QueryClientProvider>,
);
