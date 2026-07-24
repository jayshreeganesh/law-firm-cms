<?php
require_once 'includes/admin_header.php';

$action = $_GET['action'] ?? 'list';
$error = '';
$success = '';

if ($action === 'delete' && isset($_GET['id'])) {
    $stmt = $pdo->prepare("DELETE FROM case_results WHERE id = ?");
    if ($stmt->execute([(int)$_GET['id']])) {
        header("Location: case_results.php?msg=deleted");
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $amount = trim($_POST['amount'] ?? '');
    $case_type = trim($_POST['case_type'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if ($title && $amount && $case_type) {
        if (isset($_POST['id']) && !empty($_POST['id'])) {
            $id = (int)$_POST['id'];
            $stmt = $pdo->prepare("UPDATE case_results SET title = ?, amount = ?, case_type = ?, description = ? WHERE id = ?");
            $stmt->execute([$title, $amount, $case_type, $description, $id]);
            $success = "Case result updated.";
            $action = 'list';
        } else {
            $stmt = $pdo->prepare("INSERT INTO case_results (title, amount, case_type, description) VALUES (?, ?, ?, ?)");
            $stmt->execute([$title, $amount, $case_type, $description]);
            $success = "Case result added.";
            $action = 'list';
        }
    } else {
        $error = "Title, Amount, and Case Type are required.";
    }
}

if (isset($_GET['msg']) && $_GET['msg'] === 'deleted') {
    $success = "Case result deleted.";
}
?>

<?php if ($action === 'add' || $action === 'edit'): ?>
    <?php
    $item = ['id' => '', 'title' => '', 'amount' => '', 'case_type' => '', 'description' => ''];
    if ($action === 'edit' && isset($_GET['id'])) {
        $stmt = $pdo->prepare("SELECT * FROM case_results WHERE id = ?");
        $stmt->execute([(int)$_GET['id']]);
        $fetched = $stmt->fetch();
        if ($fetched) $item = $fetched;
    }
    ?>
    <div class="card" style="max-width: 600px;">
        <h3 style="margin-top: 0; color: var(--admin-primary);"><?= $action === 'add' ? 'Add' : 'Edit' ?> Case Result</h3>
        
        <?php if ($error): ?>
            <div style="background-color: #fee2e2; color: #991b1b; padding: 1rem; border-radius: 4px; margin-bottom: 1rem;">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="case_results.php?action=<?= $action ?>">
            <?php if ($item['id']): ?>
                <input type="hidden" name="id" value="<?= $item['id'] ?>">
            <?php endif; ?>
            
            <div style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Title * (e.g., Jane Doe v. Corporation)</label>
                <input type="text" name="title" value="<?= htmlspecialchars($item['title']) ?>" required style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 4px; font-family: inherit;">
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Amount Won * (e.g., $5.2 Million)</label>
                    <input type="text" name="amount" value="<?= htmlspecialchars($item['amount']) ?>" required style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 4px; font-family: inherit;">
                </div>
                
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Case Type * (e.g., Personal Injury)</label>
                    <input type="text" name="case_type" value="<?= htmlspecialchars($item['case_type']) ?>" required style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 4px; font-family: inherit;">
                </div>
            </div>
            
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Short Description (Client Testimonial or Case Summary)</label>
                <textarea name="description" rows="4" style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 4px; font-family: inherit; resize: vertical;"><?= htmlspecialchars($item['description']) ?></textarea>
            </div>
            
            <div>
                <button type="submit" style="padding: 0.75rem 1.5rem; background-color: var(--admin-accent); color: white; border: none; border-radius: 4px; font-weight: 600; cursor: pointer;">Save Result</button>
                <a href="case_results.php" style="padding: 0.75rem 1.5rem; background-color: #e2e8f0; color: #475569; text-decoration: none; border-radius: 4px; margin-left: 0.5rem; font-weight: 500; display: inline-block;">Cancel</a>
            </div>
        </form>
    </div>

<?php else: ?>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h3 style="margin: 0; color: var(--admin-primary);">Our Victories (Case Results)</h3>
        <a href="case_results.php?action=add" style="padding: 0.5rem 1rem; background-color: var(--admin-success); color: white; text-decoration: none; border-radius: 4px; font-weight: 500;"><i class="fas fa-plus"></i> Add Case Result</a>
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
                        <th>Title</th>
                        <th>Amount</th>
                        <th>Case Type</th>
                        <th style="width: 120px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stmt = $pdo->query("SELECT * FROM case_results ORDER BY created_at DESC");
                    $results = $stmt->fetchAll();
                    if (count($results) > 0): foreach ($results as $res):
                    ?>
                    <tr>
                        <td style="font-weight: 500;"><?= htmlspecialchars($res['title']) ?></td>
                        <td style="color: var(--admin-success); font-weight: bold;"><?= htmlspecialchars($res['amount']) ?></td>
                        <td><?= htmlspecialchars($res['case_type']) ?></td>
                        <td>
                            <a href="case_results.php?action=edit&id=<?= $res['id'] ?>" class="btn-sm" style="background-color: var(--admin-accent); color: white; text-decoration: none; border-radius: 4px; display: inline-block; margin-right: 5px;"><i class="fas fa-edit"></i></a>
                            <a href="case_results.php?action=delete&id=<?= $res['id'] ?>" class="btn-sm btn-danger" style="text-decoration: none; border-radius: 4px; display: inline-block;" onclick="return confirm('Delete this result?');"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 2rem;">No case results found.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<?php require_once 'includes/admin_footer.php'; ?>
