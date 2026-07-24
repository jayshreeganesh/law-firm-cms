<?php
require_once 'includes/admin_header.php';

$action = $_GET['action'] ?? 'list';
$error = '';
$success = '';

// Handle Delete
if ($action === 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare("DELETE FROM attorneys WHERE id = ?");
    if ($stmt->execute([$id])) {
        header("Location: attorneys.php?msg=deleted");
        exit;
    }
}

// Handle Add/Edit Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $position = trim($_POST['position'] ?? '');
    $bio = trim($_POST['bio'] ?? '');
    $image = trim($_POST['image'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    if ($name && $position && $bio) {
        if (isset($_POST['id']) && !empty($_POST['id'])) {
            // Update
            $id = (int)$_POST['id'];
            $stmt = $pdo->prepare("UPDATE attorneys SET name = ?, position = ?, bio = ?, image = ?, email = ?, phone = ? WHERE id = ?");
            $stmt->execute([$name, $position, $bio, $image, $email, $phone, $id]);
            $success = "Attorney profile updated successfully.";
            $action = 'list';
        } else {
            // Insert
            $stmt = $pdo->prepare("INSERT INTO attorneys (name, position, bio, image, email, phone) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $position, $bio, $image, $email, $phone]);
            $success = "Attorney added successfully.";
            $action = 'list';
        }
    } else {
        $error = "Name, Position, and Bio are required fields.";
    }
}

// Display messages
if (isset($_GET['msg']) && $_GET['msg'] === 'deleted') {
    $success = "Attorney deleted successfully.";
}

?>

<?php if ($action === 'add' || $action === 'edit'): ?>
    <?php
    $item = ['id' => '', 'name' => '', 'position' => '', 'bio' => '', 'image' => '', 'email' => '', 'phone' => ''];
    if ($action === 'edit' && isset($_GET['id'])) {
        $stmt = $pdo->prepare("SELECT * FROM attorneys WHERE id = ?");
        $stmt->execute([(int)$_GET['id']]);
        $fetched = $stmt->fetch();
        if ($fetched) $item = $fetched;
    }
    ?>
    <div class="card" style="max-width: 800px;">
        <h3 style="margin-top: 0; color: var(--admin-primary);"><?= $action === 'add' ? 'Add' : 'Edit' ?> Attorney</h3>
        
        <?php if ($error): ?>
            <div style="background-color: #fee2e2; color: #991b1b; padding: 1rem; border-radius: 4px; margin-bottom: 1rem;">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="attorneys.php?action=<?= $action ?>">
            <?php if ($item['id']): ?>
                <input type="hidden" name="id" value="<?= $item['id'] ?>">
            <?php endif; ?>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Full Name *</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($item['name']) ?>" required style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 4px; font-family: inherit;">
                </div>
                
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Position / Role *</label>
                    <input type="text" name="position" value="<?= htmlspecialchars($item['position']) ?>" required style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 4px; font-family: inherit;">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Email Address</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($item['email']) ?>" style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 4px; font-family: inherit;">
                </div>
                
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Phone Number</label>
                    <input type="text" name="phone" value="<?= htmlspecialchars($item['phone']) ?>" style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 4px; font-family: inherit;">
                </div>
            </div>
            
            <div style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Image URL</label>
                <input type="text" name="image" value="<?= htmlspecialchars($item['image']) ?>" style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 4px; font-family: inherit;">
                <small style="color: #64748b;">Provide a full URL to the image (e.g., https://example.com/image.jpg)</small>
            </div>
            
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Biography *</label>
                <textarea name="bio" rows="6" required style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 4px; font-family: inherit; resize: vertical;"><?= htmlspecialchars($item['bio']) ?></textarea>
            </div>
            
            <div>
                <button type="submit" style="padding: 0.75rem 1.5rem; background-color: var(--admin-accent); color: white; border: none; border-radius: 4px; font-weight: 600; cursor: pointer;">Save</button>
                <a href="attorneys.php" style="padding: 0.75rem 1.5rem; background-color: #e2e8f0; color: #475569; text-decoration: none; border-radius: 4px; margin-left: 0.5rem; font-weight: 500; display: inline-block;">Cancel</a>
            </div>
        </form>
    </div>

<?php else: ?>
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h3 style="margin: 0; color: var(--admin-primary);">Attorneys</h3>
        <a href="attorneys.php?action=add" style="padding: 0.5rem 1rem; background-color: var(--admin-success); color: white; text-decoration: none; border-radius: 4px; font-weight: 500;"><i class="fas fa-plus"></i> Add New</a>
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
                        <th>Photo</th>
                        <th>Name</th>
                        <th>Position</th>
                        <th>Contact</th>
                        <th style="width: 120px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stmt = $pdo->query("SELECT * FROM attorneys ORDER BY id DESC");
                    $attorneys = $stmt->fetchAll();
                    if (count($attorneys) > 0): foreach ($attorneys as $attorney):
                    ?>
                    <tr>
                        <td>
                            <?php if ($attorney['image']): ?>
                                <img src="<?= htmlspecialchars($attorney['image']) ?>" alt="Photo" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;">
                            <?php else: ?>
                                <div style="width: 50px; height: 50px; border-radius: 50%; background-color: #e2e8f0; display: flex; align-items: center; justify-content: center; color: #94a3b8;"><i class="fas fa-user"></i></div>
                            <?php endif; ?>
                        </td>
                        <td style="font-weight: 500;"><?= htmlspecialchars($attorney['name']) ?></td>
                        <td><?= htmlspecialchars($attorney['position']) ?></td>
                        <td>
                            <?php if ($attorney['email']): ?><a href="mailto:<?= htmlspecialchars($attorney['email']) ?>"><i class="fas fa-envelope"></i></a><?php endif; ?>
                            <?php if ($attorney['phone']): ?><a href="tel:<?= htmlspecialchars($attorney['phone']) ?>" style="margin-left: 10px;"><i class="fas fa-phone"></i></a><?php endif; ?>
                        </td>
                        <td>
                            <a href="attorneys.php?action=edit&id=<?= $attorney['id'] ?>" class="btn-sm" style="background-color: var(--admin-accent); color: white; text-decoration: none; border-radius: 4px; display: inline-block; margin-right: 5px;"><i class="fas fa-edit"></i></a>
                            <a href="attorneys.php?action=delete&id=<?= $attorney['id'] ?>" class="btn-sm btn-danger" style="text-decoration: none; border-radius: 4px; display: inline-block;" onclick="return confirm('Are you sure you want to delete this attorney?');"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 2rem;">No attorneys found.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

<?php endif; ?>

<?php require_once 'includes/admin_footer.php'; ?>
