import OnboardingQuestions from "./questions/OnboardingQuestions";
import AvatarPicker from "./avatar/AvatarPicker";
import ModuleGrid from "./modules/ModuleGrid";
import type { OnboardingStage } from "../App";
import TopicSelection from "./topic/TopicSelection";
import type { AnswersType, UserType } from "../types";

type OnboardingFlowProps = {
  stage: OnboardingStage;
  setStage: React.Dispatch<React.SetStateAction<OnboardingStage>>;
  answers: AnswersType;
  setAnswers: React.Dispatch<React.SetStateAction<AnswersType>>;
};

function OnboardingFlow({
  stage,
  setStage,
  answers,
  setAnswers,
}: OnboardingFlowProps) {
  switch (stage) {
    case "topic":
      return (
        <TopicSelection
          onSelect={(topic: UserType) => {
            setAnswers((prev) => ({ ...prev, topic }));
            setStage("questions");
          }}
        />
      );
    case "questions":
      return (
        <OnboardingQuestions
          onComplete={(userAnswers: AnswersType) => {
            setAnswers(userAnswers);
            setStage("avatar");
          }}
          topic={answers.topic!}
        />
      );
    case "avatar":
      return (
        <AvatarPicker
          answers={answers}
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
