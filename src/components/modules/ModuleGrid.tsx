import { useQuery } from "@tanstack/react-query";
import { fetchVideos } from "../../api/videoEndpoints";
import type { Video } from "../../types";
import SkeletonGrid from "../SkeletonGrid";
import globalStyles from "../../GlobalStyles.module.css";
import styles from "./ModuleGrid.module.css";

type ModuleGridProps = {
  answers: Record<string, string>;
};

function ModuleGrid({ answers }: ModuleGridProps) {
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

  if (isLoading) {
    return <SkeletonGrid />;
  }

  if (isError || !videos) {
    return <h3>Could not fetch videos</h3>;
  }

  return (
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
            <div className={styles.learningModuleWrapper}>
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
  );
}

export default ModuleGrid;
