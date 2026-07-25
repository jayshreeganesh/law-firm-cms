import { test, expect } from '@playwright/test';

test('has title and can navigate to admin login', async ({ page }) => {
  await page.goto('/');
  await expect(page).toHaveTitle(/Law Firm CMS/);
  
  await page.goto('/admin/login.php');
  await expect(page.locator('h2', { hasText: 'Admin Login' })).toBeVisible();
});
