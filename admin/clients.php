<?php
require_once 'includes/admin_header.php';

$action = $_GET['action'] ?? 'list';
$error = '';
$success = '';

if ($action === 'delete' && isset($_GET['id'])) {
    $stmt = $pdo->prepare("DELETE FROM clients WHERE id = ?");
    if ($stmt->execute([(int)$_GET['id']])) {
        $success = "Client deleted successfully.";
        $action = 'list';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $case_status = $_POST['case_status'] ?? 'Intake';

    if ($name && $email) {
        if (isset($_POST['id']) && !empty($_POST['id'])) {
            $id = (int)$_POST['id'];
            if ($password) {
                $hash = password_hash($password, PASSWORD_BCRYPT);
                $stmt = $pdo->prepare("UPDATE clients SET name = ?, email = ?, password = ?, case_status = ? WHERE id = ?");
                $stmt->execute([$name, $email, $hash, $case_status, $id]);
            } else {
                $stmt = $pdo->prepare("UPDATE clients SET name = ?, email = ?, case_status = ? WHERE id = ?");
                $stmt->execute([$name, $email, $case_status, $id]);
            }
            $success = "Client updated successfully.";
            $action = 'list';
        } else {
            if ($password) {
                $stmt = $pdo->prepare("SELECT id FROM clients WHERE email = ?");
                $stmt->execute([$email]);
                if ($stmt->fetch()) {
                    $error = "Email already exists.";
                } else {
                    $hash = password_hash($password, PASSWORD_BCRYPT);
                    $stmt = $pdo->prepare("INSERT INTO clients (name, email, password, case_status) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$name, $email, $hash, $case_status]);
                    $success = "Client created successfully.";
                    $action = 'list';
                }
            } else {
                $error = "Password is required for new clients.";
            }
        }
    } else {
        $error = "Name and Email are required fields.";
    }
}
?>

<?php if ($action === 'add' || $action === 'edit'): ?>
    <?php
    $item = ['id' => '', 'name' => '', 'email' => ''];
    if ($action === 'edit' && isset($_GET['id'])) {
        $stmt = $pdo->prepare("SELECT * FROM clients WHERE id = ?");
        $stmt->execute([(int)$_GET['id']]);
        $fetched = $stmt->fetch();
        if ($fetched) $item = $fetched;
    }
    ?>
    <div class="card" style="max-width: 600px;">
        <h3 style="margin-top: 0; color: var(--admin-primary);"><?= $action === 'add' ? 'Add' : 'Edit' ?> Client Portal Account</h3>
        
        <?php if ($error): ?>
            <div style="background-color: #fee2e2; color: #991b1b; padding: 1rem; border-radius: 4px; margin-bottom: 1rem;">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="clients.php?action=<?= $action ?>">
            <?php if ($item['id']): ?>
                <input type="hidden" name="id" value="<?= $item['id'] ?>">
            <?php endif; ?>
            
            <div style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Client Name *</label>
                <input type="text" name="name" value="<?= htmlspecialchars($item['name']) ?>" required style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 4px; font-family: inherit;">
            </div>
            
            <div style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Email Address * (Used for login)</label>
                <input type="email" name="email" value="<?= htmlspecialchars($item['email'] ?? '') ?>" required style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 4px; font-family: inherit;">
            </div>
            
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Case Status</label>
                <select name="case_status" style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 4px; font-family: inherit;">
                    <option value="Intake" <?= ($item['case_status'] ?? '') === 'Intake' ? 'selected' : '' ?>>Intake</option>
                    <option value="Discovery" <?= ($item['case_status'] ?? '') === 'Discovery' ? 'selected' : '' ?>>Discovery</option>
                    <option value="Litigation" <?= ($item['case_status'] ?? '') === 'Litigation' ? 'selected' : '' ?>>Litigation</option>
                    <option value="Settled" <?= ($item['case_status'] ?? '') === 'Settled' ? 'selected' : '' ?>>Settled</option>
                </select>
            </div>
            
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Password <?= $action === 'edit' ? '(leave blank to keep current)' : '*' ?></label>
                <input type="password" name="password" <?= $action === 'add' ? 'required' : '' ?> style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 4px; font-family: inherit;">
            </div>
            
            <div>
                <button type="submit" style="padding: 0.75rem 1.5rem; background-color: var(--admin-accent); color: white; border: none; border-radius: 4px; font-weight: 600; cursor: pointer;">Save Client</button>
                <a href="clients.php" style="padding: 0.75rem 1.5rem; background-color: #e2e8f0; color: #475569; text-decoration: none; border-radius: 4px; margin-left: 0.5rem; font-weight: 500; display: inline-block;">Cancel</a>
            </div>
        </form>
    </div>

<?php elseif ($action === 'view' && isset($_GET['id'])): ?>
    <?php
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM clients WHERE id = ?");
    $stmt->execute([$id]);
    $client = $stmt->fetch();
    
    // Handle admin document upload
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['document'])) {
        $title = trim($_POST['title'] ?? 'Document');
        $uploadDir = '../assets/uploads/clients/';
        $fileName = time() . '_' . preg_replace("/[^a-zA-Z0-9.]/", "_", basename($_FILES['document']['name']));
        $targetFile = $uploadDir . $fileName;
        
        if (move_uploaded_file($_FILES['document']['tmp_name'], $targetFile)) {
            $req_sig = isset($_POST['requires_signature']) ? 1 : 0;
            $stmt = $pdo->prepare("INSERT INTO client_documents (client_id, title, file_path, uploaded_by, requires_signature) VALUES (?, ?, ?, 'admin', ?)");
            $stmt->execute([$id, $title, 'assets/uploads/clients/' . $fileName, $req_sig]);
            $success = "Document shared with client.";
        } else {
            $error = "File upload failed.";
        }
    }
    
    // Handle delete document
    if (isset($_GET['del_doc'])) {
        $doc_id = (int)$_GET['del_doc'];
        $stmt = $pdo->prepare("SELECT file_path FROM client_documents WHERE id = ? AND client_id = ?");
        $stmt->execute([$doc_id, $id]);
        $doc = $stmt->fetch();
        if ($doc) {
            if (file_exists('../' . $doc['file_path'])) unlink('../' . $doc['file_path']);
            $pdo->prepare("DELETE FROM client_documents WHERE id = ?")->execute([$doc_id]);
            $success = "Document deleted.";
        }
    }
    ?>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <div>
            <h3 style="margin: 0; color: var(--admin-primary);">Client Portal: <?= htmlspecialchars($client['name']) ?></h3>
            <p style="color: var(--text-light); margin-top: 0.25rem;"><?= htmlspecialchars($client['email']) ?></p>
        </div>
        <div>
            <a href="generate_pdf.php?client_id=<?= $client['id'] ?>" target="_blank" class="btn btn-primary" style="padding: 0.5rem 1rem; background-color: #10b981; color: white; text-decoration: none; border-radius: 4px; font-weight: 500; margin-right: 10px;"><i class="fas fa-file-pdf"></i> Generate Retainer</a>
            <a href="clients.php" class="btn btn-primary" style="padding: 0.5rem 1rem; background-color: #64748b; color: white; text-decoration: none; border-radius: 4px; font-weight: 500;">&larr; Back to Clients</a>
        </div>
    </div>

    <?php
    // Handle Invoice Generation
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['invoice_amount'])) {
        $amount = (float)$_POST['invoice_amount'];
        $desc = trim($_POST['invoice_desc']);
        $is_sub = isset($_POST['is_subscription']) && $_POST['is_subscription'] == '1';
        if ($is_sub) {
            $desc .= " (Monthly Recurring Subscription)";
        }
        
        if ($amount > 0 && $desc) {
            $stmt = $pdo->prepare("INSERT INTO invoices (client_id, description, amount) VALUES (?, ?, ?)");
            $stmt->execute([$id, $desc, $amount]);
            $success = $is_sub ? "Recurring Subscription Invoice generated successfully." : "Invoice generated successfully.";
        }
    }
    
    // Handle Time Logging
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['log_time'])) {
        $mins = (int)$_POST['duration_minutes'];
        $rate = (float)$_POST['hourly_rate'];
        $total = ($mins / 60) * $rate;
        $admin_id = $_SESSION['admin_id'];
        
        $stmt = $pdo->prepare("INSERT INTO time_logs (admin_id, client_id, duration_minutes, hourly_rate, total_amount) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$admin_id, $id, $mins, $rate, $total]);
        
        // Auto-generate invoice for this time
        $desc = "Billable Hours: " . round($mins / 60, 2) . " hrs @ $" . number_format($rate, 2) . "/hr";
        $stmt = $pdo->prepare("INSERT INTO invoices (client_id, description, amount) VALUES (?, ?, ?)");
        $stmt->execute([$id, $desc, $total]);
        
        $success = "Time logged and invoice generated.";
    }
    ?>

    <?php if ($success): ?>
        <div style="background-color: #d1fae5; color: #065f46; padding: 1rem; border-radius: 4px; margin-bottom: 1rem;">
            <?= htmlspecialchars($success) ?>
        </div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div style="background-color: #fee2e2; color: #991b1b; padding: 1rem; border-radius: 4px; margin-bottom: 1rem;">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <div class="grid-2">
        <div class="card">
            <h4 style="margin-top: 0; color: var(--admin-primary); margin-bottom: 1rem;">Share Document with Client</h4>
            <form method="POST" action="clients.php?action=view&id=<?= $id ?>" enctype="multipart/form-data">
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Document Title</label>
                    <input type="text" name="title" required style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 4px; font-family: inherit;">
                </div>
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">File (PDF, DOCX, JPG)</label>
                    <input type="file" name="document" required style="width: 100%; padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 4px; font-family: inherit;">
                </div>
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: flex; align-items: center; gap: 0.5rem; font-weight: 500;">
                        <input type="checkbox" name="requires_signature" value="1">
                        Requires E-Signature (Digital Sign)
                    </label>
                </div>
                <button type="submit" style="padding: 0.75rem 1.5rem; background-color: var(--admin-success); color: white; border: none; border-radius: 4px; font-weight: 600; cursor: pointer;">Upload & Share</button>
            </form>
        </div>

        <div class="card">
            <h4 style="margin-top: 0; color: var(--admin-primary); margin-bottom: 1rem;">Client Documents</h4>
            <ul style="list-style: none; padding: 0; margin: 0;">
                <?php
                $stmt = $pdo->prepare("SELECT * FROM client_documents WHERE client_id = ? ORDER BY uploaded_at DESC");
                $stmt->execute([$id]);
                $docs = $stmt->fetchAll();
                if (count($docs) > 0): foreach ($docs as $doc):
                ?>
                <li style="padding: 1rem; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <strong><?= htmlspecialchars($doc['title']) ?></strong>
                        <div style="font-size: 0.8rem; color: #64748b; margin-top: 0.25rem;">
                            Uploaded by <?= $doc['uploaded_by'] === 'admin' ? 'You' : 'Client' ?> on <?= date('M j, Y', strtotime($doc['uploaded_at'])) ?>
                        </div>
                        <?php if ($doc['requires_signature']): ?>
                            <div style="margin-top: 0.25rem;">
                                <?php if ($doc['is_signed']): ?>
                                    <span style="background-color: #d1fae5; color: #065f46; padding: 0.15rem 0.4rem; border-radius: 4px; font-size: 0.7rem; font-weight: bold;"><i class="fas fa-check-circle"></i> Signed</span>
                                <?php else: ?>
                                    <span style="background-color: #fee2e2; color: #991b1b; padding: 0.15rem 0.4rem; border-radius: 4px; font-size: 0.7rem; font-weight: bold;"><i class="fas fa-clock"></i> Pending Signature</span>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div>
                        <a href="../<?= htmlspecialchars($doc['file_path']) ?>" target="_blank" class="btn-sm" style="background-color: var(--admin-primary); color: white; text-decoration: none; border-radius: 4px; display: inline-block; margin-right: 5px;" title="View"><i class="fas fa-download"></i></a>
                        <a href="clients.php?action=view&id=<?= $id ?>&del_doc=<?= $doc['id'] ?>" class="btn-sm btn-danger" style="text-decoration: none; border-radius: 4px; display: inline-block;" onclick="return confirm('Delete document?');" title="Delete"><i class="fas fa-trash"></i></a>
                    </div>
                </li>
                <?php endforeach; else: ?>
                <li style="padding: 1rem; color: #64748b;">No documents found.</li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
    
    <div class="card" style="margin-top: 1.5rem;">
        <h4 style="margin-top: 0; color: var(--admin-primary); margin-bottom: 1rem;"><i class="fas fa-stopwatch"></i> Billable Hours Tracker</h4>
        <div style="background-color: #f1f5f9; padding: 1.5rem; border-radius: 4px; border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 2rem;">
            <div style="text-align: center;">
                <div id="stopwatchDisplay" style="font-size: 2.5rem; font-family: monospace; font-weight: bold; color: var(--admin-primary);">00:00:00</div>
                <div style="display: flex; gap: 0.5rem; justify-content: center; margin-top: 0.5rem;">
                    <button type="button" id="startBtn" class="btn btn-primary" style="background-color: #10b981; border: none; padding: 0.5rem 1rem;"><i class="fas fa-play"></i> Start</button>
                    <button type="button" id="stopBtn" class="btn btn-primary" style="background-color: #ef4444; border: none; padding: 0.5rem 1rem;" disabled><i class="fas fa-stop"></i> Stop</button>
                </div>
            </div>
            <div style="flex: 1;">
                <form method="POST" action="clients.php?action=view&id=<?= $id ?>" id="timeLogForm" style="display: flex; gap: 1rem; align-items: flex-end;">
                    <div style="flex: 1;">
                        <label style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 500;">Minutes Logged</label>
                        <input type="number" name="duration_minutes" id="durationMinutes" value="0" required style="width: 100%; padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 4px;" readonly>
                    </div>
                    <div style="flex: 1;">
                        <label style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 500;">Hourly Rate ($)</label>
                        <input type="number" name="hourly_rate" value="350" step="1" required style="width: 100%; padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 4px;">
                    </div>
                    <input type="hidden" name="log_time" value="1">
                    <button type="submit" id="logTimeBtn" class="btn btn-primary" style="padding: 0.5rem 1.5rem; background-color: var(--admin-primary); border: none; border-radius: 4px;" disabled>Log & Invoice</button>
                </form>
            </div>
        </div>
    </div>
    
    <script>
    let timerInterval;
    let seconds = 0;
    
    const display = document.getElementById('stopwatchDisplay');
    const startBtn = document.getElementById('startBtn');
    const stopBtn = document.getElementById('stopBtn');
    const minsInput = document.getElementById('durationMinutes');
    const logBtn = document.getElementById('logTimeBtn');
    
    function updateDisplay() {
        const h = Math.floor(seconds / 3600).toString().padStart(2, '0');
        const m = Math.floor((seconds % 3600) / 60).toString().padStart(2, '0');
        const s = (seconds % 60).toString().padStart(2, '0');
        display.textContent = `${h}:${m}:${s}`;
    }
    
    startBtn.addEventListener('click', () => {
        startBtn.disabled = true;
        stopBtn.disabled = false;
        logBtn.disabled = true;
        timerInterval = setInterval(() => {
            seconds++;
            updateDisplay();
        }, 1000);
    });
    
    stopBtn.addEventListener('click', () => {
        clearInterval(timerInterval);
        startBtn.disabled = false;
        stopBtn.disabled = true;
        logBtn.disabled = false;
        // Calculate minutes rounded up
        minsInput.value = Math.ceil(seconds / 60) || 1;
    });
    </script>

    <div class="card" style="margin-top: 1.5rem;">
        <h4 style="margin-top: 0; color: var(--admin-primary); margin-bottom: 1rem;">Client Invoices</h4>
        
        <div style="margin-bottom: 2rem; background-color: #f8fafc; padding: 1.5rem; border-radius: 4px; border: 1px solid #e2e8f0;">
            <h5 style="margin-top: 0; margin-bottom: 1rem;">Generate Custom Invoice</h5>
            <form method="POST" action="clients.php?action=view&id=<?= $id ?>" style="display: flex; gap: 1rem; align-items: flex-end;">
                <div style="flex: 2;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Invoice Description</label>
                    <input type="text" name="invoice_desc" required placeholder="e.g., Legal Retainer Fee" style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 4px; font-family: inherit;">
                </div>
                <div style="flex: 1;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Amount ($)</label>
                    <input type="number" step="0.01" name="invoice_amount" required style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 4px; font-family: inherit;">
                </div>
                <div style="flex: 1;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Type</label>
                    <select name="is_subscription" style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 4px;">
                        <option value="0">One-time Invoice</option>
                        <option value="1">Monthly Retainer</option>
                    </select>
                </div>
                <div>
                    <button type="submit" style="padding: 0.75rem 1.5rem; background-color: #3b82f6; color: white; border: none; border-radius: 4px; font-weight: 600; cursor: pointer;">Send Invoice</button>
                </div>
            </form>
        </div>

        <ul style="list-style: none; padding: 0; margin: 0;">
            <?php
            $stmt = $pdo->prepare("SELECT * FROM invoices WHERE client_id = ? ORDER BY created_at DESC");
            $stmt->execute([$id]);
            $invoices = $stmt->fetchAll();
            if (count($invoices) > 0): foreach ($invoices as $inv):
            ?>
            <li style="padding: 1rem; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <strong><?= htmlspecialchars($inv['description']) ?></strong>
                    <div style="font-size: 0.8rem; color: #64748b; margin-top: 0.25rem;">
                        Date: <?= date('M j, Y', strtotime($inv['created_at'])) ?>
                    </div>
                </div>
                <div>
                    <span style="font-weight: bold; margin-right: 1rem; font-size: 1.1rem;">$<?= number_format($inv['amount'], 2) ?></span>
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
                        <span style="background-color: #d1fae5; color: #065f46; padding: 0.25rem 0.5rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600;">Paid<?= $gateway_badge ?></span>
                    <?php else: ?>
                        <span style="background-color: #fef3c7; color: #d97706; padding: 0.25rem 0.5rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600;">Pending</span>
                    <?php endif; ?>
                </div>
            </li>
            <?php endforeach; else: ?>
            <li style="padding: 1rem; color: #64748b;">No invoices found.</li>
            <?php endif; ?>
        </ul>
    </div>

<?php else: ?>
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h3 style="margin: 0; color: var(--admin-primary);">Client Portal Accounts</h3>
        <a href="clients.php?action=add" style="padding: 0.5rem 1rem; background-color: var(--admin-success); color: white; text-decoration: none; border-radius: 4px; font-weight: 500;"><i class="fas fa-plus"></i> New Client</a>
    </div>

    <?php if ($success): ?>
        <div style="background-color: #d1fae5; color: #065f46; padding: 1rem; border-radius: 4px; margin-bottom: 1rem;">
            <?= htmlspecialchars($success) ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Client Name</th>
                        <th>Email</th>
                        <th>Registered</th>
                        <th style="width: 150px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stmt = $pdo->query("SELECT * FROM clients ORDER BY created_at DESC");
                    $clients = $stmt->fetchAll();
                    if (count($clients) > 0): foreach ($clients as $c):
                    ?>
                    <tr>
                        <td style="font-weight: 500;"><?= htmlspecialchars($c['name']) ?></td>
                        <td><?= htmlspecialchars($c['email']) ?></td>
                        <td><?= date('M j, Y', strtotime($c['created_at'])) ?></td>
                        <td>
                            <a href="clients.php?action=view&id=<?= $c['id'] ?>" class="btn-sm" style="background-color: var(--admin-success); color: white; text-decoration: none; border-radius: 4px; display: inline-block; margin-right: 5px;" title="View Documents"><i class="fas fa-folder-open"></i></a>
                            <a href="clients.php?action=edit&id=<?= $c['id'] ?>" class="btn-sm" style="background-color: var(--admin-accent); color: white; text-decoration: none; border-radius: 4px; display: inline-block; margin-right: 5px;"><i class="fas fa-edit"></i></a>
                            <a href="clients.php?action=delete&id=<?= $c['id'] ?>" class="btn-sm btn-danger" style="text-decoration: none; border-radius: 4px; display: inline-block;" onclick="return confirm('Delete client and all their documents?');"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 2rem;">No clients found.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

<?php endif; ?>

<?php require_once 'includes/admin_footer.php'; ?>
