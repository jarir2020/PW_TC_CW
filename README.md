# Pew Training Center WordPress Port

This repository contains the WordPress port of the public Dinajpur Institute of Science & Technology frontend:

<https://distdinajpur.edu.bd/>

The 'dist-faithful' theme uses the live site’s HTML class structure, CSS, JavaScript, fonts, logos, and gallery assets. WordPress provides the content management layer for pages, notices, routines, results, courses, and the private dashboard.

## Local setup

Requirements:

- PHP 8+
- MySQL/MariaDB
- PHP MySQL and cURL extensions

Create a local .env from .env.example, configure the local database, then run:

~~~bash
bash scripts/prepare-wordpress.sh
bash scripts/serve-wordpress.sh
~~~

Open <http://127.0.0.1:8090/>. If the requested port is occupied, the server script selects the next available local port.

Docker is optional. The supported fast path uses the PHP built-in server and the local MySQL database configured in .env.

## Content import

The one-time import scripts are:

~~~bash
php -r 'require getcwd()."/wp-load.php"; require getcwd()."/scripts/import-reference-content.php";'
php -r 'require getcwd()."/wp-load.php"; require getcwd()."/scripts/import-live-pages.php";'
~~~

The first imports the homepage reference records. The second copies public page-body HTML from the live site into WordPress pages. Do not run these against production without taking a database backup first.

## Admin panel

Open the standalone administrator panel at <http://127.0.0.1:8090/admin-panel/>; it contains its own login form and does not require opening `wp-login.php`.

Seed or refresh the demo administrator from the ignored `.env` values (`WP_USERNAME` / `WP_PASSWORD` or `PEW_ADMIN_USERNAME` / `PEW_ADMIN_PASSWORD`):

~~~bash
php scripts/seed-demo-admin.php
~~~

The panel includes Dashboard, Notice Board, Gallery, Certificates, Course Settings, and Profile.
- **Notice Board:** Create, edit, publish, attach PDFs/images, and trash notices shown on the homepage ticker and board.
- **Gallery:** Upload, edit titles/captions, and manage photos displayed on `/page/albums/` with interactive lightbox.
- **Certificates:** Issue, edit, and trash verified trainee certificates searchable publicly at `/page/certificate-verification/`.
- **Course Settings:** Toggle visibility and labels for Electrical and Welding programs.

Seed demo content:

~~~bash
php scripts/seed-demo-gallery.php
php scripts/seed-demo-certificates.php
~~~

- wp-content/themes/dist-faithful/ — faithful public theme and imported frontend assets.
- wp-content/plugins/pew-site-core/ — custom post types, roles, dashboard, and admin behavior.
- wp-content/mu-plugins/dist-faithful-routes.php — live-style /page/<slug>/ routing.
- scripts/ — local server, setup, import, and repair utilities.
- .env.example — configuration template; local secrets belong only in ignored .env.

## Verification

~~~bash
find wp-content/themes/dist-faithful scripts wp-content/mu-plugins -type f -name '*.php' -print0 | xargs -0 -n1 php -l
curl -I http://127.0.0.1:8090/
~~~

The project source is MIT licensed in LICENSE. WordPress core remains distributed under its upstream GPL license in license.txt.

# PW_TC_CW
