import { useQuery } from '@tanstack/react-query';
import styles from './AvatarPicker.module.css';
import { fetchFaces } from '../../api/faceEndpoints';
import { type Face } from '../../types';

type AvatarPickerProps = {
	onSelect: (avatarId: string) => void;
}

function AvatarPicker({  }: AvatarPickerProps) {
	const { data: faces, isLoading, isError } = useQuery<Face[]>({
		queryKey: ['faces'],
		queryFn: () => fetchFaces(),
	});

	if (isLoading) {
		return (
			<div className={styles.avatarGrid}>
				{Array.from({ length: 5 }).map(() => (
					<div className={styles.skeleton}></div>
				))}
			</div>
		)
	}

	if (isError || !faces) {
		return (
			<>
				<h3>Could not fetch faces</h3>
			</>
		)
	}

	return (
		<div className={styles.avatarGrid}>
			{faces.map((face) => (
				<div className={styles.avatarWrapper}>
					<video src={face.thumbnail_video_url} className={styles.avatar}></video>
				</div>
			))}
		</div>
	)
}
export default AvatarPicker;