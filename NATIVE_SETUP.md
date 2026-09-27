# Native PHP + MySQL setup

Docker is optional for this project. The fast local path uses the PHP built-in server and the MySQL service already running on `localhost:3306`.

## First run

```bash
cp .env.example .env
# Set the local MySQL password in .env
bash scripts/prepare-wordpress.sh
bash scripts/serve-wordpress.sh
```

Then open:

```text
http://127.0.0.1:8080/wp-admin/install.php
```

The project already creates the `pew_training_center` database and uses the `pew_` table prefix. Complete the normal WordPress installer once, activate **Pew Training Center** and **Pew Site Core**, and set the homepage/navigation under Settings.

## Fast command

```bash
bash scripts/serve-wordpress.sh
```

You can override the address without editing files:

```bash
HOST=127.0.0.1 PORT=8090 bash scripts/serve-wordpress.sh
```

## When Docker is useful

Docker is useful for a clean, reproducible environment or for another developer who does not have PHP/MySQL installed. It is not needed for your current machine, and the existing `docker-compose.yml` is retained as an optional fallback.

