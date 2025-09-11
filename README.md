# YouTube Playlist Viewer With Pagination

A paginated playlist viewer hosted on [ytplaylist.robert.wesner.io](https://ytplaylist.robert.wesner.io/).

## How do I run my own instance?

1. [Acquire a YouTube Data API Key.](https://developers.google.com/youtube/v3/getting-started)
2. Copy `.env.dist` to `.env` and modify `YOUTUBE_API_KEY`.
3. Copy `docker-compose.override.yml.example` to `docker-compose.override.yml`.
   - Optionally change the port. 
4. Run `docker compose up -d`.

Runs on https://localhost:8080 by default.
