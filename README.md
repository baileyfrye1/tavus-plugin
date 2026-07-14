# Tavus Integration

WordPress plugin integrating Tavus AI avatar videos into a guided learning experience.

## Setup

1. Activate plugin
2. Add `[tavus_integration]` shortcode to a page
3. Define API key in `wp-config.php`:
   ```php
   define('TAVUS_API_KEY', 'your-key');
   ```
4. Update face IDs in `includes/tavus-api.php::getFaceIds()`

## Commands

| Command | Description |
|---|---|
| `pnpm dev` | Dev server with HMR |
| `pnpm build` | Type-check + build to `dist/` |
| `pnpm lint` | oxlint |

## Video Naming Convention

Must start with a track prefix in brackets:

- `[parent-caregiver] Title`
- `[healthcare-provider] Title`

The prefix is stripped from display titles.

## Caching

| Layer | TTL |
|---|---|
| WordPress transient (PHP -> Tavus) | 1 hour |
| React Query (browser -> WordPress) | 15 min |

## REST Endpoints

All under `tavus/v1`, publicly accessible.

| Route | Params | Returns |
|---|---|---|
| `GET /faces` | `?track=` (optional) | Avatar faces filtered by track, or all |
| `GET /videos` | `?face_id=` (required), `?track=` (optional) | Videos for face, optionally by track |
| `GET /video/{id}` | `videoId` (path), `?face_id=` | Single video |

Generating videos are filtered out.

## TODO

- Replace hardcoded face IDs with admin settings page
- Add cache invalidation endpoint
- HLS.js support via `stream_url`
