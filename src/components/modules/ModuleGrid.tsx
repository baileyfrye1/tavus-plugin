import styles from "../avatar/AvatarPicker.module.css";

type ModuleGridProps = {
  answers: Record<string, string>;
};

function ModuleGrid({ answers }: ModuleGridProps) {
  console.log(answers);
  return (
    <div className={styles.avatarGrid}>
      {Array.from({ length: 3 }).map(() => (
        <div className={styles.skeleton}></div>
      ))}
    </div>
  );
}

export default ModuleGrid;
