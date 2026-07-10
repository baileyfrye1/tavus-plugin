import { useState } from "react";
import OnboardingQuestions from "./questions/OnboardingQuestions";
import AvatarPicker from "./avatar/AvatarPicker";
import ModuleGrid from "./modules/ModuleGrid";
import type { OnboardingStage } from "../App";

type OnboardingFlowProps = {
  stage: OnboardingStage;
  setStage: React.Dispatch<React.SetStateAction<OnboardingStage>>;
};

function OnboardingFlow({ stage, setStage }: OnboardingFlowProps) {
  const [answers, setAnswers] = useState<Record<string, string>>({});

  switch (stage) {
    case "questions":
      return (
        <OnboardingQuestions
          onComplete={(userAnswers: Record<string, string>) => {
            setAnswers(userAnswers);
            setStage("avatar");
          }}
        />
      );
    case "avatar":
      return (
        <AvatarPicker
          onSelect={(avatarId) => {
            setAnswers((prev) => ({ ...prev, avatar: avatarId }));
            setStage("library");
          }}
        />
      );
    case "library":
      return <ModuleGrid answers={answers} />;
  }
}
export default OnboardingFlow;
