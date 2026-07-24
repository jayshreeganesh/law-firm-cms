<?php
require_once 'includes/admin_header.php';

$action = $_GET['action'] ?? 'list';
$error = '';
$success = '';

// Handle Delete
if ($action === 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare("DELETE FROM practice_areas WHERE id = ?");
    if ($stmt->execute([$id])) {
        header("Location: practice_areas.php?msg=deleted");
        exit;
    }
}

// Handle Add/Edit Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $icon = trim($_POST['icon'] ?? 'fas fa-balance-scale');

    if ($title && $description) {
        if (isset($_POST['id']) && !empty($_POST['id'])) {
            // Update
            $id = (int)$_POST['id'];
            $stmt = $pdo->prepare("UPDATE practice_areas SET title = ?, description = ?, icon = ? WHERE id = ?");
            $stmt->execute([$title, $description, $icon, $id]);
            $success = "Practice area updated successfully.";
            $action = 'list';
        } else {
            // Insert
            $stmt = $pdo->prepare("INSERT INTO practice_areas (title, description, icon) VALUES (?, ?, ?)");
            $stmt->execute([$title, $description, $icon]);
            $success = "Practice area added successfully.";
            $action = 'list';
        }
    } else {
        $error = "Title and Description are required.";
    }
}

// Display messages
if (isset($_GET['msg']) && $_GET['msg'] === 'deleted') {
    $success = "Practice area deleted successfully.";
}

?>

<?php if ($action === 'add' || $action === 'edit'): ?>
    <?php
    $item = ['id' => '', 'title' => '', 'description' => '', 'icon' => 'fas fa-balance-scale'];
    if ($action === 'edit' && isset($_GET['id'])) {
        $stmt = $pdo->prepare("SELECT * FROM practice_areas WHERE id = ?");
        $stmt->execute([(int)$_GET['id']]);
        $fetched = $stmt->fetch();
        if ($fetched) $item = $fetched;
    }
    ?>
    <div class="card" style="max-width: 600px;">
        <h3 style="margin-top: 0; color: var(--admin-primary);"><?= $action === 'add' ? 'Add' : 'Edit' ?> Practice Area</h3>
        
        <?php if ($error): ?>
            <div style="background-color: #fee2e2; color: #991b1b; padding: 1rem; border-radius: 4px; margin-bottom: 1rem;">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="practice_areas.php?action=<?= $action ?>">
            <?php if ($item['id']): ?>
                <input type="hidden" name="id" value="<?= $item['id'] ?>">
            <?php endif; ?>
            
            <div style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Title *</label>
                <input type="text" name="title" value="<?= htmlspecialchars($item['title']) ?>" required style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 4px; font-family: inherit;">
            </div>
            
            <div style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Icon Class (FontAwesome)</label>
                <input type="text" name="icon" value="<?= htmlspecialchars($item['icon']) ?>" style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 4px; font-family: inherit;">
                <small style="color: #64748b;">Example: fas fa-balance-scale, fas fa-gavel</small>
            </div>
            
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Description *</label>
                <textarea name="description" rows="5" required style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 4px; font-family: inherit; resize: vertical;"><?= htmlspecialchars($item['description']) ?></textarea>
            </div>
            
            <div>
                <button type="submit" style="padding: 0.75rem 1.5rem; background-color: var(--admin-accent); color: white; border: none; border-radius: 4px; font-weight: 600; cursor: pointer;">Save</button>
                <a href="practice_areas.php" style="padding: 0.75rem 1.5rem; background-color: #e2e8f0; color: #475569; text-decoration: none; border-radius: 4px; margin-left: 0.5rem; font-weight: 500; display: inline-block;">Cancel</a>
            </div>
        </form>
    </div>

<?php else: ?>
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h3 style="margin: 0; color: var(--admin-primary);">Practice Areas</h3>
        <a href="practice_areas.php?action=add" style="padding: 0.5rem 1rem; background-color: var(--admin-success); color: white; text-decoration: none; border-radius: 4px; font-weight: 500;"><i class="fas fa-plus"></i> Add New</a>
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
                        <th>Icon</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th style="width: 120px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stmt = $pdo->query("SELECT * FROM practice_areas ORDER BY id DESC");
                    $areas = $stmt->fetchAll();
                    if (count($areas) > 0): foreach ($areas as $area):
                    ?>
                    <tr>
                        <td><i class="<?= htmlspecialchars($area['icon']) ?>" style="font-size: 1.5rem; color: var(--admin-primary);"></i></td>
                        <td style="font-weight: 500;"><?= htmlspecialchars($area['title']) ?></td>
                        <td><?= htmlspecialchars(substr($area['description'], 0, 80)) ?>...</td>
                        <td>
                            <a href="practice_areas.php?action=edit&id=<?= $area['id'] ?>" class="btn-sm" style="background-color: var(--admin-accent); color: white; text-decoration: none; border-radius: 4px; display: inline-block; margin-right: 5px;"><i class="fas fa-edit"></i></a>
                            <a href="practice_areas.php?action=delete&id=<?= $area['id'] ?>" class="btn-sm btn-danger" style="text-decoration: none; border-radius: 4px; display: inline-block;" onclick="return confirm('Are you sure you want to delete this practice area?');"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 2rem;">No practice areas found.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

<?php endif; ?>

<?php require_once 'includes/admin_footer.php'; ?>
