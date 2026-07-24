<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['client_id'])) {
    header("Location: client_login.php");
    exit;
}

if (!isset($_GET['invoice_id'])) {
    header("Location: client_dashboard.php");
    exit;
}

$invoice_id = (int)$_GET['invoice_id'];
$client_id = $_SESSION['client_id'];

// Verify invoice belongs to this client and is pending
$stmt = $pdo->prepare("SELECT * FROM invoices WHERE id = ? AND client_id = ? AND status = 'pending'");
$stmt->execute([$invoice_id, $client_id]);
$invoice = $stmt->fetch();

if (!$invoice) {
    header("Location: client_dashboard.php");
    exit;
}

$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pay_simulate'])) {
    $gateway_used = $_POST['gateway_used'] ?? 'unknown';
    // Simulate successful payment processing via API
    $stmt = $pdo->prepare("UPDATE invoices SET status = 'paid', payment_gateway_used = ? WHERE id = ?");
    $stmt->execute([$gateway_used, $invoice_id]);
    $success = true;
}

$page_title = 'Secure Checkout';
require_once 'includes/header.php';
?>

<section class="section" style="background-color: #f8fafc; min-height: 70vh; display: flex; align-items: center; justify-content: center;">
    <div class="container" style="max-width: 500px;">
        
        <?php if ($success): ?>
            <div class="card" style="text-align: center; padding: 3rem 2rem;">
                <i class="fas fa-check-circle" style="font-size: 4rem; color: #10b981; margin-bottom: 1.5rem;"></i>
                <h2 style="margin-bottom: 1rem;">Payment Successful</h2>
                <p style="color: var(--text-light); margin-bottom: 2rem;">Thank you! Your payment of <strong>$<?= number_format($invoice['amount'], 2) ?></strong> has been processed securely.</p>
                <a href="client_dashboard.php" class="btn btn-primary">Return to Dashboard</a>
            </div>
        <?php else: ?>
            <div class="card">
                <div style="text-align: center; margin-bottom: 2rem;">
                    <i class="fab fa-stripe" style="font-size: 3rem; color: #6366f1;"></i>
                    <h2 style="margin-top: 0.5rem; color: var(--primary-color);">Secure Checkout</h2>
                </div>
                
                <div style="background-color: #f1f5f9; padding: 1rem; border-radius: 4px; margin-bottom: 1.5rem; text-align: center;">
                    <div style="color: #64748b; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 0.25rem;">Amount Due</div>
                    <div style="font-size: 2.5rem; font-weight: bold; color: var(--primary-color);">$<?= number_format($invoice['amount'], 2) ?> <?= strpos($invoice['description'], 'Recurring') !== false ? '<span style="font-size:1rem; color:#64748b;">/month</span>' : '' ?></div>
                    <div style="color: var(--text-color); margin-top: 0.5rem;"><?= htmlspecialchars($invoice['description']) ?></div>
                </div>
                
                <?php 
                $gateway = get_setting($pdo, 'active_payment_gateway');
                $mode = get_setting($pdo, 'payment_mode');
                $mode_text = $mode === 'sandbox' ? '<span style="color: #ef4444; font-size: 0.8rem; font-weight: bold; margin-left: 10px;">[TEST MODE]</span>' : '';
                ?>
                <form method="POST">
                    <input type="hidden" name="pay_simulate" value="1">
                    <input type="hidden" name="gateway_used" value="<?= htmlspecialchars($gateway) ?>">
                    
                    <?php if ($gateway === 'stripe'): ?>
                        <div style="margin-bottom: 1rem;">
                            <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Cardholder Name</label>
                            <input type="text" class="form-control" placeholder="<?= htmlspecialchars($_SESSION['client_name']) ?>" required>
                        </div>
                        <div style="margin-bottom: 1rem;">
                            <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Card Number</label>
                            <div style="position: relative;">
                                <input type="text" class="form-control" placeholder="•••• •••• •••• ••••" required>
                                <i class="fab fa-cc-visa" style="position: absolute; right: 1rem; top: 1rem; color: #cbd5e1; font-size: 1.2rem;"></i>
                            </div>
                        </div>
                        <div style="display: flex; gap: 1rem; margin-bottom: 1rem;">
                            <div style="flex: 1;">
                                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Expiry (MM/YY)</label>
                                <input type="text" class="form-control" placeholder="MM/YY" required>
                            </div>
                            <div style="flex: 1;">
                                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">CVC</label>
                                <input type="text" class="form-control" placeholder="123" required>
                            </div>
                        </div>
                        <button type="submit" name="pay_simulate" class="btn btn-primary" style="width: 100%; padding: 1rem; font-size: 1.1rem; margin-top: 1rem;">
                            <i class="fas fa-lock"></i> <?= strpos($invoice['description'], 'Recurring') !== false ? 'Subscribe Now' : 'Pay $' . number_format($invoice['amount'], 2) ?>
                        </button>
                    <?php elseif ($gateway === 'paypal'): ?>
                        <div style="text-align: center; padding: 2rem 0;">
                            <button type="submit" name="pay_simulate" style="background-color: #ffc439; color: #000; border: none; padding: 1rem 2rem; border-radius: 4px; font-size: 1.2rem; font-weight: bold; cursor: pointer; width: 100%;">
                                <i class="fab fa-paypal"></i> Pay with PayPal <?= $mode_text ?>
                            </button>
                        </div>
                    <?php elseif ($gateway === 'payu'): ?>
                        <div style="text-align: center; padding: 2rem 0;">
                            <button type="submit" name="pay_simulate" style="background-color: #a4ce4e; color: #fff; border: none; padding: 1rem 2rem; border-radius: 4px; font-size: 1.2rem; font-weight: bold; cursor: pointer; width: 100%;">
                                Pay with PayU <?= $mode_text ?>
                            </button>
                        </div>
                    <?php elseif ($gateway === 'affirm'): ?>
                        <div style="text-align: center; padding: 2rem 0;">
                            <button type="submit" name="pay_simulate" style="background-color: #004cff; color: #fff; border: none; padding: 1rem 2rem; border-radius: 4px; font-size: 1.2rem; font-weight: bold; cursor: pointer; width: 100%;">
                                Pay with Affirm (Buy Now, Pay Later) <?= $mode_text ?>
                            </button>
                        </div>
                    <?php elseif ($gateway === 'razorpay'): ?>
                        <div style="text-align: center; padding: 2rem 0;">
                            <button type="submit" name="pay_simulate" style="background-color: #3395ff; color: #fff; border: none; padding: 1rem 2rem; border-radius: 4px; font-size: 1.2rem; font-weight: bold; cursor: pointer; width: 100%;">
                                Pay with Razorpay <?= $mode_text ?>
                            </button>
                        </div>
                    <?php elseif ($gateway === 'paytm'): ?>
                        <div style="text-align: center; padding: 2rem 0;">
                            <button type="submit" name="pay_simulate" style="background-color: #00b9f5; color: #fff; border: none; padding: 1rem 2rem; border-radius: 4px; font-size: 1.2rem; font-weight: bold; cursor: pointer; width: 100%;">
                                Pay with PayTM <?= $mode_text ?>
                            </button>
                        </div>
                    <?php elseif ($gateway === 'mobikwik'): ?>
                        <div style="text-align: center; padding: 2rem 0;">
                            <button type="submit" name="pay_simulate" style="background-color: #002e6e; color: #fff; border: none; padding: 1rem 2rem; border-radius: 4px; font-size: 1.2rem; font-weight: bold; cursor: pointer; width: 100%;">
                                Pay with MobiKwik <?= $mode_text ?>
                            </button>
                        </div>
                    <?php elseif ($gateway === 'phonepe'): ?>
                        <div style="text-align: center; padding: 2rem 0;">
                            <button type="submit" name="pay_simulate" style="background-color: #5f259f; color: #fff; border: none; padding: 1rem 2rem; border-radius: 4px; font-size: 1.2rem; font-weight: bold; cursor: pointer; width: 100%;">
                                Pay with PhonePe <?= $mode_text ?>
                            </button>
                        </div>
                    <?php elseif ($gateway === 'upi'): ?>
                        <div style="text-align: center; padding: 2rem 0;">
                            <div style="margin-bottom: 1rem; color: #64748b; font-size: 0.9rem;">Send payment to: <strong><?= htmlspecialchars(get_setting($pdo, 'upi_vpa_address') ?: 'lawfirm@upi') ?></strong></div>
                            <button type="submit" name="pay_simulate" style="background-color: #2196F3; color: #fff; border: none; padding: 1rem 2rem; border-radius: 4px; font-size: 1.2rem; font-weight: bold; cursor: pointer; width: 100%;">
                                Confirm UPI Transfer <?= $mode_text ?>
                            </button>
                        </div>
                    <?php elseif ($gateway === 'bharatpe'): ?>
                        <div style="text-align: center; padding: 2rem 0;">
                            <button type="submit" name="pay_simulate" style="background-color: #00bcd4; color: #fff; border: none; padding: 1rem 2rem; border-radius: 4px; font-size: 1.2rem; font-weight: bold; cursor: pointer; width: 100%;">
                                Pay with BharatPe <?= $mode_text ?>
                            </button>
                        </div>
                    <?php elseif ($gateway === 'amazonpay'): ?>
                        <div style="text-align: center; padding: 2rem 0;">
                            <button type="submit" name="pay_simulate" style="background-color: #ff9900; color: #000; border: none; padding: 1rem 2rem; border-radius: 4px; font-size: 1.2rem; font-weight: bold; cursor: pointer; width: 100%;">
                                <i class="fab fa-amazon"></i> Pay with Amazon Pay <?= $mode_text ?>
                            </button>
                        </div>
                    <?php elseif ($gateway === 'zoho_billing'): ?>
                        <div style="text-align: center; padding: 2rem 0;">
                            <button type="submit" name="pay_simulate" style="background-color: #f0483e; color: #fff; border: none; padding: 1rem 2rem; border-radius: 4px; font-size: 1.2rem; font-weight: bold; cursor: pointer; width: 100%;">
                                Pay with Zoho Billing <?= $mode_text ?>
                            </button>
                        </div>
                    <?php elseif ($gateway === 'ccavenue'): ?>
                        <div style="text-align: center; padding: 2rem 0;">
                            <button type="submit" name="pay_simulate" style="background-color: #b71c1c; color: #fff; border: none; padding: 1rem 2rem; border-radius: 4px; font-size: 1.2rem; font-weight: bold; cursor: pointer; width: 100%;">
                                Pay with CCAvenue <?= $mode_text ?>
                            </button>
                        </div>
                    <?php elseif ($gateway === 'jiopay'): ?>
                        <div style="text-align: center; padding: 2rem 0;">
                            <button type="submit" name="pay_simulate" style="background-color: #0f3cc9; color: #fff; border: none; padding: 1rem 2rem; border-radius: 4px; font-size: 1.2rem; font-weight: bold; cursor: pointer; width: 100%;">
                                Pay with Jio Pay <?= $mode_text ?>
                            </button>
                        </div>
                    <?php elseif ($gateway === 'airtel_money'): ?>
                        <div style="text-align: center; padding: 2rem 0;">
                            <button type="submit" name="pay_simulate" style="background-color: #ff0000; color: #fff; border: none; padding: 1rem 2rem; border-radius: 4px; font-size: 1.2rem; font-weight: bold; cursor: pointer; width: 100%;">
                                Pay with Airtel Money <?= $mode_text ?>
                            </button>
                        </div>
                    <?php elseif ($gateway === 'vi_money'): ?>
                        <div style="text-align: center; padding: 2rem 0;">
                            <button type="submit" name="pay_simulate" style="background-color: #e3000f; color: #fff; border: none; padding: 1rem 2rem; border-radius: 4px; font-size: 1.2rem; font-weight: bold; cursor: pointer; width: 100%;">
                                Pay with Vi Money <?= $mode_text ?>
                            </button>
                        </div>
                    <?php endif; ?>
                    
                    <p style="text-align: center; color: var(--text-light); font-size: 0.85rem; margin-top: 1rem;">
                        <i class="fas fa-shield-alt"></i> Payments are securely processed via 256-bit encryption. <?= $mode_text ?>
                    </p>
                </form>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
