import { useState } from "react";
import "./App.css";
import OnboardingFlow from "./components/OnboardingFlow";

export type OnboardingStage = "questions" | "avatar" | "complete";

function App() {
  const [stage, setStage] = useState<OnboardingStage>("questions");
  const stageText: Record<OnboardingStage, Record<"heading" | "eyebrow", string>> = {
    "questions": {
      "heading": "Find Your Learning Modules",
      "eyebrow": "Answer a few quick questions to get modules matched to your conversation"
    },
    "avatar": {
      "heading": "Avatar Selection",
      "eyebrow": "Pick the avatar you'd like to hear from"
    },
    "complete": {
      "heading": "Library",
      "eyebrow": "Browse learning videos",
    },
  }

  return (
    <div className="container resources">
      <h2 className="resources__heading">{stageText[stage].heading}</h2>
      <p className="resources__eyebrow">{stageText[stage].eyebrow}</p>
      <OnboardingFlow stage={stage} setStage={setStage} />
    </div>
  );
}

export default App;
