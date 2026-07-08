import styles from './AvatarPicker.module.css';

type AvatarPickerProps = {
	onSelect: (avatarId: string) => void;
}

function AvatarPicker({  }: AvatarPickerProps) {
	return (
		<div className={styles.moduleGrid}>
			{Array.from({ length: 5 }).map(() => (
				<div className={styles.skeleton}></div>
			))}
		</div>
	)
}
export default AvatarPicker;