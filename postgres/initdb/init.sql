-- init.sql: runs during first-time Postgres initialization
CREATE USER laravel_user WITH PASSWORD 'laravel_pass';
CREATE DATABASE laravel_db OWNER laravel_user;

CREATE USER wp_user WITH PASSWORD 'wp_pass';
CREATE DATABASE wp_db OWNER wp_user;
