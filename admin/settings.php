<?php
require_once 'includes/admin_header.php';

$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $settings = [
        'site_name' => $_POST['site_name'] ?? '',
        'site_email' => $_POST['site_email'] ?? '',
        'site_phone' => $_POST['site_phone'] ?? '',
        'site_address' => $_POST['site_address'] ?? '',
        'seo_description' => $_POST['seo_description'] ?? '',
        'seo_keywords' => $_POST['seo_keywords'] ?? '',
        'smtp_from_email' => $_POST['smtp_from_email'] ?? 'no-reply@lawfirm.local',
        'smtp_from_email' => $_POST['smtp_from_email'] ?? 'no-reply@lawfirm.local',
        'chat_widget_type' => $_POST['chat_widget_type'] ?? 'tawk',
        'gemini_api_key' => $_POST['gemini_api_key'] ?? '',
        'google_calendar_webhook' => $_POST['google_calendar_webhook'] ?? '',
        'twilio_sid' => $_POST['twilio_sid'] ?? '',
        'twilio_token' => $_POST['twilio_token'] ?? '',
        'twilio_phone' => $_POST['twilio_phone'] ?? '',
        'payment_mode' => $_POST['payment_mode'] ?? 'sandbox',
        'active_payment_gateway' => $_POST['active_payment_gateway'] ?? 'stripe',
        'stripe_public_key' => $_POST['stripe_public_key'] ?? '',
        'stripe_secret_key' => $_POST['stripe_secret_key'] ?? '',
        'paypal_client_id' => $_POST['paypal_client_id'] ?? '',
        'paypal_secret' => $_POST['paypal_secret'] ?? '',
        'payu_merchant_key' => $_POST['payu_merchant_key'] ?? '',
        'payu_salt' => $_POST['payu_salt'] ?? '',
        'affirm_public_key' => $_POST['affirm_public_key'] ?? '',
        'affirm_private_key' => $_POST['affirm_private_key'] ?? '',
        'razorpay_key_id' => $_POST['razorpay_key_id'] ?? '',
        'razorpay_key_secret' => $_POST['razorpay_key_secret'] ?? '',
        'paytm_merchant_id' => $_POST['paytm_merchant_id'] ?? '',
        'paytm_merchant_key' => $_POST['paytm_merchant_key'] ?? '',
        'mobikwik_merchant_id' => $_POST['mobikwik_merchant_id'] ?? '',
        'mobikwik_secret_key' => $_POST['mobikwik_secret_key'] ?? '',
        'phonepe_merchant_id' => $_POST['phonepe_merchant_id'] ?? '',
        'phonepe_salt_key' => $_POST['phonepe_salt_key'] ?? '',
        'upi_vpa_address' => $_POST['upi_vpa_address'] ?? '',
        'bharatpe_merchant_id' => $_POST['bharatpe_merchant_id'] ?? '',
        'bharatpe_token' => $_POST['bharatpe_token'] ?? '',
        'amazonpay_merchant_id' => $_POST['amazonpay_merchant_id'] ?? '',
        'amazonpay_access_key' => $_POST['amazonpay_access_key'] ?? '',
        'zoho_billing_client_id' => $_POST['zoho_billing_client_id'] ?? '',
        'zoho_billing_client_secret' => $_POST['zoho_billing_client_secret'] ?? '',
        'ccavenue_merchant_id' => $_POST['ccavenue_merchant_id'] ?? '',
        'ccavenue_working_key' => $_POST['ccavenue_working_key'] ?? '',
        'ccavenue_access_code' => $_POST['ccavenue_access_code'] ?? '',
        'jiopay_merchant_id' => $_POST['jiopay_merchant_id'] ?? '',
        'jiopay_client_id' => $_POST['jiopay_client_id'] ?? '',
        'airtel_money_merchant_id' => $_POST['airtel_money_merchant_id'] ?? '',
        'airtel_money_hash_key' => $_POST['airtel_money_hash_key'] ?? '',
        'vi_money_merchant_id' => $_POST['vi_money_merchant_id'] ?? ''
    ];
    
    foreach ($settings as $key => $value) {
        $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?");
        $stmt->execute([$key, $value, $value]);
    }
    $success = "Settings updated successfully.";
}
?>

<div class="card" style="max-width: 600px;">
    <h3 style="margin-top: 0; color: var(--admin-primary);">Site Settings</h3>
    
    <?php if ($success): ?>
        <div style="background-color: #d1fae5; color: #065f46; padding: 1rem; border-radius: 4px; margin-bottom: 1.5rem;">
            <?= htmlspecialchars($success) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <div style="margin-bottom: 1rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Site Name</label>
            <input type="text" name="site_name" value="<?= htmlspecialchars(get_setting($pdo, 'site_name')) ?>" required style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 4px; font-family: inherit;">
        </div>
        
        <div style="margin-bottom: 1rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Contact Email</label>
            <input type="email" name="site_email" value="<?= htmlspecialchars(get_setting($pdo, 'site_email')) ?>" required style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 4px; font-family: inherit;">
        </div>
        
        <div style="margin-bottom: 1rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Contact Phone</label>
            <input type="text" name="site_phone" value="<?= htmlspecialchars(get_setting($pdo, 'site_phone')) ?>" style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 4px; font-family: inherit;">
        </div>
        
        <div style="margin-bottom: 1.5rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Office Address</label>
            <textarea name="site_address" rows="3" style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 4px; font-family: inherit; resize: vertical;"><?= htmlspecialchars(get_setting($pdo, 'site_address')) ?></textarea>
        </div>
        
        <h4 style="margin-top: 2rem; margin-bottom: 1rem; color: var(--admin-primary); border-bottom: 1px solid #e2e8f0; padding-bottom: 0.5rem;">SEO Settings</h4>
        
        <div style="margin-bottom: 1rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Global Meta Description</label>
            <textarea name="seo_description" rows="2" style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 4px; font-family: inherit; resize: vertical;"><?= htmlspecialchars(get_setting($pdo, 'seo_description')) ?></textarea>
        </div>
        
        <div style="margin-bottom: 1.5rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Global Meta Keywords</label>
            <input type="text" name="seo_keywords" value="<?= htmlspecialchars(get_setting($pdo, 'seo_keywords')) ?>" style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 4px; font-family: inherit;">
        </div>
        
        <h4 style="margin-top: 2rem; margin-bottom: 1rem; color: var(--admin-primary); border-bottom: 1px solid #e2e8f0; padding-bottom: 0.5rem;">Advanced Settings</h4>

        <div style="margin-bottom: 1rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">System 'From' Email Address (for Notifications)</label>
            <input type="email" name="smtp_from_email" value="<?= htmlspecialchars(get_setting($pdo, 'smtp_from_email') ?: 'no-reply@lawfirm.local') ?>" required style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 4px; font-family: inherit;">
        </div>

        <div style="margin-bottom: 1rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Live Chat Widget Frontend</label>
            <select name="chat_widget_type" style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 4px; font-family: inherit;">
                <option value="tawk" <?= get_setting($pdo, 'chat_widget_type') === 'tawk' ? 'selected' : '' ?>>Tawk.to (Human Live Chat)</option>
                <option value="ai" <?= get_setting($pdo, 'chat_widget_type') === 'ai' ? 'selected' : '' ?>>Custom AI Chatbot (Gemini)</option>
                <option value="disabled" <?= get_setting($pdo, 'chat_widget_type') === 'disabled' ? 'selected' : '' ?>>Disabled</option>
            </select>
        </div>

        <div style="margin-bottom: 1.5rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Google Gemini API Key (If using AI Chatbot)</label>
            <input type="password" name="gemini_api_key" value="<?= htmlspecialchars(get_setting($pdo, 'gemini_api_key')) ?>" style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 4px; font-family: inherit;">
            <small style="color: #64748b; display: block; margin-top: 0.25rem;">Leave blank to use the deterministic offline fallback bot.</small>
        </div>

        <h4 style="margin-top: 2rem; margin-bottom: 1rem; color: var(--admin-primary); border-bottom: 1px solid #e2e8f0; padding-bottom: 0.5rem;">API Integrations</h4>
        
        <div style="margin-bottom: 1rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Twilio Account SID (For SMS Alerts)</label>
            <input type="text" name="twilio_sid" value="<?= htmlspecialchars(get_setting($pdo, 'twilio_sid')) ?>" style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 4px; font-family: inherit;">
        </div>
        <div style="margin-bottom: 1rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Twilio Auth Token</label>
            <input type="password" name="twilio_token" value="<?= htmlspecialchars(get_setting($pdo, 'twilio_token')) ?>" style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 4px; font-family: inherit;">
        </div>
        <div style="margin-bottom: 1rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Twilio Phone Number (From)</label>
            <input type="text" name="twilio_phone" value="<?= htmlspecialchars(get_setting($pdo, 'twilio_phone')) ?>" style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 4px; font-family: inherit;">
        </div>

        <div style="margin-bottom: 1.5rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Google Calendar Sync (Webhook URL)</label>
            <input type="url" name="google_calendar_webhook" value="<?= htmlspecialchars(get_setting($pdo, 'google_calendar_webhook')) ?>" style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 4px; font-family: inherit;">
            <small style="color: #64748b; display: block; margin-top: 0.25rem;">Enter a Zapier or Make.com webhook URL to sync bookings to your Google Calendar.</small>
        </div>

        <h4 style="margin-top: 2rem; margin-bottom: 1rem; color: var(--admin-primary); border-bottom: 1px solid #e2e8f0; padding-bottom: 0.5rem;">Payment Gateways</h4>
        
        <div style="margin-bottom: 1rem; display: flex; gap: 1rem;">
            <div style="flex: 1;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Environment Mode</label>
                <select name="payment_mode" style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 4px;">
                    <option value="sandbox" <?= get_setting($pdo, 'payment_mode') === 'sandbox' ? 'selected' : '' ?>>Sandbox (Test Mode)</option>
                    <option value="live" <?= get_setting($pdo, 'payment_mode') === 'live' ? 'selected' : '' ?>>Live (Production)</option>
                </select>
            </div>
            <div style="flex: 1;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Active Gateway</label>
                <select name="active_payment_gateway" style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 4px;">
                    <option value="stripe" <?= get_setting($pdo, 'active_payment_gateway') === 'stripe' ? 'selected' : '' ?>>Stripe</option>
                    <option value="paypal" <?= get_setting($pdo, 'active_payment_gateway') === 'paypal' ? 'selected' : '' ?>>PayPal</option>
                    <option value="payu" <?= get_setting($pdo, 'active_payment_gateway') === 'payu' ? 'selected' : '' ?>>PayU</option>
                    <option value="affirm" <?= get_setting($pdo, 'active_payment_gateway') === 'affirm' ? 'selected' : '' ?>>Affirm</option>
                    <option value="razorpay" <?= get_setting($pdo, 'active_payment_gateway') === 'razorpay' ? 'selected' : '' ?>>Razorpay</option>
                    <option value="paytm" <?= get_setting($pdo, 'active_payment_gateway') === 'paytm' ? 'selected' : '' ?>>PayTM</option>
                    <option value="mobikwik" <?= get_setting($pdo, 'active_payment_gateway') === 'mobikwik' ? 'selected' : '' ?>>MobiKwik</option>
                    <option value="phonepe" <?= get_setting($pdo, 'active_payment_gateway') === 'phonepe' ? 'selected' : '' ?>>PhonePe</option>
                    <option value="upi" <?= get_setting($pdo, 'active_payment_gateway') === 'upi' ? 'selected' : '' ?>>UPI / Google Pay / BHIM</option>
                    <option value="bharatpe" <?= get_setting($pdo, 'active_payment_gateway') === 'bharatpe' ? 'selected' : '' ?>>BharatPe</option>
                    <option value="amazonpay" <?= get_setting($pdo, 'active_payment_gateway') === 'amazonpay' ? 'selected' : '' ?>>Amazon Pay</option>
                    <option value="zoho_billing" <?= get_setting($pdo, 'active_payment_gateway') === 'zoho_billing' ? 'selected' : '' ?>>Zoho Billing</option>
                    <option value="ccavenue" <?= get_setting($pdo, 'active_payment_gateway') === 'ccavenue' ? 'selected' : '' ?>>CCAvenue</option>
                    <option value="jiopay" <?= get_setting($pdo, 'active_payment_gateway') === 'jiopay' ? 'selected' : '' ?>>Jio Pay</option>
                    <option value="airtel_money" <?= get_setting($pdo, 'active_payment_gateway') === 'airtel_money' ? 'selected' : '' ?>>Airtel Money</option>
                    <option value="vi_money" <?= get_setting($pdo, 'active_payment_gateway') === 'vi_money' ? 'selected' : '' ?>>Vi Money (Vodafone Idea)</option>
                </select>
            </div>
        </div>

        <div style="background-color: #f8fafc; padding: 1.5rem; border-radius: 4px; border: 1px solid #e2e8f0; margin-bottom: 1rem;">
            <h5 style="margin-top: 0; margin-bottom: 1rem;">Stripe Credentials (Includes Google Pay & Apple Pay)</h5>
            <input type="text" name="stripe_public_key" placeholder="Public Key" value="<?= htmlspecialchars(get_setting($pdo, 'stripe_public_key')) ?>" style="width: 100%; padding: 0.5rem; margin-bottom: 0.5rem; border: 1px solid #cbd5e1; border-radius: 4px;">
            <input type="password" name="stripe_secret_key" placeholder="Secret Key" value="<?= htmlspecialchars(get_setting($pdo, 'stripe_secret_key')) ?>" style="width: 100%; padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 4px;">
        </div>

        <div style="background-color: #f8fafc; padding: 1.5rem; border-radius: 4px; border: 1px solid #e2e8f0; margin-bottom: 1rem;">
            <h5 style="margin-top: 0; margin-bottom: 1rem;">PayPal Credentials</h5>
            <input type="text" name="paypal_client_id" placeholder="Client ID" value="<?= htmlspecialchars(get_setting($pdo, 'paypal_client_id')) ?>" style="width: 100%; padding: 0.5rem; margin-bottom: 0.5rem; border: 1px solid #cbd5e1; border-radius: 4px;">
            <input type="password" name="paypal_secret" placeholder="Secret Key" value="<?= htmlspecialchars(get_setting($pdo, 'paypal_secret')) ?>" style="width: 100%; padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 4px;">
        </div>

        <div style="background-color: #f8fafc; padding: 1.5rem; border-radius: 4px; border: 1px solid #e2e8f0; margin-bottom: 1rem;">
            <h5 style="margin-top: 0; margin-bottom: 1rem;">PayU Credentials</h5>
            <input type="text" name="payu_merchant_key" placeholder="Merchant Key" value="<?= htmlspecialchars(get_setting($pdo, 'payu_merchant_key')) ?>" style="width: 100%; padding: 0.5rem; margin-bottom: 0.5rem; border: 1px solid #cbd5e1; border-radius: 4px;">
            <input type="password" name="payu_salt" placeholder="Salt" value="<?= htmlspecialchars(get_setting($pdo, 'payu_salt')) ?>" style="width: 100%; padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 4px;">
        </div>

        <div style="background-color: #f8fafc; padding: 1.5rem; border-radius: 4px; border: 1px solid #e2e8f0; margin-bottom: 1rem;">
            <h5 style="margin-top: 0; margin-bottom: 1rem;">Affirm Credentials</h5>
            <input type="text" name="affirm_public_key" placeholder="Public Key" value="<?= htmlspecialchars(get_setting($pdo, 'affirm_public_key')) ?>" style="width: 100%; padding: 0.5rem; margin-bottom: 0.5rem; border: 1px solid #cbd5e1; border-radius: 4px;">
            <input type="password" name="affirm_private_key" placeholder="Private Key" value="<?= htmlspecialchars(get_setting($pdo, 'affirm_private_key')) ?>" style="width: 100%; padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 4px;">
        </div>

        <div style="background-color: #f8fafc; padding: 1.5rem; border-radius: 4px; border: 1px solid #e2e8f0; margin-bottom: 1.5rem;">
            <h5 style="margin-top: 0; margin-bottom: 1rem;">Razorpay Credentials (Includes Indian UPI & Google Pay)</h5>
            <input type="text" name="razorpay_key_id" placeholder="Key ID" value="<?= htmlspecialchars(get_setting($pdo, 'razorpay_key_id')) ?>" style="width: 100%; padding: 0.5rem; margin-bottom: 0.5rem; border: 1px solid #cbd5e1; border-radius: 4px;">
            <input type="password" name="razorpay_key_secret" placeholder="Key Secret" value="<?= htmlspecialchars(get_setting($pdo, 'razorpay_key_secret')) ?>" style="width: 100%; padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 4px;">
        </div>

        <div style="background-color: #f8fafc; padding: 1.5rem; border-radius: 4px; border: 1px solid #e2e8f0; margin-bottom: 1rem;">
            <h5 style="margin-top: 0; margin-bottom: 1rem;">PayTM Credentials</h5>
            <input type="text" name="paytm_merchant_id" placeholder="Merchant ID" value="<?= htmlspecialchars(get_setting($pdo, 'paytm_merchant_id')) ?>" style="width: 100%; padding: 0.5rem; margin-bottom: 0.5rem; border: 1px solid #cbd5e1; border-radius: 4px;">
            <input type="password" name="paytm_merchant_key" placeholder="Merchant Key" value="<?= htmlspecialchars(get_setting($pdo, 'paytm_merchant_key')) ?>" style="width: 100%; padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 4px;">
        </div>

        <div style="background-color: #f8fafc; padding: 1.5rem; border-radius: 4px; border: 1px solid #e2e8f0; margin-bottom: 1rem;">
            <h5 style="margin-top: 0; margin-bottom: 1rem;">MobiKwik Credentials</h5>
            <input type="text" name="mobikwik_merchant_id" placeholder="Merchant ID" value="<?= htmlspecialchars(get_setting($pdo, 'mobikwik_merchant_id')) ?>" style="width: 100%; padding: 0.5rem; margin-bottom: 0.5rem; border: 1px solid #cbd5e1; border-radius: 4px;">
            <input type="password" name="mobikwik_secret_key" placeholder="Secret Key" value="<?= htmlspecialchars(get_setting($pdo, 'mobikwik_secret_key')) ?>" style="width: 100%; padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 4px;">
        </div>

        <div style="background-color: #f8fafc; padding: 1.5rem; border-radius: 4px; border: 1px solid #e2e8f0; margin-bottom: 1rem;">
            <h5 style="margin-top: 0; margin-bottom: 1rem;">PhonePe Credentials</h5>
            <input type="text" name="phonepe_merchant_id" placeholder="Merchant ID" value="<?= htmlspecialchars(get_setting($pdo, 'phonepe_merchant_id')) ?>" style="width: 100%; padding: 0.5rem; margin-bottom: 0.5rem; border: 1px solid #cbd5e1; border-radius: 4px;">
            <input type="password" name="phonepe_salt_key" placeholder="Salt Key" value="<?= htmlspecialchars(get_setting($pdo, 'phonepe_salt_key')) ?>" style="width: 100%; padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 4px;">
        </div>

        <div style="background-color: #f8fafc; padding: 1.5rem; border-radius: 4px; border: 1px solid #e2e8f0; margin-bottom: 1rem;">
            <h5 style="margin-top: 0; margin-bottom: 1rem;">Generic UPI / BHIM (Direct Transfer)</h5>
            <input type="text" name="upi_vpa_address" placeholder="e.g. yourname@ybl, yourname@okaxis" value="<?= htmlspecialchars(get_setting($pdo, 'upi_vpa_address')) ?>" style="width: 100%; padding: 0.5rem; margin-bottom: 0.5rem; border: 1px solid #cbd5e1; border-radius: 4px;">
        </div>

        <div style="background-color: #f8fafc; padding: 1.5rem; border-radius: 4px; border: 1px solid #e2e8f0; margin-bottom: 1rem;">
            <h5 style="margin-top: 0; margin-bottom: 1rem;">BharatPe Credentials</h5>
            <input type="text" name="bharatpe_merchant_id" placeholder="Merchant ID" value="<?= htmlspecialchars(get_setting($pdo, 'bharatpe_merchant_id')) ?>" style="width: 100%; padding: 0.5rem; margin-bottom: 0.5rem; border: 1px solid #cbd5e1; border-radius: 4px;">
            <input type="password" name="bharatpe_token" placeholder="Token" value="<?= htmlspecialchars(get_setting($pdo, 'bharatpe_token')) ?>" style="width: 100%; padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 4px;">
        </div>

        <div style="background-color: #f8fafc; padding: 1.5rem; border-radius: 4px; border: 1px solid #e2e8f0; margin-bottom: 1.5rem;">
            <h5 style="margin-top: 0; margin-bottom: 1rem;">Amazon Pay Credentials</h5>
            <input type="text" name="amazonpay_merchant_id" placeholder="Merchant ID" value="<?= htmlspecialchars(get_setting($pdo, 'amazonpay_merchant_id')) ?>" style="width: 100%; padding: 0.5rem; margin-bottom: 0.5rem; border: 1px solid #cbd5e1; border-radius: 4px;">
            <input type="password" name="amazonpay_access_key" placeholder="Access Key" value="<?= htmlspecialchars(get_setting($pdo, 'amazonpay_access_key')) ?>" style="width: 100%; padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 4px;">
        </div>

        <div style="background-color: #f8fafc; padding: 1.5rem; border-radius: 4px; border: 1px solid #e2e8f0; margin-bottom: 1.5rem;">
            <h5 style="margin-top: 0; margin-bottom: 1rem;">Zoho Billing Credentials</h5>
            <input type="text" name="zoho_billing_client_id" placeholder="Client ID" value="<?= htmlspecialchars(get_setting($pdo, 'zoho_billing_client_id')) ?>" style="width: 100%; padding: 0.5rem; margin-bottom: 0.5rem; border: 1px solid #cbd5e1; border-radius: 4px;">
            <input type="password" name="zoho_billing_client_secret" placeholder="Client Secret" value="<?= htmlspecialchars(get_setting($pdo, 'zoho_billing_client_secret')) ?>" style="width: 100%; padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 4px;">
        </div>

        <div style="background-color: #f8fafc; padding: 1.5rem; border-radius: 4px; border: 1px solid #e2e8f0; margin-bottom: 1.5rem;">
            <h5 style="margin-top: 0; margin-bottom: 1rem;">CCAvenue Credentials</h5>
            <input type="text" name="ccavenue_merchant_id" placeholder="Merchant ID" value="<?= htmlspecialchars(get_setting($pdo, 'ccavenue_merchant_id')) ?>" style="width: 100%; padding: 0.5rem; margin-bottom: 0.5rem; border: 1px solid #cbd5e1; border-radius: 4px;">
            <input type="password" name="ccavenue_working_key" placeholder="Working Key" value="<?= htmlspecialchars(get_setting($pdo, 'ccavenue_working_key')) ?>" style="width: 100%; padding: 0.5rem; margin-bottom: 0.5rem; border: 1px solid #cbd5e1; border-radius: 4px;">
            <input type="text" name="ccavenue_access_code" placeholder="Access Code" value="<?= htmlspecialchars(get_setting($pdo, 'ccavenue_access_code')) ?>" style="width: 100%; padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 4px;">
        </div>

        <div style="background-color: #f8fafc; padding: 1.5rem; border-radius: 4px; border: 1px solid #e2e8f0; margin-bottom: 1.5rem;">
            <h5 style="margin-top: 0; margin-bottom: 1rem;">Jio Pay Credentials</h5>
            <input type="text" name="jiopay_merchant_id" placeholder="Merchant ID" value="<?= htmlspecialchars(get_setting($pdo, 'jiopay_merchant_id')) ?>" style="width: 100%; padding: 0.5rem; margin-bottom: 0.5rem; border: 1px solid #cbd5e1; border-radius: 4px;">
            <input type="password" name="jiopay_client_id" placeholder="Client ID" value="<?= htmlspecialchars(get_setting($pdo, 'jiopay_client_id')) ?>" style="width: 100%; padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 4px;">
        </div>

        <div style="background-color: #f8fafc; padding: 1.5rem; border-radius: 4px; border: 1px solid #e2e8f0; margin-bottom: 1.5rem;">
            <h5 style="margin-top: 0; margin-bottom: 1rem;">Airtel Money Credentials</h5>
            <input type="text" name="airtel_money_merchant_id" placeholder="Merchant ID" value="<?= htmlspecialchars(get_setting($pdo, 'airtel_money_merchant_id')) ?>" style="width: 100%; padding: 0.5rem; margin-bottom: 0.5rem; border: 1px solid #cbd5e1; border-radius: 4px;">
            <input type="password" name="airtel_money_hash_key" placeholder="Hash Key" value="<?= htmlspecialchars(get_setting($pdo, 'airtel_money_hash_key')) ?>" style="width: 100%; padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 4px;">
        </div>

        <div style="background-color: #f8fafc; padding: 1.5rem; border-radius: 4px; border: 1px solid #e2e8f0; margin-bottom: 1.5rem;">
            <h5 style="margin-top: 0; margin-bottom: 1rem;">Vi Money Credentials</h5>
            <input type="text" name="vi_money_merchant_id" placeholder="Merchant ID" value="<?= htmlspecialchars(get_setting($pdo, 'vi_money_merchant_id')) ?>" style="width: 100%; padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 4px;">
        </div>
        
        <button type="submit" style="padding: 0.75rem 1.5rem; background-color: var(--admin-accent); color: white; border: none; border-radius: 4px; font-weight: 600; cursor: pointer;">Save Settings</button>
    </form>
</div>

<?php require_once 'includes/admin_footer.php'; ?>
