# Law Firm CMS

A modern, responsive, and robust Content Management System (CMS) tailored specifically for Law Firms, Attorneys, and Legal Practices. Built entirely in PHP, this CMS natively supports multiple database engines including MySQL, MariaDB, SQLite, PostgreSQL, MS SQL Server, and Oracle.

## 🚀 Features

- **Multi-Database Support:** Choose your preferred database during installation.
- **Client Dashboard & Secure Login:** Clients can log in, view updates, and upload documents securely.
- **Dynamic Content Management:** Easily manage blog posts, attorneys, practice areas, and case results.
- **Modern UI/UX:** Responsive front-end built for trust and high conversions.
- **Automated Installation Wizard:** Effortless setup in under 60 seconds with an optional dummy data generator.

## 🛠 Installation

1. **Clone the repository:**
   ```bash
   git clone https://github.com/yourusername/law-firm-cms.git
   cd law-firm-cms
   ```

2. **Start your web server:**
   Ensure you have a local web server running (Apache, Nginx, or XAMPP) and PHP installed. Alternatively, you can use the built-in PHP server:
   ```bash
   php -S localhost:8000
   ```

3. **Run the Setup Wizard:**
   Open your browser and navigate to `http://localhost:8000`. You will be automatically redirected to the installation wizard.
   - Select your preferred database.
   - Enter your connection credentials (for MySQL/PostgreSQL/etc.).
   - (Optional) Check the "Generate Dummy Data" box to populate the site with sample content.
   - Click **Install Database**.

4. **Login to Admin Panel:**
   Once installed, navigate to `/admin`.
   - **Username:** `admin`
   - **Password:** `admin123`

## 🔒 Security Best Practices
- **Change Default Passwords:** Immediately log into the admin panel and change the default admin password.
- **Configuration Security:** The `includes/config.php` file is automatically added to `.gitignore` to prevent sensitive credentials from being exposed on GitHub.

## 📜 License
This project is open-sourced software licensed under the MIT license.
