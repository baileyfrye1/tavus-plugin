import styles from "./OnboardingQuestions.module.css";
import useFormState from "../../hooks/useFormState";
import { useState } from "react";
import StepIndicator from "./StepIndicator";
import type { AnswersType, UserType } from "../../types";

type OnboardingQuestionsProps = {
  onComplete: (answers: AnswersType) => void;
  topic: UserType;
};

function OnboardingQuestions({ onComplete, topic }: OnboardingQuestionsProps) {
  const {
    currentQuestion,
    currentIndex,
    back,
    answers,
    storeAnswer,
    totalSteps,
  } = useFormState(topic);
  const [selected, setSelected] = useState("");

  const handleSubmit = (e: React.SubmitEvent) => {
    e.preventDefault();
    if (!selected) return;

    const nextAnswers: AnswersType = {
      ...answers,
      [currentQuestion.id]: selected,
    };
    const isCompleted = storeAnswer(selected);

    if (isCompleted) onComplete(nextAnswers);
    setSelected("");
  };

  return (
    <form className={styles.onboardingQuestions} onSubmit={handleSubmit}>
      <StepIndicator
        currentIndex={currentIndex + 1}
        totalSteps={totalSteps + 1}
      />
      <div className={styles.formContent}>
        <h3 className={styles.question}>{currentQuestion.text}</h3>
        <select
          name="questions"
          id="questions"
          onChange={(e) => setSelected(e.target.value)}
          className={styles.answerChoices}
        >
          <option value="">Select an option...</option>
          {currentQuestion.options.map((opt) => (
            <option key={opt.value} value={opt.value}>
              {opt.label}
            </option>
          ))}
        </select>
        <div className={styles.btnGroup}>
          <button
            type="submit"
            className={`${styles.nextBtn} ${styles.formBtn}`}
          >
            Next
          </button>
          {currentIndex > 0 && (
            <button
              type="button"
              onClick={back}
              className={`${styles.backBtn} ${styles.formBtn}`}
            >
              Back
            </button>
          )}
        </div>
      </div>
    </form>
  );
}

export default OnboardingQuestions;
