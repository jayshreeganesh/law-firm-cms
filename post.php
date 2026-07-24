<?php 
require_once 'includes/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
$stmt->execute([$id]);
$post = $stmt->fetch();

if (!$post) {
    header("Location: blog.php");
    exit;
}

require_once 'includes/header.php'; 
?>

<section class="section" style="background-color: var(--primary-color); padding: 80px 0 40px; color: white;">
    <div class="container text-center">
        <h1 style="color: white; margin-bottom: 0.5rem; max-width: 800px; margin-left: auto; margin-right: auto;"><?= htmlspecialchars($post['title']) ?></h1>
        <p style="color: #cbd5e1; font-size: 0.95rem;"><i class="far fa-calendar-alt"></i> <?= date('F j, Y', strtotime($post['created_at'])) ?></p>
    </div>
</section>

<section class="section bg-white">
    <div class="container" style="max-width: 800px;">
        <?php if ($post['image']): ?>
            <div style="margin-bottom: 2rem;">
                <img src="<?= htmlspecialchars($post['image']) ?>" alt="<?= htmlspecialchars($post['title']) ?>" style="width: 100%; border-radius: 8px; box-shadow: var(--shadow-md);">
            </div>
        <?php endif; ?>
        
        <div style="font-size: 1.1rem; line-height: 1.8; color: var(--text-color);">
            <?= nl2br(htmlspecialchars($post['content'])) ?>
        </div>
        
        <div style="margin-top: 4rem; border-top: 1px solid var(--border-color); padding-top: 2rem; text-align: center;">
            <a href="blog.php" class="btn btn-primary">&larr; Back to News</a>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
