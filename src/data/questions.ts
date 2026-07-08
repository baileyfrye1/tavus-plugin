export type UserType = 'parent-caregiver' | 'healthcare-provider';

export type Question = {
	id: string;
	text: string;
	options: { value: string, label: string; }[];
	track?: UserType;
}

export const questions: Question[] = [
	{
		id: "topic",
		text: "Which best describes you?",
		options: [
			{
				value: "parent-caregiver",
				label: "Parent or caregiver"
			},
			{
				value: "healthcare-provider",
				label: "Healthcare provider"
			}
		]
	},
	{
		id: "child-age",
		text: "What age group is your child in?",
		options: [
			{
				value: "12-14",
				label: "12-14",
			},
			{
				value: "15-17",
				label: "15-17",
			},
			{
				value: "18-25",
				label: "18-25",
			},
		],
		track: "parent-caregiver",
	},
	{
		id: "best-applies",
		text: "What brings you here today?",
		options: [
			{
				value: "first-conversation",
				label: "I want to learn how to talk with my child about cannabis",
			},
			{
				value: "finding-cannabis",
				label: "I want to learn how to respond after finding a vape pen or cannabis product when I do not know the full story",
			},
			{
				value: "tried-cannabis",
				label: "I want to learn how to respond when my child tells me, or I learn, that they tried cannabis or use it occasionally",
			},
			{
				value: "ongoing-use",
				label: "I want to learn how to talk with my child when cannabis use is ongoing and they do not see it as a concern or feel ready to change",
			},
			{
				value: "professional-support",
				label: "I want to learn how to recognize when conversations at home may not be enough and how to connect my child with professional support",
			},
		],
		track: "parent-caregiver",
	},
	{
		id: "patient-context",
		text: "Which stage of care best fits your patient?",
		options: [
			{
				value: "pregnant",
				label: "Pregnant",
			},
			{
				value: "postpartum-breastfeeding",
				label: "Postpartum or breastfeeding",
			},
		],
		track: "healthcare-provider",
	},
	{
		id: "conversation-help",
		text: "What would you like support with?",
		options: [
			{
				value: "hesitant-patient",
				label: "I want to learn how to ask about cannabis use when my patient appears hesitant or wants to know why I am asking",
			},
			{
				value: "cannabis-disclosure",
				label: "I want to learn how to respond when my patient discloses cannabis use",
			},
			{
				value: "cannabis-education",
				label: "I want to learn how to educate my patients about cannabis use, including how to correct misconceptions",
			},
			{
				value: "patient-support",
				label: "I want to learn how to support a patient who understands the risks of cannabis, but does not feel ready or able to reduce or stop use",
			},
			{
				value: "plan-development",
				label: "I want to learn how to help a patient who wants to reduce or stop cannabis use develop a realistic plan and access support",
			},
		],
		track: "healthcare-provider",
	},
]