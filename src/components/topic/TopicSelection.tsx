import styles from "./TopicSelection.module.css";
import type { UserType } from "../../types";

type TopicSelectionProps = {
  onSelect: (topic: UserType) => void;
};

function TopicSelection({ onSelect }: TopicSelectionProps) {
  return (
    <div className={styles.topicGrid}>
      <div onClick={() => onSelect("parent-caregiver")}>
        <img src="#" alt="Parent or Caregiver" />
        <h3>Parent or caregiver</h3>
        <p></p>
        <button onClick={() => onSelect("parent-caregiver")}>
          Choose Topic
        </button>
      </div>
      <div onClick={() => onSelect("healthcare-provider")}>
        <img src="#" alt="Healthcare Provider" />
        <h3>Healthcare Provider</h3>
        <p></p>
        <button onClick={() => onSelect("healthcare-provider")}>
          Choose Topic
        </button>
      </div>
    </div>
  );
}

export default TopicSelection;
