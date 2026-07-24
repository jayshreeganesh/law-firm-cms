<?php
require_once 'includes/admin_header.php';

$action = $_GET['action'] ?? 'list';
$error = '';
$success = '';

// Handle Delete
if ($action === 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    // get image path to delete it
    $stmt = $pdo->prepare("SELECT image FROM posts WHERE id = ?");
    $stmt->execute([$id]);
    $post = $stmt->fetch();
    if ($post && $post['image'] && file_exists('../' . $post['image'])) {
        unlink('../' . $post['image']);
    }
    
    $stmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
    if ($stmt->execute([$id])) {
        header("Location: posts.php?msg=deleted");
        exit;
    }
}

// Handle Add/Edit Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    
    // Image Upload Logic
    $imagePath = $_POST['current_image'] ?? '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../assets/uploads/posts/';
        $fileName = time() . '_' . basename($_FILES['image']['name']);
        $targetFile = $uploadDir . $fileName;
        
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (in_array($imageFileType, $allowedTypes)) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                $imagePath = 'assets/uploads/posts/' . $fileName;
                // Delete old image if exists
                if (!empty($_POST['current_image']) && file_exists('../' . $_POST['current_image'])) {
                    unlink('../' . $_POST['current_image']);
                }
            } else {
                $error = "Sorry, there was an error uploading your file.";
            }
        } else {
            $error = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        }
    }

    if ($title && $content && !$error) {
        if (isset($_POST['id']) && !empty($_POST['id'])) {
            $id = (int)$_POST['id'];
            $stmt = $pdo->prepare("UPDATE posts SET title = ?, content = ?, image = ? WHERE id = ?");
            $stmt->execute([$title, $content, $imagePath, $id]);
            $success = "Post updated successfully.";
            $action = 'list';
        } else {
            $stmt = $pdo->prepare("INSERT INTO posts (title, content, image) VALUES (?, ?, ?)");
            $stmt->execute([$title, $content, $imagePath]);
            $success = "Post created successfully.";
            $action = 'list';
        }
    } elseif (!$error) {
        $error = "Title and Content are required fields.";
    }
}

if (isset($_GET['msg']) && $_GET['msg'] === 'deleted') {
    $success = "Post deleted successfully.";
}
?>

<?php if ($action === 'add' || $action === 'edit'): ?>
    <?php
    $item = ['id' => '', 'title' => '', 'content' => '', 'image' => ''];
    if ($action === 'edit' && isset($_GET['id'])) {
        $stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
        $stmt->execute([(int)$_GET['id']]);
        $fetched = $stmt->fetch();
        if ($fetched) $item = $fetched;
    }
    ?>
    <div class="card" style="max-width: 800px;">
        <h3 style="margin-top: 0; color: var(--admin-primary);"><?= $action === 'add' ? 'Create' : 'Edit' ?> Post</h3>
        
        <?php if ($error): ?>
            <div style="background-color: #fee2e2; color: #991b1b; padding: 1rem; border-radius: 4px; margin-bottom: 1rem;">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="posts.php?action=<?= $action ?>" enctype="multipart/form-data">
            <?php if ($item['id']): ?>
                <input type="hidden" name="id" value="<?= $item['id'] ?>">
            <?php endif; ?>
            <input type="hidden" name="current_image" value="<?= htmlspecialchars($item['image']) ?>">
            
            <div style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Title *</label>
                <input type="text" name="title" value="<?= htmlspecialchars($item['title']) ?>" required style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 4px; font-family: inherit;">
            </div>
            
            <div style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Featured Image</label>
                <?php if ($item['image']): ?>
                    <div style="margin-bottom: 10px;">
                        <img src="../<?= htmlspecialchars($item['image']) ?>" alt="Current Image" style="max-width: 200px; border-radius: 4px;">
                    </div>
                <?php endif; ?>
                <input type="file" name="image" accept="image/*" style="width: 100%; padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 4px; font-family: inherit;">
            </div>
            
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Content *</label>
                <textarea name="content" rows="10" required style="width: 100%; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 4px; font-family: inherit; resize: vertical;"><?= htmlspecialchars($item['content']) ?></textarea>
            </div>
            
            <div>
                <button type="submit" style="padding: 0.75rem 1.5rem; background-color: var(--admin-accent); color: white; border: none; border-radius: 4px; font-weight: 600; cursor: pointer;">Save Post</button>
                <a href="posts.php" style="padding: 0.75rem 1.5rem; background-color: #e2e8f0; color: #475569; text-decoration: none; border-radius: 4px; margin-left: 0.5rem; font-weight: 500; display: inline-block;">Cancel</a>
            </div>
        </form>
    </div>

<?php else: ?>
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h3 style="margin: 0; color: var(--admin-primary);">Blog Posts</h3>
        <a href="posts.php?action=add" style="padding: 0.5rem 1rem; background-color: var(--admin-success); color: white; text-decoration: none; border-radius: 4px; font-weight: 500;"><i class="fas fa-plus"></i> Add New Post</a>
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
                        <th>Date</th>
                        <th>Title</th>
                        <th style="width: 120px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stmt = $pdo->query("SELECT * FROM posts ORDER BY created_at DESC");
                    $posts = $stmt->fetchAll();
                    if (count($posts) > 0): foreach ($posts as $post):
                    ?>
                    <tr>
                        <td><?= date('M j, Y', strtotime($post['created_at'])) ?></td>
                        <td style="font-weight: 500;"><?= htmlspecialchars($post['title']) ?></td>
                        <td>
                            <a href="posts.php?action=edit&id=<?= $post['id'] ?>" class="btn-sm" style="background-color: var(--admin-accent); color: white; text-decoration: none; border-radius: 4px; display: inline-block; margin-right: 5px;"><i class="fas fa-edit"></i></a>
                            <a href="posts.php?action=delete&id=<?= $post['id'] ?>" class="btn-sm btn-danger" style="text-decoration: none; border-radius: 4px; display: inline-block;" onclick="return confirm('Are you sure you want to delete this post?');"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr>
                        <td colspan="3" style="text-align: center; padding: 2rem;">No posts found.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

<?php endif; ?>

<?php require_once 'includes/admin_footer.php'; ?>
