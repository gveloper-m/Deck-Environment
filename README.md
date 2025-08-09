Dockerized project scaffold
===========================

What you got
------------
- docker-compose.yml (builds/starts PostgreSQL, Laravel, WordPress, and a static Nginx web)
- laravel/docker/Dockerfile + docker-entrypoint.sh (will **create a Laravel app in ./laravel/app on first run**)
- laravel/app/ (this is where the Laravel project will live — the container will populate it if empty)
- wordpress/ (WordPress files will appear here; you can edit themes/plugins)
- web/ (static site files: index.html, style.css, script.js and a small Dockerfile)
- postgres/initdb/init.sql (creates DBs & users for Laravel and WordPress)
- postgres/data/ (Postgres data directory mapped to host)

Quick start (Linux / macOS / WSL)
-------------------------------
1. Unzip the archive and `cd` into the folder.
2. Run: `docker-compose up -d --build`
   - On first run the Laravel container will use `composer create-project` to create a fresh Laravel app inside `./laravel/app` if that folder is empty. This requires internet and may take a few minutes.
   - WordPress will initialize and copy files into `./wordpress` if that folder is empty.
   - Postgres initialization script will create databases `laravel_db` and `wp_db` and users `laravel_user` / `wp_user` with passwords defined in the docker-compose.yml (see `./postgres/initdb/init.sql`).
3. Visit the services:
   - Laravel app (served with `php artisan serve` inside the container): http://localhost:8000  (API route: http://localhost:8000/api/hello)
   - WordPress: http://localhost:8080
   - Static web (nginx): http://localhost:8081

Notes & troubleshooting
-----------------------
- If port 5432 (Postgres) is already in use on your machine, change the host mapping in docker-compose.yml (left side of "5432:5432").
- Composer will run inside the Laravel container. If you prefer to run Composer on the host, run composer inside `./laravel/app` after the project is created.
- The Laravel container's build context is `./laravel/docker` so the Dockerfile and entrypoint are separated from the editable app folder `./laravel/app`.
- You can edit all app files on your host machine; containers mount the host folders so your edits take effect immediately.
