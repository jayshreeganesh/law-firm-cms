import { test, expect } from '@playwright/test';

test('has title and can navigate to admin login', async ({ page }) => {
  await page.goto('/');
  await expect(page).toHaveTitle(/Justice \& Partners/);
  // Capture frontend screenshot
  await page.screenshot({ path: 'screenshots/frontend-home.png', fullPage: true });
  
  await page.goto('/admin/login.php');
  await expect(page.locator('h2', { hasText: 'Admin Login' })).toBeVisible();
  // Capture admin screenshot
  await page.screenshot({ path: 'screenshots/admin-login.png', fullPage: true });
});
