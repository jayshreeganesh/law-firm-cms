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
?>

<section class="section" style="background-color: var(--primary-color); padding: 40px 0; color: white;">
    <div class="container" style="display: flex; justify-content: space-between; align-items: center;">
        <h2 style="color: white; margin: 0;">Welcome, <?= htmlspecialchars($_SESSION['client_name']) ?></h2>
        <a href="client_logout.php" class="btn btn-primary" style="background-color: #64748b; border: none;">Logout</a>
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
                            <?php if ($doc['uploaded_by'] === 'client'): ?>
                                <a href="client_dashboard.php?del_doc=<?= $doc['id'] ?>" class="btn" style="background-color: #ef4444; color: white; padding: 0.4rem 0.8rem; font-size: 0.85rem;" onclick="return confirm('Delete this document permanently?');"><i class="fas fa-trash"></i></a>
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
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
