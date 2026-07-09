import { useState } from "react";
import styles from "./App.module.css";
import OnboardingFlow from "./components/OnboardingFlow";

export type OnboardingStage = "questions" | "avatar" | "library";

function App() {
  const [stage, setStage] = useState<OnboardingStage>("avatar");

  const stageText: Record<OnboardingStage, Record<"heading" | "eyebrow", string>> = {
    "questions": {
      "heading": "Find Your Learning Modules",
      "eyebrow": "Answer a few quick questions to get modules matched to your conversation"
    },
    "avatar": {
      "heading": "Avatar Selection",
      "eyebrow": "Pick the avatar you'd like to hear from"
    },
    "library": {
      "heading": "Library",
      "eyebrow": "Browse learning videos",
    },
  }

  return (
    <div className={`container ${styles.resources}`}>
      <div className={styles.headingContainer}>
        <div>
          <h2 className={styles.resourcesHeading}>{stageText[stage].heading}</h2>
          <p className={styles.resourcesEyebrow}>{stageText[stage].eyebrow}</p>
        </div>
        <p className={styles.viewAll} onClick={() => setStage("library")}>View All</p>
      </div>
      <OnboardingFlow stage={stage} setStage={setStage} />
    </div>
  );
}

export default App;
