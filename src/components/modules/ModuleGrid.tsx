import { useQuery } from "@tanstack/react-query";
import { fetchVideos } from "../../api/videoEndpoints";
import type { Video } from "../../types";
import SkeletonGrid from "../SkeletonGrid";
import globalStyles from "../../GlobalStyles.module.css";
import styles from "./ModuleGrid.module.css";
import { useState, useEffect, useRef } from "react";
import { X } from "lucide-react";

type ModuleGridProps = {
  answers: Record<string, string>;
};

function ModuleGrid({ answers }: ModuleGridProps) {
  const [selectedVideo, setSelectedVideo] = useState<Video | null>(null);
  const dialogRef = useRef<HTMLDialogElement>(null);
  const track = answers.topic;

  const {
    data: videos,
    isLoading,
    isError,
  } = useQuery<Video[]>({
    queryKey: ["videos", answers.avatar, track],
    queryFn: () => fetchVideos(answers.avatar, track),
    staleTime: 1000 * 60 * 15,
  });

  useEffect(() => {
    const dialog = dialogRef.current;
    if (!dialog) return;

    if (selectedVideo) {
      dialog.showModal();
      document.body.style.overflow = "hidden";
    } else {
      dialog.close();
      document.body.style.overflow = "";
    }

    return () => {
      document.body.style.overflow = "";
    };
  }, [selectedVideo]);

  function handleClose(e: React.MouseEvent<HTMLDialogElement>) {
    if (e.target === e.currentTarget) {
      setSelectedVideo(null);
    }
  }

  if (isLoading) {
    return <SkeletonGrid />;
  }
  if (isError || !videos) {
    return <h3>Could not fetch videos</h3>;
  }

  return (
    <>
      <div className={globalStyles.avatarGrid}>
        {videos.length === 0 ? (
          <h3>No videos found with that avatar</h3>
        ) : (
          videos.map((video) => {
            const videoTitle = video.video_name.replace(
              /^\[(parent-caregiver|healthcare-provider)\]/g,
              "",
            );
            return (
              <div
                key={video.video_name}
                className={styles.learningModuleWrapper}
                onClick={() => setSelectedVideo(video)}
              >
                <img
                  src={video.still_image_thumbnail_url}
                  alt={`Learning module ${videoTitle}`}
                  className={styles.learningModule}
                />
                <h3 className={styles.learningModuleTitle}>{videoTitle}</h3>
              </div>
            );
          })
        )}
      </div>

      <dialog
        ref={dialogRef}
        className={styles.player}
        onClick={handleClose}
        onClose={() => setSelectedVideo(null)}
      >
        {selectedVideo && (
          <>
            <button
              onClick={() => setSelectedVideo(null)}
              aria-label="Close video"
              className={styles.closeBtn}
            >
              <X width="30" height="30" />
            </button>
            <video
              src={selectedVideo.hosted_url}
              controls
              className={styles.video}
            />
          </>
        )}
      </dialog>
    </>
  );
}

export default ModuleGrid;
