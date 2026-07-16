import { questions } from "../data/questions";
import { useState } from "react";
import type { AnswersType, UserType } from "../types";

function getVisibleQuestions(answers: AnswersType) {
  const role = answers["topic"];
  if (!role) return questions.filter((q) => !q.track);
  return questions.filter((q) => !q.track || q.track === role);
}

export default function useFormState(topic: UserType) {
  const [currentIndex, setCurrentIndex] = useState<number>(0);
  const [answers, setAnswers] = useState<AnswersType>({ topic });

  const visibleQuestions = getVisibleQuestions(answers);
  const currentQuestion = visibleQuestions[currentIndex];
  const totalSteps = visibleQuestions.length;

  const storeAnswer = (value: string) => {
    const nextAnswers = { ...answers, [currentQuestion.id]: value };
    setAnswers(nextAnswers);

    const nextVisible = getVisibleQuestions(nextAnswers);
    const nextSteps = nextVisible.length;

    setCurrentIndex((i) => Math.min(i + 1, nextVisible.length - 1));

    return currentIndex >= nextSteps - 1;
  };

  const next = () =>
    setCurrentIndex((i) => Math.min(i + 1, visibleQuestions.length - 1));
  const back = () => setCurrentIndex((i) => Math.max(i - 1, 0));

  return {
    currentQuestion,
    currentIndex,
    totalSteps,
    answers,
    storeAnswer,
    next,
    back,
  };
}
