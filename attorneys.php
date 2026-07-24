<?php require_once 'includes/header.php'; ?>

<section class="section" style="background-color: var(--primary-color); padding: 80px 0 40px; color: white;">
    <div class="container text-center">
        <h1 style="color: white; margin-bottom: 0;">Our Attorneys</h1>
    </div>
</section>

<section class="section bg-white">
    <div class="container">
        <div class="grid-3">
            <?php
            $stmt = $pdo->query("SELECT * FROM attorneys ORDER BY name ASC");
            $attorneys = $stmt->fetchAll();
            if(count($attorneys) > 0):
                foreach ($attorneys as $row):
            ?>
            <div class="card" style="padding: 0; overflow: hidden;">
                <img src="<?= htmlspecialchars($row['image'] ?: 'https://images.unsplash.com/photo-1560250097-0b93528c311a?auto=format&fit=crop&q=80') ?>" alt="<?= htmlspecialchars($row['name']) ?>" style="width: 100%; height: 250px; object-fit: cover;">
                <div style="padding: 1.5rem;">
                    <h3><?= htmlspecialchars($row['name']) ?></h3>
                    <p style="color: var(--secondary-color); font-weight: 600; margin-bottom: 1rem;"><?= htmlspecialchars($row['position']) ?></p>
                    <p style="font-size: 0.95rem; margin-bottom: 1rem;"><?= htmlspecialchars(substr($row['bio'], 0, 100)) ?>...</p>
                    <div style="border-top: 1px solid var(--border-color); padding-top: 1rem;">
                        <a href="mailto:<?= htmlspecialchars($row['email']) ?>" style="margin-right: 15px;"><i class="fas fa-envelope"></i></a>
                        <a href="tel:<?= htmlspecialchars($row['phone']) ?>"><i class="fas fa-phone"></i></a>
                    </div>
                </div>
            </div>
            <?php 
                endforeach;
            else:
            ?>
                <p style="grid-column: span 3; text-align: center;">No attorneys listed yet.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
