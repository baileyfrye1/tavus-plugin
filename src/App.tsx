import { useState } from "react";
import styles from "./App.module.css";
import globalStyles from "./GlobalStyles.module.css";
import OnboardingFlow from "./components/OnboardingFlow";
import { RotateCcw } from "lucide-react";
import type { AnswersType } from "./types";

export type OnboardingStage = "topic" | "questions" | "avatar" | "library";

function App() {
  const [stage, setStage] = useState<OnboardingStage>("topic");
  const [answers, setAnswers] = useState<AnswersType>({});

  const stageText: Record<
    OnboardingStage,
    Record<"heading" | "eyebrow", string>
  > = {
    topic: {
      heading: "Choose Your Learning Module Topic",
      eyebrow:
        "Select which path you would like to view learning modules about",
    },
    questions: {
      heading: "Find Your Learning Modules",
      eyebrow:
        "Answer a few quick questions to get modules matched to your conversation",
    },
    avatar: {
      heading: "Avatar Selection",
      eyebrow: "Pick the avatar you'd like to hear from",
    },
    library: {
      heading: "Library",
      eyebrow: "Browse learning videos",
    },
  };

  return (
    <div className={`${globalStyles.container} ${styles.resources}`}>
      <div className={styles.headingContainer}>
        <div>
          <h2 className={styles.resourcesHeading}>
            {stageText[stage].heading}
          </h2>
          <p className={styles.resourcesEyebrow}>{stageText[stage].eyebrow}</p>
        </div>
        <div className={styles.controlsGroup}>
          <button
            aria-label="Reset learning module selection process"
            onClick={() => {
              setAnswers({});
              setStage("topic");
            }}
            className={styles.resetBtn}
          >
            <RotateCcw />
          </button>
          <p
            className={styles.viewAll}
            onClick={() => {
              setAnswers({});
              setStage("avatar");
            }}
          >
            View All
          </p>
        </div>
      </div>
      <OnboardingFlow
        stage={stage}
        setStage={setStage}
        answers={answers}
        setAnswers={setAnswers}
      />
    </div>
  );
}

export default App;
