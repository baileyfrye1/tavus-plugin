const BASE = '/wp-json/tavus/v1';

export async function fetchVideos() {
	const res = await fetch(`${BASE}/videos`, {
		method: 'GET'
	});

	if (!res.ok) {
		throw new Error('Failed to fetch videos');
	}

	return await res.json();
}

export async function fetchVideo(videoId: string) {
	const res = await fetch(`${BASE}/video/${videoId}`, {
		method: 'GET'
	});

	if (!res.ok) {
		throw new Error('Failed to fetch video');
	}

	return await res.json();
}