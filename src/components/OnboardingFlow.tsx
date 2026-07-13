import OnboardingQuestions from "./questions/OnboardingQuestions";
import AvatarPicker from "./avatar/AvatarPicker";
import ModuleGrid from "./modules/ModuleGrid";
import type { OnboardingStage } from "../App";

type OnboardingFlowProps = {
  stage: OnboardingStage;
  setStage: React.Dispatch<React.SetStateAction<OnboardingStage>>;
  answers: Record<string, string>;
  setAnswers: React.Dispatch<React.SetStateAction<Record<string, string>>>;
};

function OnboardingFlow({
  stage,
  setStage,
  answers,
  setAnswers,
}: OnboardingFlowProps) {
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
