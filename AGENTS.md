# Agent Instructions

## Project scope

This is a WordPress port of the public Dinajpur Institute of Science & Technology site. Preserve the live frontend contract when changing the public theme: legacy wrappers, class names, asset paths, Bengali labels, and imported CSS/JavaScript behavior are intentional.

## Development rules

- Use the active project .env for local database/server configuration. Never print, commit, or place credentials in documentation.
- Do not commit .env, wp-config.php, runtime logs, uploads, caches, or generated screenshots.
- Keep WordPress core changes out of scope unless explicitly requested.
- Put public-site changes in wp-content/themes/dist-faithful/; put content types and backend behavior in wp-content/plugins/pew-site-core/.
- Use the import scripts for reference data instead of hard-coding notices, routines, and results into templates.
- Do not replace the copied live CSS/JavaScript with approximated redesigns without explicit approval.

## Local workflow

~~~bash
bash scripts/serve-wordpress.sh
~~~

The default local port is WORDPRESS_PORT from .env or 8080. The script automatically chooses a free fallback port when necessary.

Before handoff, lint changed PHP files and verify the homepage plus any changed route over HTTP. Docker is optional; the native PHP server and local MySQL workflow are supported.

## Git workflow

Review staged paths before committing. Preserve unrelated work and keep secrets out of the index. Use focused commit messages and verify the pushed branch and remote when a push is requested.
