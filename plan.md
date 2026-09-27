## WordPress Rebuild Plan

No code, database, or deployment changes will be made until this plan is approved.

### 1. Demo Website Audit

Inspect the public CakePHP website and document:

- Header, logo, navigation, dropdown menus, and mobile menu
- Homepage slider/banner and image gallery
- Bengali typography, colors, spacing, borders, and layout widths
- Notice board, class routine, syllabus, exam routine, and results sections
- Institute history and principal’s message
- Technology/course cards
- Calendar, attendance, important links, map, and footer
- All public page URLs and linked documents
- Existing CSS, JavaScript behavior, image assets, and responsive breakpoints

The current homepage includes these major sections and navigation groups: institutional information, gallery, admission, technology departments, administration, results, student ID search, job placement, blog, notices, attendance, and important links. ([demo website](https://distdinajpur.edu.bd/))

### 2. WordPress and Local Database Setup

Create a local WordPress installation using:

- Database name: `pew_training_center`
- Database host: `localhost`
- Port: `3306`
- Username: `root`
- Password: supplied local password
- Character set: `utf8mb4`
- WordPress table prefix: a project-specific prefix such as `pew_`

The database credentials will remain local and will not be committed to Git or placed in public configuration.

### 3. Theme Architecture

Build a custom WordPress theme rather than relying on a generic theme.

Planned structure:

- Custom header and navigation
- Responsive mega/dropdown menus
- Homepage template
- Inner-page template
- Department/course template
- Notice and document templates
- Search/result pages
- Gallery template
- Contact and location template
- Reusable widgets for notices, routines, results, calendar, attendance, and important links

The original visual language will be reproduced as closely as possible using WordPress-compatible PHP templates, CSS, JavaScript, and optimized local assets.

### 4. Content Management Model

Create WordPress content types for:

- Notices
- Class routines
- Exam routines
- Syllabus documents
- Results
- Departments and technologies
- Teachers and staff
- Gallery items
- Blog/news
- Important external links
- Attendance information
- Admission information
- Institutional pages

Each content type will have suitable admin fields, categories, publication dates, featured images, and downloadable attachments.

### 5. User and Admin Panel

Since the existing user/admin interface is not publicly visible, create a private WordPress portal that follows the public site’s visual style.

Planned roles:

- Administrator
- Teacher/staff
- Student/user

Planned behavior:

- Custom branded login screen
- Role-based permissions
- Frontend user dashboard where appropriate
- Protected student, teacher, and administration pages
- Non-administrator users restricted from unnecessary WordPress backend areas
- Admin screens organized around notices, results, attendance, courses, users, and documents
- Secure password reset and session handling

The exact private-panel features will be finalized during the implementation audit.

### 6. Asset and Frontend Migration

- Download or recreate publicly available visual assets where appropriate
- Rebuild CakePHP templates as WordPress templates
- Port equivalent CSS styling into the custom theme
- Reimplement sliders, dropdowns, tabs, galleries, calendars, and other JavaScript interactions
- Replace CakePHP-specific routes and variables with WordPress URLs and template functions
- Optimize images and avoid unnecessary third-party dependencies
- Preserve Bengali text rendering and RTL-compatible behavior if required later

### 7. Data and Content Migration

Migrate or recreate the visible demo content into WordPress:

- Main institutional text
- Course and department information
- Notices and downloadable files
- Principal and staff information
- Gallery images
- Contact information
- Important links
- Homepage sections

Content will be separated from design so administrators can update it without editing code.

### 8. Responsive and Accessibility Work

Test the theme at:

- Desktop
- Tablet
- Mobile portrait
- Mobile landscape

Validation will include:

- Mobile navigation
- Dropdown usability
- Readable Bengali fonts
- Keyboard navigation
- Image alt text
- Form labels
- Color contrast
- Responsive tables and documents
- Basic performance optimization

### 9. Testing

Before delivery, verify:

- WordPress installation and database connectivity
- Public homepage and inner pages
- Navigation and dropdowns
- Search functionality
- Notices, documents, results, and gallery
- Login and role permissions
- Admin CRUD operations
- Mobile layout
- Broken links and missing assets
- PHP and JavaScript errors
- WordPress security basics
- Backup and restore procedure

### 10. Final Deliverables

- Custom WordPress theme
- WordPress database and setup instructions
- Admin/user portal
- Migrated demo content
- Local development configuration
- Installation guide
- Admin usage guide
- Backup instructions
- Testing report
- Final visual comparison against the demo website

The implementation phase should begin only after approval of this plan. The browser-verification skill could not be loaded because of a local sandbox mount error, so the initial assessment used read-only inspection of the live homepage instead.