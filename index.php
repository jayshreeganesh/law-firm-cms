<?php require_once 'includes/header.php'; ?>

<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <h1><?= $lang['hero_title'] ?></h1>
        <p><?= $lang['hero_desc'] ?></p>
        <a href="book.php" class="btn btn-primary" style="padding: 1rem 2rem; font-size: 1.1rem;"><?= $lang['schedule_consultation'] ?></a>
    </div>
</section>

<?php
$stmt = $pdo->query(db_limit_offset_sql($db_driver, "SELECT * FROM case_results ORDER BY created_at DESC", 3));
$results = $stmt->fetchAll();
if (count($results) > 0):
?>
<section class="section" style="background-color: var(--primary-color); color: white;">
    <div class="container text-center">
        <h2 style="color: white; margin-bottom: 3rem;"><?= $lang['recent_victories'] ?></h2>
        <div class="grid-3">
            <?php foreach ($results as $res): ?>
            <div class="card" style="background-color: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); text-align: left;">
                <h3 style="color: var(--secondary-color); font-size: 2.5rem; margin-bottom: 0.5rem;"><?= htmlspecialchars($res['amount']) ?></h3>
                <h4 style="color: white; font-size: 1.25rem; margin-bottom: 0.5rem;"><?= htmlspecialchars($res['title']) ?></h4>
                <p style="color: #94a3b8; font-weight: bold; margin-bottom: 1rem; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 1px;"><?= htmlspecialchars($res['case_type']) ?></p>
                <?php if ($res['description']): ?>
                    <p style="color: #cbd5e1; font-style: italic;">"<?= htmlspecialchars($res['description']) ?>"</p>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="section bg-light">
    <div class="container">
        <h2 class="section-title">Our Practice Areas</h2>
        <div class="grid-3">
            <?php
            $stmt = $pdo->query(db_limit_offset_sql($db_driver, "SELECT * FROM practice_areas ORDER BY id DESC", 3));
            while ($row = $stmt->fetch()):
            ?>
            <div class="card">
                <i class="<?= htmlspecialchars($row['icon']) ?>"></i>
                <h3><?= htmlspecialchars($row['title']) ?></h3>
                <p><?= htmlspecialchars($row['description']) ?></p>
            </div>
            <?php endwhile; ?>
        </div>
        <div style="text-align: center; margin-top: 3rem;">
            <a href="practice-areas.php" class="btn btn-primary">View All Services</a>
        </div>
    </div>
</section>

<!-- Why Choose Us -->
<section class="section" style="background-color: var(--primary-color); color: white;">
    <div class="container">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 4rem; align-items: center;">
            <div>
                <h2 style="color: white; font-size: 2.5rem; margin-bottom: 1.5rem;">Why Choose Justice Partners?</h2>
                <p style="color: #cbd5e1; margin-bottom: 2rem; font-size: 1.1rem;">With decades of combined experience, our law firm has built a reputation for excellence, integrity, and unwavering dedication to our clients.</p>
                <ul style="list-style: none; color: #cbd5e1;">
                    <li style="margin-bottom: 1rem;"><i class="fas fa-check-circle" style="color: var(--secondary-color); margin-right: 10px;"></i> Decades of Trial Experience</li>
                    <li style="margin-bottom: 1rem;"><i class="fas fa-check-circle" style="color: var(--secondary-color); margin-right: 10px;"></i> Award-Winning Legal Team</li>
                    <li style="margin-bottom: 1rem;"><i class="fas fa-check-circle" style="color: var(--secondary-color); margin-right: 10px;"></i> Free Initial Consultations</li>
                    <li><i class="fas fa-check-circle" style="color: var(--secondary-color); margin-right: 10px;"></i> 24/7 Availability for Urgent Matters</li>
                </ul>
            </div>
            <div>
                <img src="https://images.unsplash.com/photo-1589829085413-56de8ae18c73?auto=format&fit=crop&q=80" alt="Law Office" style="width: 100%; border-radius: 8px; box-shadow: var(--shadow-lg);">
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
