<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['admin_id'])) {
    exit;
}

if (!isset($_GET['client_id'])) {
    die("Client ID required.");
}

$client_id = (int)$_GET['client_id'];
$stmt = $pdo->prepare("SELECT * FROM clients WHERE id = ?");
$stmt->execute([$client_id]);
$client = $stmt->fetch();

if (!$client) {
    die("Client not found.");
}

$date = date('F j, Y');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Retainer Agreement - <?= htmlspecialchars($client['name']) ?></title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; line-height: 1.6; color: #000; margin: 0; padding: 0; background: #525659; }
        .page { background: white; width: 210mm; min-height: 297mm; padding: 20mm; margin: 10mm auto; box-shadow: 0 0 10px rgba(0,0,0,0.5); }
        h1, h2 { text-align: center; }
        .signature-block { margin-top: 50px; display: flex; justify-content: space-between; }
        .signature-line { border-top: 1px solid #000; width: 45%; padding-top: 5px; margin-top: 50px; }
        @media print {
            body { background: white; margin: 0; }
            .page { margin: 0; box-shadow: none; width: auto; min-height: auto; padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div style="text-align: center; padding: 10px; background: #333; color: white;" class="no-print">
        <button onclick="window.print()" style="padding: 10px 20px; cursor: pointer; font-size: 16px;">Print / Save as PDF</button>
    </div>
    <div class="page">
        <h1>LEGAL SERVICES RETAINER AGREEMENT</h1>
        <p>This Legal Services Retainer Agreement ("Agreement") is made effective as of <strong><?= $date ?></strong>.</p>
        
        <h2>1. PARTIES</h2>
        <p><strong>Attorney/Firm:</strong> <?= htmlspecialchars(get_setting($pdo, 'site_title') ?? 'The Law Firm') ?><br>
        <strong>Client:</strong> <?= htmlspecialchars($client['name']) ?> (Email: <?= htmlspecialchars($client['email']) ?>)</p>
        
        <h2>2. SCOPE OF SERVICES</h2>
        <p>The Attorney agrees to represent the Client in matters pertaining to their active case (Status: <strong><?= htmlspecialchars($client['case_status'] ?? 'Intake') ?></strong>). The Attorney will provide legal counsel, draft necessary documents, and represent the Client in negotiations or court proceedings as required.</p>
        
        <h2>3. FEES & RETAINER</h2>
        <p>The Client agrees to pay the Attorney for legal services based on an hourly rate or a flat fee as discussed. A retainer fee may be required before services commence, which will be deposited into a client trust account and billed against.</p>
        
        <h2>4. TERMINATION</h2>
        <p>Either party may terminate this agreement at any time by providing written notice. The Client will remain responsible for all fees incurred up to the date of termination.</p>
        
        <div class="signature-block">
            <div class="signature-line">
                Attorney Signature<br>
                Date: <?= $date ?>
            </div>
            <div class="signature-line">
                Client Signature: <?= htmlspecialchars($client['name']) ?><br>
                Date: 
            </div>
        </div>
    </div>
</body>
</html>
