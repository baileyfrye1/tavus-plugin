import { API_BASE } from "./config";

export async function fetchVideos(faceId: string, track?: string) {
  const res = await fetch(
    `${API_BASE}/videos?face_id=${faceId}${track ? `&track=${track}` : ""}`,
    {
      method: "GET",
    },
  );

  if (!res.ok) {
    throw new Error("Failed to fetch videos");
  }

  return await res.json();
}

export async function fetchVideo(videoId: string, faceId: string) {
  const res = await fetch(`${API_BASE}/video/${videoId}?face_id=${faceId}`, {
    method: "GET",
  });

  if (!res.ok) {
    throw new Error("Failed to fetch video");
  }

  return await res.json();
}
