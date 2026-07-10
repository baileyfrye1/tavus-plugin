import { useQuery } from "@tanstack/react-query";
import styles from "./AvatarPicker.module.css";
import { fetchFaces } from "../../api/faceEndpoints";
import { type Face } from "../../types";
import { useState } from "react";

type AvatarPickerProps = {
  onSelect: (avatarId: string) => void;
};

function AvatarPicker({ onSelect }: AvatarPickerProps) {
  const [selectedAvatarId, setSelectedAvatarId] = useState<string | undefined>(
    undefined,
  );

  const {
    data: faces,
    isLoading,
    isError,
  } = useQuery<Face[]>({
    queryKey: ["faces"],
    queryFn: () => fetchFaces(),
  });

  if (isLoading) {
    return (
      <div className={styles.avatarGrid}>
        {Array.from({ length: 3 }).map(() => (
          <div className={styles.skeleton}></div>
        ))}
      </div>
    );
  }

  if (isError || !faces) {
    return (
      <>
        <h3>Could not fetch faces</h3>
      </>
    );
  }

  return (
    <>
      <div className={styles.avatarGrid}>
        {faces.map((face) => (
          <div
            className={`${styles.avatarWrapper} ${selectedAvatarId === face.face_id ? styles.selected : ""}`}
            onClick={() => {
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
