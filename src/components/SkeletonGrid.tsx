import globalStyles from "../GlobalStyles.module.css";

type SkeletonGridProps = {
  length?: number;
};

function SkeletonGrid({ length = 3 }: SkeletonGridProps) {
  return (
    <div className={globalStyles.avatarGrid}>
      {Array.from({ length }).map((_, i) => (
        <div className={globalStyles.skeleton} key={i}></div>
      ))}
    </div>
  );
}

export default SkeletonGrid;
