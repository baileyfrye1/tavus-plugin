// Questions
export type UserType = "parent-caregiver" | "healthcare-provider";

export type Question = {
  id: string;
  text: string;
  options: { value: string; label: string }[];
  track?: UserType;
};

// API Responses
export type Face = {
  face_id: string;
  face_name: string;
  thumbnail_video_url: string;
  thumbnail_image_url: string;
  status: string;
  created_at: string;
};

export type Video = {
  video_id: string;
  video_name: string;
  status: string;
  data: {
    script: string;
    start_with_wave: boolean;
  };
  replica_id: string;
  download_url: string;
  hosted_url: string;
  stream_url: string;
  status_details: string;
  created_at: string;
  updated_at: string;
  generation_progress: string;
  still_image_thumbnail_url: string;
  gif_thumbnail_url: string;
};
