// Questions
export type UserType = 'parent-caregiver' | 'healthcare-provider';

export type Question = {
	id: string;
	text: string;
	options: { value: string, label: string; }[];
	track?: UserType;
}

// API Responses
export type Face = {
	face_id: string;
	face_name: string;
	thumbnail_video_url: string;
	status: string;
	created_at: string;
}