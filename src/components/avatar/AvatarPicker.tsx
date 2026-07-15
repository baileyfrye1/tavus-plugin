import { useQuery } from "@tanstack/react-query";
import styles from "./AvatarPicker.module.css";
import globalStyles from "../../GlobalStyles.module.css";
import { fetchFaces } from "../../api/faceEndpoints";
import { type Face } from "../../types";
import { useState } from "react";
import SkeletonGrid from "../SkeletonGrid";

type AvatarPickerProps = {
  onSelect: (avatarId: string) => void;
  answers: Record<string, string>;
};

function AvatarPicker({ onSelect, answers }: AvatarPickerProps) {
  const [selectedAvatarId, setSelectedAvatarId] = useState<string | undefined>(
    undefined,
  );
  const track = answers.topic;

  // TEMPORARY: Restrict which avatars can be clicked
  const activeAvatarIds = ["r3f427f43c9d", "r4ba1277e4fb"];

  const {
    data: faces,
    isLoading,
    isError,
  } = useQuery<Face[]>({
    queryKey: ["faces", track],
    queryFn: () => fetchFaces(track),
    staleTime: 1000 * 60 * 15,
  });

  if (isLoading) {
    return <SkeletonGrid />;
  }

  if (isError || !faces) {
    return <h3>Could not fetch faces</h3>;
  }

  return (
    <>
      <div className={globalStyles.avatarGrid}>
        {faces.map((face) => (
          <div
            key={face.face_id}
            className={`${styles.avatarWrapper} ${selectedAvatarId === face.face_id ? styles.selected : ""} ${!activeAvatarIds.includes(face.face_id) ? styles.inactive : ""}`}
            onClick={() => {
              if (!activeAvatarIds.includes(face.face_id)) return;

              if (selectedAvatarId === face.face_id) {
                setSelectedAvatarId(undefined);
              } else {
                setSelectedAvatarId(face.face_id);
              }
            }}
          >
            <video
              src={face.thumbnail_video_url}
              className={styles.avatar}
            ></video>
            <button
              aria-label={`Select avatar ${face.face_name}`}
              className={styles.selectAvatar}
              disabled={!activeAvatarIds.includes(face.face_id)}
            ></button>
          </div>
        ))}
      </div>
      <button
        disabled={selectedAvatarId === undefined}
        onClick={() => selectedAvatarId && onSelect(selectedAvatarId)}
        className={styles.avatarButton}
      >
        Next
      </button>
    </>
  );
}
export default AvatarPicker;
