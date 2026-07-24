<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['client_id'])) {
    header("Location: client_login.php");
    exit;
}

$client_id = $_SESSION['client_id'];
$success = '';
$error = '';

// Handle Document Upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['document'])) {
    $title = trim($_POST['title'] ?? 'Client Upload');
    $uploadDir = 'assets/uploads/clients/';
    $fileName = time() . '_' . preg_replace("/[^a-zA-Z0-9.]/", "_", basename($_FILES['document']['name']));
    $targetFile = $uploadDir . $fileName;
    
    if (move_uploaded_file($_FILES['document']['tmp_name'], $targetFile)) {
        $stmt = $pdo->prepare("INSERT INTO client_documents (client_id, title, file_path, uploaded_by) VALUES (?, ?, ?, 'client')");
        $stmt->execute([$client_id, $title, $targetFile]);
        $success = "Document uploaded successfully.";
    } else {
        $error = "Failed to upload document. Please ensure the file is valid and within size limits.";
    }
}

// Handle Document Deletion (only client-uploaded ones)
if (isset($_GET['del_doc'])) {
    $doc_id = (int)$_GET['del_doc'];
    $stmt = $pdo->prepare("SELECT file_path FROM client_documents WHERE id = ? AND client_id = ? AND uploaded_by = 'client'");
    $stmt->execute([$doc_id, $client_id]);
    $doc = $stmt->fetch();
    if ($doc) {
        if (file_exists($doc['file_path'])) unlink($doc['file_path']);
        $pdo->prepare("DELETE FROM client_documents WHERE id = ?")->execute([$doc_id]);
        $success = "Document deleted.";
    } else {
        $error = "You do not have permission to delete this document.";
    }
}

$page_title = 'Secure Client Dashboard';
require_once 'includes/header.php';
$stmt = $pdo->prepare("SELECT case_status FROM clients WHERE id = ?");
$stmt->execute([$client_id]);
$client_data = $stmt->fetch();
$case_status = $client_data['case_status'] ?? 'Intake';
$statuses = ['Intake', 'Discovery', 'Litigation', 'Settled'];
$current_index = array_search($case_status, $statuses);
?>

<section class="section" style="background-color: var(--primary-color); padding: 40px 0; color: white;">
    <div class="container" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h2 style="color: white; margin: 0;">Welcome, <?= htmlspecialchars($_SESSION['client_name']) ?></h2>
        <a href="client_logout.php" class="btn btn-primary" style="background-color: #64748b; border: none;">Logout</a>
    </div>
    <div class="container">
        <h4 style="margin-top: 0; margin-bottom: 1rem; color: #cbd5e1; font-weight: 500;">Live Case Progress</h4>
        <div style="display: flex; justify-content: space-between; align-items: center; background: rgba(255,255,255,0.1); padding: 1.5rem; border-radius: 8px; position: relative;">
            <div style="position: absolute; top: 50%; left: 10%; right: 10%; height: 4px; background: rgba(255,255,255,0.2); z-index: 1;"></div>
            <?php foreach ($statuses as $i => $status): ?>
                <?php 
                $isActive = $i <= $current_index;
                $isCurrent = $i === $current_index;
                $color = $isActive ? '#10b981' : '#64748b'; 
                ?>
                <div style="text-align: center; position: relative; z-index: 2;">
                    <div style="width: 30px; height: 30px; border-radius: 50%; background-color: <?= $color ?>; margin: 0 auto 0.5rem; border: 4px solid var(--primary-color); display: flex; align-items: center; justify-content: center;">
                        <?php if ($isActive): ?><i class="fas fa-check" style="font-size: 0.6rem; color: white;"></i><?php endif; ?>
                    </div>
                    <span style="font-size: 0.85rem; font-weight: <?= $isCurrent ? 'bold' : 'normal' ?>; color: <?= $isActive ? 'white' : '#94a3b8' ?>;"><?= $status ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section bg-light">
    <div class="container">
        <?php if ($success): ?>
            <div style="background-color: #d1fae5; color: #065f46; padding: 1rem; border-radius: 4px; margin-bottom: 1.5rem;">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div style="background-color: #fee2e2; color: #991b1b; padding: 1rem; border-radius: 4px; margin-bottom: 1.5rem;">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <div class="grid-2">
            <div class="card">
                <h3 style="margin-top: 0; color: var(--primary-color); margin-bottom: 1.5rem;"><i class="fas fa-folder-open"></i> Your Case Documents</h3>
                <ul style="list-style: none; padding: 0; margin: 0;">
                    <?php
                    $stmt = $pdo->prepare("SELECT * FROM client_documents WHERE client_id = ? ORDER BY uploaded_at DESC");
                    $stmt->execute([$client_id]);
                    $docs = $stmt->fetchAll();
                    if (count($docs) > 0): foreach ($docs as $doc):
                    ?>
                    <li style="padding: 1rem; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <strong><?= htmlspecialchars($doc['title']) ?></strong>
                            <div style="font-size: 0.85rem; color: #64748b; margin-top: 0.25rem;">
                                <?php if ($doc['uploaded_by'] === 'admin'): ?>
                                    <span style="color: var(--secondary-color); font-weight: bold;"><i class="fas fa-shield-alt"></i> Shared by Attorney</span>
                                <?php else: ?>
                                    Uploaded by you
                                <?php endif; ?>
                                | <?= date('M j, Y', strtotime($doc['uploaded_at'])) ?>
                            </div>
                        </div>
                        <div>
                            <a href="<?= htmlspecialchars($doc['file_path']) ?>" target="_blank" class="btn btn-primary" style="padding: 0.4rem 0.8rem; font-size: 0.85rem;"><i class="fas fa-download"></i> View</a>
                            <?php if ($doc['requires_signature']): ?>
                                <?php if ($doc['is_signed']): ?>
                                    <span style="background-color: #d1fae5; color: #065f46; padding: 0.4rem 0.8rem; border-radius: 4px; font-size: 0.85rem; font-weight: 600; display: inline-block; margin-left: 0.5rem;"><i class="fas fa-check"></i> Signed</span>
                                <?php else: ?>
                                    <a href="sign_document.php?doc_id=<?= $doc['id'] ?>" class="btn" style="background-color: #f59e0b; color: white; padding: 0.4rem 0.8rem; font-size: 0.85rem; margin-left: 0.5rem;"><i class="fas fa-pen-nib"></i> Sign Now</a>
                                <?php endif; ?>
                            <?php endif; ?>
                            <?php if ($doc['uploaded_by'] === 'client'): ?>
                                <a href="client_dashboard.php?del_doc=<?= $doc['id'] ?>" class="btn" style="background-color: #ef4444; color: white; padding: 0.4rem 0.8rem; font-size: 0.85rem; margin-left: 0.5rem;" onclick="return confirm('Delete this document permanently?');"><i class="fas fa-trash"></i></a>
                            <?php endif; ?>
                        </div>
                    </li>
                    <?php endforeach; else: ?>
                    <li style="padding: 2rem; text-align: center; color: #64748b; background-color: #f8fafc; border-radius: 4px;">No documents found in your file.</li>
                    <?php endif; ?>
                </ul>
            </div>
            
            <div class="card" style="align-self: start;">
                <h3 style="margin-top: 0; color: var(--primary-color); margin-bottom: 1.5rem;"><i class="fas fa-upload"></i> Upload Secure Document</h3>
                <p style="color: var(--text-light); margin-bottom: 1.5rem; font-size: 0.95rem;">Upload evidence, signed forms, or requested documents. These files are securely transmitted directly to your attorney.</p>
                <form method="POST" action="client_dashboard.php" enctype="multipart/form-data">
                    <div class="form-group">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Document Title</label>
                        <input type="text" name="title" class="form-control" required placeholder="e.g., Signed Affidavit">
                    </div>
                    <div class="form-group">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">File (PDF, DOCX, JPG)</label>
                        <input type="file" name="document" class="form-control" required style="padding: 0.5rem;">
                    </div>
                    <button type="submit" class="btn btn-primary" style="width: 100%;"><i class="fas fa-lock"></i> Securely Upload</button>
                </form>
            </div>
        </div>

        <!-- Billing & Invoices Section -->
        <div class="card" style="margin-top: 2rem;">
            <h3 style="margin-top: 0; color: var(--primary-color); margin-bottom: 1.5rem;"><i class="fas fa-file-invoice-dollar"></i> Billing & Invoices</h3>
            <ul style="list-style: none; padding: 0; margin: 0;">
                <?php
                $stmt = $pdo->prepare("SELECT * FROM invoices WHERE client_id = ? ORDER BY created_at DESC");
                $stmt->execute([$client_id]);
                $invoices = $stmt->fetchAll();
                if (count($invoices) > 0): foreach ($invoices as $inv):
                ?>
                <li style="padding: 1.5rem; border: 1px solid #e2e8f0; border-radius: 4px; margin-bottom: 1rem; display: flex; justify-content: space-between; align-items: center; background-color: <?= $inv['status'] === 'paid' ? '#f0fdf4' : '#fff' ?>;">
                    <div>
                        <strong style="font-size: 1.1rem;"><?= htmlspecialchars($inv['description']) ?></strong>
                        <div style="font-size: 0.85rem; color: #64748b; margin-top: 0.25rem;">
                            Issued: <?= date('M j, Y', strtotime($inv['created_at'])) ?>
                        </div>
                    </div>
                    <div style="text-align: right;">
                        <div style="font-size: 1.25rem; font-weight: bold; margin-bottom: 0.5rem;">$<?= number_format($inv['amount'], 2) ?></div>
                        <?php if ($inv['status'] === 'paid'): ?>
                            <?php 
                            $gateway_badge = '';
                            if (!empty($inv['payment_gateway_used'])) {
                                $gw = htmlspecialchars($inv['payment_gateway_used']);
                                if ($gw == 'paypal') $gateway_badge = ' <i class="fab fa-paypal"></i> PayPal';
                                elseif ($gw == 'stripe') $gateway_badge = ' <i class="fab fa-stripe"></i> Stripe';
                                elseif ($gw == 'payu') $gateway_badge = ' PayU';
                                elseif ($gw == 'affirm') $gateway_badge = ' Affirm';
                                elseif ($gw == 'razorpay') $gateway_badge = ' Razorpay';
                                elseif ($gw == 'paytm') $gateway_badge = ' PayTM';
                                elseif ($gw == 'mobikwik') $gateway_badge = ' MobiKwik';
                                elseif ($gw == 'phonepe') $gateway_badge = ' PhonePe';
                                elseif ($gw == 'upi') $gateway_badge = ' UPI / VPA';
                                elseif ($gw == 'bharatpe') $gateway_badge = ' BharatPe';
                                elseif ($gw == 'amazonpay') $gateway_badge = ' <i class="fab fa-amazon"></i> Amazon Pay';
                                elseif ($gw == 'zoho_billing') $gateway_badge = ' Zoho Billing';
                                elseif ($gw == 'ccavenue') $gateway_badge = ' CCAvenue';
                                elseif ($gw == 'jiopay') $gateway_badge = ' Jio Pay';
                                elseif ($gw == 'airtel_money') $gateway_badge = ' Airtel Money';
                                elseif ($gw == 'vi_money') $gateway_badge = ' Vi Money';
                            }
                            ?>
                            <span style="background-color: #d1fae5; color: #065f46; padding: 0.4rem 0.8rem; border-radius: 9999px; font-size: 0.85rem; font-weight: 600;"><i class="fas fa-check-circle"></i> Paid<?= $gateway_badge ?></span>
                        <?php else: ?>
                            <a href="checkout.php?invoice_id=<?= $inv['id'] ?>" class="btn btn-primary" style="background-color: #3b82f6; padding: 0.4rem 1rem; font-size: 0.9rem;"><i class="fas fa-credit-card"></i> Pay Now</a>
                        <?php endif; ?>
                    </div>
                </li>
                <?php endforeach; else: ?>
                <li style="padding: 2rem; text-align: center; color: #64748b; background-color: #f8fafc; border-radius: 4px;">No pending invoices.</li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
