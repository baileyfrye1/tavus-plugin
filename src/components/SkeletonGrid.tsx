import globalStyles from "../GlobalStyles.module.css";

type SkeletonGridProps = {
  length?: number;
};

function SkeletonGrid({ length = 3 }: SkeletonGridProps) {
  return (
    <div className={globalStyles.avatarGrid}>
      {Array.from({ length: length }).map(() => (
        <div className={globalStyles.skeleton}></div>
      ))}
    </div>
  );
}

export default SkeletonGrid;
