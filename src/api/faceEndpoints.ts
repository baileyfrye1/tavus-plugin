import { API_BASE } from "./config";

export async function fetchFaces(track?: string) {
  const res = await fetch(
    `${API_BASE}/faces${track ? `?track=${track}` : ""}`,
    {
      method: "GET",
    },
  );

  if (!res.ok) {
    throw new Error("Failed to fetch avatar faces");
  }

  return await res.json();
}
