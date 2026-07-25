# Justice & Partners - Enterprise Law Firm CMS

An ultra-modern, fully responsive, and highly advanced Content Management System built specifically for Law Firms. This project was engineered from the ground up to feature enterprise-level architecture, encompassing agnostic database connectivity, hyper-local translations, strict web accessibility (WCAG) standards, and automated end-to-end testing frameworks.

## 🌟 Core Enterprise Features

### 1. Database Agnostic Architecture
Unlike standard PHP applications locked to MySQL, this CMS uses a completely abstracted PDO layer allowing it to seamlessly run on **6 different database engines** without modifying a single line of application code:
- MySQL
- MariaDB
- PostgreSQL
- SQLite
- Microsoft SQL Server (SQLSRV)
- Oracle (OCI)

**How it works:**
- **DDL (Schema Creation):** The `setup.php` script dynamically selects the correct syntax file from the `database/schema/` directory (e.g., `mysql.sql`, `pgsql.sql`, `sqlsrv.sql`) based on your chosen driver.
- **DML (Query Abstraction):** Syntax differences between drivers (like pagination offsets or `UPSERT` statements) are routed through helper functions in `includes/db.php`.
*Example:* `db_limit_offset_sql('oracle', $sql, 10, 5)` automatically converts standard `LIMIT` queries into Oracle's `OFFSET 5 ROWS FETCH NEXT 10 ROWS ONLY`.

### 2. Hyper-Localized Translation Engine (28 Languages)
The CMS features a lightning-fast, offline native translation engine specifically curated for the Indian subcontinent, ensuring zero reliance on third-party APIs for primary regional traffic.
- **22 Official Scheduled Languages:** Hindi, Bengali, Telugu, Marathi, Tamil, Urdu, Gujarati, Kannada, Odia, Malayalam, Punjabi, Assamese, etc.
- **6 Massive Regional Dialects:** Bhojpuri, Haryanvi, Magahi, Marwari, Chhattisgarhi, and Awadhi.
- **Global Fallback:** A fully integrated Google Translate widget remains available to instantly translate the site into any non-Indian global language (French, German, Spanish, etc.).

**How to use:**
Simply select a language from the "Native Offline Languages" dropdown in the header. The application will store your preference in a session and instantaneously load the corresponding array from the `lang/` directory (e.g., `lang/bho.php` for Bhojpuri).

### 3. Universal Accessibility (WCAG A11y Toolbar)
Designed to be inclusive for specially-abled users, the CMS includes a floating Accessibility Widget (bottom left icon) offering enterprise-grade tools:
- **Read Page Aloud (TTS):** Uses the browser's native Speech Synthesis API to read page content out loud for the visually impaired.
- **High Contrast Mode:** Inverts the UI for users with color blindness, cataracts, or low vision.
- **Dyslexia Friendly Font:** Overrides global fonts with heavier, asymmetric typography to prevent "jumping letters".
- **Text Resizer:** Instantly scales the global UI font size up or down.

### 4. Automated Testing Suite (PHPUnit & Playwright)
To ensure absolute reliability during deployment, the project ships with both backend unit tests and frontend E2E tests:
- **PHPUnit:** Validates that the abstract database query builders (like `db_limit_offset_sql`) generate the perfect syntax for all 6 database drivers.
  *Run it:* `.\vendor\bin\phpunit`
- **Playwright:** Launches a headless Chromium browser, navigates the live frontend, and verifies that UI elements (like localized titles and admin login portals) render correctly.
  *Run it:* `npm run test`

### 5. Secure Admin Dashboard & Analytics
The backend is a fully-featured, secure admin portal (`/admin/`) featuring:
- **Interactive Analytics:** Visual Chart.js representations of page views, form submissions, and user engagement over time.
- **Audit Logging:** Every action (login, content creation, setting changes) is cryptographically logged to ensure total accountability and security compliance.
- **Global Settings Management:** Admins can dynamically update Site Name, Contact Info, SEO Meta Tags, and switch between Live AI Chat or Human-Agent Chat widgets on the fly.

### 6. Full Content Management System (CRUD)
The CMS allows non-technical staff to seamlessly manage all public-facing data:
- **Practice Areas Management:** Add, edit, and categorize legal services (e.g., Corporate Law, Criminal Defense).
- **Attorneys Directory:** Manage lawyer profiles, headshots, specializations, and contact details.
- **News & Legal Blog:** Publish SEO-optimized articles and legal insights directly to the public blog.
- **Form Submissions:** Review, manage, and respond to incoming Contact Queries and Free Consultation bookings directly from the dashboard.

### 7. Client Portal & Secure Billing
A dedicated, secure `/client_login.php` portal allowing clients to securely log in, view their active case status, track billable hours, and interact with their assigned attorneys.

### 8. UI/UX & SEO Engine
- **Dark Mode / Light Mode:** A dynamic frontend theme toggler that remembers user preferences via `localStorage`.
- **Dynamic SEO:** The CMS automatically injects relevant meta tags, canonical links, and Open Graph data based on the specific page and language being viewed.
- **AI Chat Assistant:** A floating chat widget in the footer provides immediate, automated assistance to potential clients.

---

## 🚀 Installation & Setup

### Prerequisites
- PHP 8.0+
- Composer (for PHPUnit)
- Node.js & NPM (for Playwright)
- A supported Database Server (MySQL, Postgres, SQL Server, etc.)

### Step 1: Install Dependencies
```bash
# Install PHP testing dependencies
composer install

# Install Playwright testing dependencies
npm install
npx playwright install chromium
```

### Step 2: Database Setup
1. Open `includes/db.php` and configure your database credentials.
2. Change the `$db_driver` variable to match your database (e.g., `'mysql'`, `'pgsql'`, `'sqlsrv'`).
3. Run the built-in setup script by navigating to: `http://localhost/setup.php`
4. The setup script will automatically detect your driver, run the exact DDL schema required, and instantly seed the database with dummy data!

### Step 3: Run the Application
You can serve the application locally using PHP's built-in server:
```bash
php -S localhost:8000 -t .
```
Navigate to `http://localhost:8000` to view the frontend, and `http://localhost:8000/admin/login.php` to access the CMS portal.

---

## 📁 Directory Structure
- `/admin` - Secure backend portal for managing blogs, settings, and forms.
- `/assets` - CSS, JS, and Images (Features built-in Dark Mode).
- `/database/schema` - Distinct SQL files tailored to 6 different database engines.
- `/includes` - Core application logic, database abstraction (`db.php`), and the WCAG Accessibility widget (`accessibility.php`).
- `/lang` - 28 native translation arrays.
- `/tests` - Playwright E2E specs (`/tests/e2e`) and PHPUnit backend tests.
