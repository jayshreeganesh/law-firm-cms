<?php require_once 'includes/header.php'; ?>

<section class="section" style="background-color: var(--primary-color); padding: 80px 0 40px; color: white;">
    <div class="container text-center">
        <h1 style="color: white; margin-bottom: 0;">Legal Insights & News</h1>
    </div>
</section>

<section class="section bg-white">
    <div class="container">
        <div class="grid-3">
            <?php
            $stmt = $pdo->query("SELECT * FROM posts ORDER BY created_at DESC");
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
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
