<?php require_once 'includes/header.php'; ?>

<section class="section" style="background-color: var(--primary-color); padding: 80px 0 40px; color: white;">
    <div class="container text-center">
        <h1 style="color: white; margin-bottom: 0;">Our Practice Areas</h1>
    </div>
</section>

<section class="section bg-white">
    <div class="container">
        <div class="grid-3">
            <?php
            $stmt = $pdo->query("SELECT * FROM practice_areas ORDER BY title ASC");
            while ($row = $stmt->fetch()):
            ?>
            <div class="card">
                <i class="<?= htmlspecialchars($row['icon']) ?>"></i>
                <h3><?= htmlspecialchars($row['title']) ?></h3>
                <p><?= htmlspecialchars($row['description']) ?></p>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
