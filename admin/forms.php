<?php
require_once 'includes/admin_header.php';

if (!isset($_SESSION['admin_role']) || $_SESSION['admin_role'] !== 'superadmin') {
    die("Unauthorized");
}

$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'create') {
        $title = trim($_POST['title']);
        $fields = $_POST['fields'] ?? [];
        $fields_json = json_encode($fields);
        if ($title && !empty($fields)) {
            $stmt = $pdo->prepare("INSERT INTO custom_forms (title, fields_json) VALUES (?, ?)");
            $stmt->execute([$title, $fields_json]);
            $success = "Form created successfully.";
        }
    } elseif (isset($_POST['action']) && $_POST['action'] === 'delete') {
        $id = (int)$_POST['id'];
        $pdo->prepare("DELETE FROM custom_forms WHERE id = ?")->execute([$id]);
        $success = "Form deleted.";
    }
}

$forms = $pdo->query("SELECT * FROM custom_forms ORDER BY created_at DESC")->fetchAll();
?>

<div style="display: flex; gap: 2rem;">
    <div class="card" style="flex: 1;">
        <h3 style="margin-top: 0; color: var(--admin-primary);">Create New Intake Form</h3>
        <?php if ($success): ?><div style="background: #d1fae5; color: #065f46; padding: 1rem; margin-bottom: 1rem; border-radius: 4px;"><?= $success ?></div><?php endif; ?>
        
        <form method="POST">
            <input type="hidden" name="action" value="create">
            <div style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Form Title</label>
                <input type="text" name="title" required placeholder="e.g. Divorce Intake Questionnaire" style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 4px;">
            </div>
            
            <div id="fieldsContainer">
                <div style="display: flex; gap: 10px; margin-bottom: 10px;">
                    <input type="text" name="fields[]" placeholder="Field Label (e.g. Date of Marriage)" required style="flex: 1; padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 4px;">
                </div>
            </div>
            
            <button type="button" onclick="addField()" class="btn" style="background: #e2e8f0; color: #334155; margin-bottom: 1rem; padding: 0.5rem 1rem; border-radius: 4px; border: none; cursor: pointer;">+ Add Another Field</button>
            <br>
            <button type="submit" class="btn btn-primary" style="background: var(--admin-accent); color: white; padding: 0.75rem 1.5rem; border: none; border-radius: 4px; cursor: pointer;">Save Form</button>
        </form>
    </div>
    
    <div class="card" style="flex: 1;">
        <h3 style="margin-top: 0; color: var(--admin-primary);">Existing Forms</h3>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 1px solid #e2e8f0; text-align: left;">
                    <th style="padding: 0.5rem;">Title</th>
                    <th style="padding: 0.5rem;">Fields</th>
                    <th style="padding: 0.5rem;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($forms as $form): ?>
                <tr style="border-bottom: 1px solid #e2e8f0;">
                    <td style="padding: 0.5rem;"><?= htmlspecialchars($form['title']) ?></td>
                    <td style="padding: 0.5rem;"><?= count(json_decode($form['fields_json'], true)) ?> fields</td>
                    <td style="padding: 0.5rem;">
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= $form['id'] ?>">
                            <button class="btn btn-danger" style="background: #ef4444; color: white; border: none; padding: 0.25rem 0.5rem; border-radius: 4px; cursor: pointer;">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function addField() {
    const container = document.getElementById('fieldsContainer');
    const div = document.createElement('div');
    div.style.display = 'flex';
    div.style.gap = '10px';
    div.style.marginBottom = '10px';
    div.innerHTML = `
        <input type="text" name="fields[]" placeholder="Field Label" required style="flex: 1; padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 4px;">
        <button type="button" onclick="this.parentElement.remove()" style="background: #ef4444; color: white; border: none; padding: 0.5rem 1rem; border-radius: 4px; cursor: pointer;"><i class="fas fa-times"></i></button>
    `;
    container.appendChild(div);
}
</script>

<?php require_once 'includes/admin_footer.php'; ?>
