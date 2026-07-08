import styles from './OnboardingQuestions.module.css';
import useFormState from '../../hooks/useFormState';
import { useState } from 'react';
import StepIndicator from './StepIndicator';

type OnboardingQuestionsProps = {
	onComplete: (answers: Record<string, string>) => void;
}

function OnboardingQuestions({ onComplete }: OnboardingQuestionsProps) {
	const { currentQuestion, currentIndex, totalSteps, back, answers, storeAnswer } = useFormState();
	const [selected, setSelected] = useState('');

	const handleSubmit = (e: React.SubmitEvent) => {
		e.preventDefault();
		if (!selected) return;

		const nextAnswers = { ...answers, [currentQuestion.id]: selected };
		const isCompleted = storeAnswer(selected);

		if (isCompleted) onComplete(nextAnswers);
		setSelected('');
	};

	return (
		<form className={styles.onboardingQuestions} onSubmit={handleSubmit}>
			{currentIndex > 0 && <StepIndicator currentIndex={currentIndex} totalSteps={totalSteps} />}
			<div className={styles.formContent}>
				<h3 className={styles.question}>{currentQuestion.text}</h3>
				<select name="questions" id="questions" onChange={(e) => setSelected(e.target.value)}>
					<option value="">Select an option...</option>
					{currentQuestion.options.map((opt) => (
						<option key={opt.value} value={opt.value}>{opt.label}</option>
					))}
				</select>
				<div className={styles.btnGroup}>
					<button type='submit' className={`${styles.nextBtn} ${styles.formBtn}`}>Next</button>
					{currentIndex > 0 && <button type='button' onClick={back} className={`${styles.backBtn} ${styles.formBtn}`}>Back</button>}
				</div>
			</div>
		</form>
	)
}

export default OnboardingQuestions