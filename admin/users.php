<?php
require_once 'includes/admin_header.php';

$action = $_GET['action'] ?? 'list';
$error = '';
$success = '';

// Handle Delete
if ($action === 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    if ($id !== $_SESSION['admin_id']) {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        if ($stmt->execute([$id])) {
            header("Location: users.php?msg=deleted");
            exit;
        }
    } else {
        $error = "You cannot delete your own account.";
        $action = 'list';
    }
}

// Handle Add/Edit Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username && $email) {
        if (isset($_POST['id']) && !empty($_POST['id'])) {
            // Update
            $id = (int)$_POST['id'];
            if ($password) {
                $hash = password_hash($password, PASSWORD_BCRYPT);
                $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, password = ? WHERE id = ?");
                $stmt->execute([$username, $email, $hash, $id]);
            } else {
                $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
                $stmt->execute([$username, $email, $id]);
            }
            $success = "User updated successfully.";
            $action = 'list';
        } else {
            // Insert
            if ($password) {
                // Check if username exists
                $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
                $stmt->execute([$username]);
                if ($stmt->fetch()) {
                    $error = "Username already exists.";
                } else {
                    $hash = password_hash($password, PASSWORD_BCRYPT);
                    $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
                    $stmt->execute([$username, $email, $hash]);
                    $success = "User created successfully.";
                    $action = 'list';
                }
            } else {
                $error = "Password is required for new users.";
            }
        }
    } else {
        $error = "Username and Email are required fields.";
    }
}

if (isset($_GET['msg']) && $_GET['msg'] === 'deleted') {
    $success = "User deleted successfully.";
}
?>

<?php if ($action === 'add' || $action === 'edit'): ?>
    <?php
    $item = ['id' => '', 'username' => '', 'email' => ''];
    if ($action === 'edit' && isset($_GET['id'])) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([(int)$_GET['id']]);
        $fetched = $stmt->fetch();
        if ($fetched) $item = $fetched;
    }
    ?>
    <div class="card" style="max-width: 600px;">
        <h3 style="margin-top: 0; color: var(--admin-primary);"><?= $action === 'add' ? 'Add' : 'Edit' ?> User</h3>
        
        <?php if ($error): ?>
            <div style="background-color: #fee2e2; color: #991b1b; padding: 1rem; border-radius: 4px; margin-bottom: 1rem;">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="users.php?action=<?= $action ?>">
            <?php if ($item['id']): ?>
                <input type="hidden" name="id" value="<?= $item['id'] ?>">
            <?php endif; ?>
            
            <div style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Username *</label>
                <input type="text" name="username" value="<?= htmlspecialchars($item['username']) ?>" required style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 4px; font-family: inherit;">
            </div>
            
            <div style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Email Address *</label>
                <input type="email" name="email" value="<?= htmlspecialchars($item['email']) ?>" required style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 4px; font-family: inherit;">
            </div>
            
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Password <?= $action === 'edit' ? '(leave blank to keep current)' : '*' ?></label>
                <input type="password" name="password" <?= $action === 'add' ? 'required' : '' ?> style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 4px; font-family: inherit;">
            </div>
            
            <div>
                <button type="submit" style="padding: 0.75rem 1.5rem; background-color: var(--admin-accent); color: white; border: none; border-radius: 4px; font-weight: 600; cursor: pointer;">Save User</button>
                <a href="users.php" style="padding: 0.75rem 1.5rem; background-color: #e2e8f0; color: #475569; text-decoration: none; border-radius: 4px; margin-left: 0.5rem; font-weight: 500; display: inline-block;">Cancel</a>
            </div>
        </form>
    </div>

<?php else: ?>
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h3 style="margin: 0; color: var(--admin-primary);">Admin Users</h3>
        <a href="users.php?action=add" style="padding: 0.5rem 1rem; background-color: var(--admin-success); color: white; text-decoration: none; border-radius: 4px; font-weight: 500;"><i class="fas fa-plus"></i> Add New User</a>
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

    <div class="card">
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Registered</th>
                        <th style="width: 120px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stmt = $pdo->query("SELECT * FROM users ORDER BY id ASC");
                    $users = $stmt->fetchAll();
                    if (count($users) > 0): foreach ($users as $user):
                    ?>
                    <tr>
                        <td style="font-weight: 500;">
                            <?= htmlspecialchars($user['username']) ?>
                            <?php if ($user['id'] === $_SESSION['admin_id']): ?>
                                <span style="font-size: 0.75rem; background-color: #e2e8f0; padding: 2px 6px; border-radius: 10px; margin-left: 5px;">You</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><?= date('M j, Y', strtotime($user['created_at'])) ?></td>
                        <td>
                            <a href="users.php?action=edit&id=<?= $user['id'] ?>" class="btn-sm" style="background-color: var(--admin-accent); color: white; text-decoration: none; border-radius: 4px; display: inline-block; margin-right: 5px;"><i class="fas fa-edit"></i></a>
                            <?php if ($user['id'] !== $_SESSION['admin_id']): ?>
                            <a href="users.php?action=delete&id=<?= $user['id'] ?>" class="btn-sm btn-danger" style="text-decoration: none; border-radius: 4px; display: inline-block;" onclick="return confirm('Are you sure you want to delete this user?');"><i class="fas fa-trash"></i></a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 2rem;">No users found.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

<?php endif; ?>

<?php require_once 'includes/admin_footer.php'; ?>
