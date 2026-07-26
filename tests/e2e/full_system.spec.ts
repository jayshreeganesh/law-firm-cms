import { test, expect } from '@playwright/test';

test.describe('Full System Walkthrough & Screenshot Generation', () => {
  
  test('Capture all frontend public pages', async ({ page }) => {
    // 1. Home Page
    await page.goto('/');
    await page.waitForLoadState('networkidle');
    await page.screenshot({ path: 'screenshots/01_frontend_home.png', fullPage: true });

    // 2. About Us
    await page.goto('/about.php');
    await page.waitForLoadState('networkidle');
    await page.screenshot({ path: 'screenshots/02_frontend_about.png', fullPage: true });

    // 3. Practice Areas
    await page.goto('/practice-areas.php');
    await page.waitForLoadState('networkidle');
    await page.screenshot({ path: 'screenshots/03_frontend_practice_areas.png', fullPage: true });

    // 4. Our Attorneys
    await page.goto('/attorneys.php');
    await page.waitForLoadState('networkidle');
    await page.screenshot({ path: 'screenshots/04_frontend_attorneys.png', fullPage: true });

    // 5. News & Legal Blog
    await page.goto('/blog.php');
    await page.waitForLoadState('networkidle');
    await page.screenshot({ path: 'screenshots/05_frontend_blog.png', fullPage: true });

    // 6. Contact Us
    await page.goto('/contact.php');
    await page.waitForLoadState('networkidle');
    await page.screenshot({ path: 'screenshots/06_frontend_contact.png', fullPage: true });

    // 7. Client Portal Login
    await page.goto('/client_login.php');
    await page.waitForLoadState('networkidle');
    await page.screenshot({ path: 'screenshots/07_client_login.png', fullPage: true });
  });

  test('Capture secure admin dashboard functionality', async ({ page }) => {
    // 8. Admin Login
    await page.goto('/admin/login.php');
    await page.waitForLoadState('networkidle');
    await page.screenshot({ path: 'screenshots/08_admin_login.png', fullPage: true });

    // Perform Login
    await page.fill('input[name="username"]', 'admin');
    await page.fill('input[name="password"]', 'admin123');
    await page.click('button[type="submit"]');

    // 9. 2FA Screen
    await page.waitForURL('**/admin/verify_2fa.php');
    await page.waitForLoadState('networkidle');
    await page.screenshot({ path: 'screenshots/09_admin_2fa.png', fullPage: true });
    
    const demoText = await page.locator('div', { hasText: 'Demo Mode:' }).last().textContent();
    const codeMatch = demoText?.match(/\d{6}/);
    if (codeMatch) {
      await page.fill('input[name="code"]', codeMatch[0]);
      await page.click('button[type="submit"]');
    }

    // 10. Admin Dashboard
    await page.waitForURL('**/admin/index.php');
    await page.waitForLoadState('networkidle');
    await page.screenshot({ path: 'screenshots/10_admin_dashboard.png', fullPage: true });

    // 11. Admin Content Management (Practice Areas)
    await page.goto('/admin/practice_areas.php');
    await page.waitForLoadState('networkidle');
    await page.screenshot({ path: 'screenshots/11_admin_practice_areas.png', fullPage: true });

    // 12. Admin Settings
    await page.goto('/admin/settings.php');
    await page.waitForLoadState('networkidle');
    await page.screenshot({ path: 'screenshots/12_admin_settings.png', fullPage: true });
  });
});
