<?php 
$page_title = 'Legal Insights & News';
require_once 'includes/header.php'; 
?>

<section class="section" style="background-color: var(--primary-color); padding: 80px 0 40px; color: white;">
    <div class="container text-center">
        <h1 style="color: white; margin-bottom: 1.5rem;">Legal Insights & News</h1>
        <form method="GET" action="blog.php" style="max-width: 500px; margin: 0 auto; display: flex; gap: 0.5rem;">
            <input type="text" name="q" value="<?= htmlspecialchars($_GET['q'] ?? '') ?>" placeholder="Search articles..." style="flex: 1; padding: 0.75rem 1rem; border-radius: 4px; border: none; font-family: inherit;">
            <button type="submit" class="btn btn-primary" style="padding: 0.75rem 1.5rem;"><i class="fas fa-search"></i></button>
        </form>
    </div>
</section>

<section class="section bg-white">
    <div class="container">
        <div class="grid-3">
            <?php
            $search = $_GET['q'] ?? '';
            $page = max(1, (int)($_GET['page'] ?? 1));
            $limit = 6;
            $offset = ($page - 1) * $limit;

            $whereClause = "";
            $params = [];
            
            if ($search) {
                $whereClause = "WHERE title LIKE ? OR content LIKE ?";
                $params = ["%$search%", "%$search%"];
            }

            // Get total for pagination
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM posts $whereClause");
            $stmt->execute($params);
            $total = $stmt->fetchColumn();
            $totalPages = ceil($total / $limit);

            $stmt = $pdo->prepare("SELECT * FROM posts $whereClause ORDER BY created_at DESC LIMIT $limit OFFSET $offset");
            $stmt->execute($params);
            $posts = $stmt->fetchAll();
            if(count($posts) > 0):
                foreach ($posts as $row):
            ?>
            <div class="card" style="padding: 0; overflow: hidden; text-align: left;">
                <?php if ($row['image']): ?>
                    <img src="<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['title']) ?>" style="width: 100%; height: 200px; object-fit: cover;">
                <?php else: ?>
                    <div style="width: 100%; height: 200px; background-color: var(--admin-sidebar); display: flex; align-items: center; justify-content: center; color: white;">
                        <i class="fas fa-newspaper fa-3x"></i>
                    </div>
                <?php endif; ?>
                <div style="padding: 1.5rem;">
                    <p style="color: #64748b; font-size: 0.85rem; margin-bottom: 0.5rem;"><i class="far fa-calendar-alt"></i> <?= date('F j, Y', strtotime($row['created_at'])) ?></p>
                    <h3 style="margin-bottom: 1rem; font-size: 1.25rem;">
                        <a href="post.php?id=<?= $row['id'] ?>" style="color: var(--primary-color);"><?= htmlspecialchars($row['title']) ?></a>
                    </h3>
                    <p style="font-size: 0.95rem; margin-bottom: 1.5rem; color: var(--text-light);">
                        <?= htmlspecialchars(substr(strip_tags($row['content']), 0, 120)) ?>...
                    </p>
                    <a href="post.php?id=<?= $row['id'] ?>" style="color: var(--secondary-color); font-weight: 600; font-size: 0.95rem;">Read More &rarr;</a>
                </div>
            </div>
            <?php 
                endforeach;
            else:
            ?>
                <p style="grid-column: span 3; text-align: center; color: var(--text-light);">No articles have been published yet. Check back soon!</p>
            <?php endif; ?>
        </div>
        
        <?php if ($totalPages > 1): ?>
        <div style="display: flex; justify-content: center; margin-top: 3rem; gap: 0.5rem;">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="blog.php?page=<?= $i ?><?= $search ? '&q='.urlencode($search) : '' ?>" style="padding: 0.5rem 1rem; border-radius: 4px; background-color: <?= $i === $page ? 'var(--secondary-color)' : '#e2e8f0' ?>; color: <?= $i === $page ? 'white' : 'var(--text-color)' ?>; font-weight: 500; text-decoration: none;"><?= $i ?></a>
            <?php endfor; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
