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

    if ($name && $email) {
        if (isset($_POST['id']) && !empty($_POST['id'])) {
            $id = (int)$_POST['id'];
            if ($password) {
                $hash = password_hash($password, PASSWORD_BCRYPT);
                $stmt = $pdo->prepare("UPDATE clients SET name = ?, email = ?, password = ? WHERE id = ?");
                $stmt->execute([$name, $email, $hash, $id]);
            } else {
                $stmt = $pdo->prepare("UPDATE clients SET name = ?, email = ? WHERE id = ?");
                $stmt->execute([$name, $email, $id]);
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
                    $stmt = $pdo->prepare("INSERT INTO clients (name, email, password) VALUES (?, ?, ?)");
                    $stmt->execute([$name, $email, $hash]);
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
                <input type="email" name="email" value="<?= htmlspecialchars($item['email']) ?>" required style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 4px; font-family: inherit;">
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
            $stmt = $pdo->prepare("INSERT INTO client_documents (client_id, title, file_path, uploaded_by) VALUES (?, ?, ?, 'admin')");
            $stmt->execute([$id, $title, 'assets/uploads/clients/' . $fileName]);
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
        <a href="clients.php" class="btn btn-primary" style="padding: 0.5rem 1rem; background-color: #64748b; color: white; text-decoration: none; border-radius: 4px; font-weight: 500;">&larr; Back to Clients</a>
    </div>

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
