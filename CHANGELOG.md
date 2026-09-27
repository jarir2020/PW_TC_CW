# Changelog

All notable changes to the PEW Training Center WordPress project are recorded here. New entries should be added at the top of the dated history.

## 2026-09-28 — Feedback 04: Navigation streamlining, admin gallery control, certificate verification, and content enrichment

- Streamlined the public navigation menu by removing `কলেজ প্রশাসন` (College Administration), `রেজাল্ট অনুসন্ধান` (Result Search), `স্টুডেন্ট আইডি অনুসন্ধান` (Student ID Search), `জব প্লেসমেন্ট` (Job Placement), and `ব্লগ` (Blog).
- Added 301 redirects for legacy removed pages (`student-results` and `student-id` redirect to `certificate-verification`; `job-placement` redirects to homepage).
- Completed and fixed the Admin Panel Gallery management (`/admin-panel/?section=gallery`) with full image upload, title/caption editing, and trashing; enabled Fancybox modal lightbox for the public `/page/albums/` gallery.
- Built interactive Certificate Verification Portal (`/page/certificate-verification/`) featuring instant search by certificate ID or roll number, official verification badge, trainee details card, print button, and helpline support.
- Added Certificate Management in the Admin Panel (`/admin-panel/?section=certificates`) allowing administrators to view, add, edit, and delete verified trainee certificates directly from the dashboard.
- Created a dedicated Admission page (`/page/admission/` — `ভর্তি তথ্য`) with comprehensive details for 4-month trade courses, daily travel/refreshment allowances, eligibility, and required application documents.
- Enriched remaining public pages (`history`, `principal`, `anual_activities`, `courses`, `discipline`, `documentaries`, `library`, `dormitory`, `contact`, `ডাউনলোড`) using authentic PEW Training Center information gathered from Facebook, Google, and SICIP/BEIOA sources.
- Updated homepage sidebar important links and header social icons to link to the official Facebook page (`facebook.com/pewtc`) and the certificate verification portal.

## 2026-09-27 — Admin panel legacy-script isolation

- Disabled the copied public theme CSS/JavaScript bundle on `/admin-panel/`.
- Prevented the legacy Apycom menu script from injecting the “No back link” element into the standalone dashboard.
- Kept the public theme assets active on normal public pages.
## 2026-09-27 — Logout and legacy markup cleanup

- Routed the WordPress admin-bar logout action through the standalone PEW panel logout endpoint.
- Routed the existing dashboard logout link through the same panel login destination.
- Removed the hidden Apycom “No back link” legacy markup from the public footer.
## 2026-09-27 — Isolated admin dashboard shell

- Removed the public header, navigation menu, news ticker, footer, and public page wrapper from `/admin-panel/`.
- Kept the public reference-theme shell unchanged on normal public pages.
- Added a clean full-width admin page shell for the standalone dashboard.
## 2026-09-27 — Admin panel UX fixes

- Fixed custom logout so it returns directly to `/admin-panel/` instead of `wp-login.php?loggedout=true`.
- Removed the reference-theme top-right “মন্তব্য / লগ ইন” controls from the admin-panel page.
- Kept the public reference header controls unchanged on normal public pages.
## 2026-09-27 — Standalone admin-panel login

- Changed `/admin-panel/` into the complete admin entry point with its own username/password form.
- Added nonce-protected administrator authentication through the panel’s own form instead of linking unauthenticated users to `wp-login.php`.
- Added panel-native logout handling.
- Removed the inherited reference-theme mini login form from the admin-panel page only.
- Normalized panel redirects to the requested `/admin-panel/` URL.

## 2026-09-27 — Feedback 03: admin panel and notice board

- Added the PEW administrator panel with Dashboard, Notice Board, Profile, and Course Settings sections.
- Added notice creation, editing, publishing, date fields, optional PDF/image attachments, and trash actions.
- Connected published notices to the existing homepage notice board and notice detail pages.
- Kept Class Routine, Syllabus, Exam Routine, and Result visible as display-only reference buttons.
- Added `scripts/seed-demo-admin.php` to create or refresh the local administrator from ignored `.env` credentials without storing a password in source code.
- Added responsive admin-panel styling and documented local setup in `README.md` and `.env.example`.
- Verified authenticated panel access, notice submission, attachment upload, public homepage display, PHP lint, and Git whitespace checks.

## 2026-09-27 — Course content and course visibility

- Restricted the public course selector to Electrical and Welding while retaining legacy course code/routes hidden.
- Added the supplied course poster to both course detail pages.
- Added OCR-derived English course details: duration, eligibility, age, SICIP context, priorities, allowances, certificates, application documents, job sectors, and higher-study/overseas pathways.
- Updated the retained `mechanical-1` route title to Welding.
- Added Course Settings support for the two visible course labels and visibility flags.
- Resized the supplied banner photos inside the reference slider frame.

## 2026-09-27 — PEW Training Center branding and assets

- Rebranded the site from the reference institute to PEW Training Center.
- Updated site title, description, homepage copy, footer contact details, header/footer branding, and footer logo.
- Added the supplied company photos to the homepage slider.
- Added the supplied course image and OCR source files.
- Restored missing reference CSS, JavaScript, and image assets needed by the faithful frontend.
- Added Cloudflare Quick Tunnel sharing scripts and forwarded-host/HTTPS asset handling.

## 2026-09-27 — Initial WordPress port

- Added the WordPress project structure, active `dist-faithful` theme, PEW site-core plugin, content importers, local PHP server workflow, and database configuration.
- Preserved the reference site’s frontend HTML structure, CSS classes, JavaScript behavior, and public routes as the implementation baseline.
- Added MIT licensing, project documentation, agent instructions, LLM project notes, and local ignore rules.