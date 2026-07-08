import styles from './StepIndicator.module.css';

type StepIndicatorProps = {
	currentIndex: number;
	totalSteps: number;
}

function StepIndicator({ currentIndex, totalSteps }: StepIndicatorProps) {
	return (
		<div className={styles.stepper} role='list' aria-label='Question Progress'>
			{Array.from({ length: totalSteps }).map((_, index) => {
				const isFilled = index <= currentIndex;
				return (
					<div key={index} className={styles.step} role='listitem'>
						<div className={`${styles.circle} ${isFilled ? styles.filled : ''}`}>
							{index + 1}
						</div>
					</div>
				)
			})}
		</div>
	)
}

export default StepIndicator;