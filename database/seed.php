<?php
// database/seed.php
require_once __DIR__ . '/../includes/db.php';

try {
    $pdo->exec("DELETE FROM practice_areas");
    $pdo->exec("DELETE FROM attorneys");
    $pdo->exec("DELETE FROM case_results");
    $pdo->exec("DELETE FROM clients");
    $pdo->exec("DELETE FROM invoices");
    $pdo->exec("DELETE FROM appointments");

    // 1. Practice Areas
    $areas = [
        ['Corporate Law', 'Comprehensive legal solutions for businesses of all sizes.', 'fas fa-briefcase'],
        ['Family Law', 'Compassionate representation in family matters.', 'fas fa-users'],
        ['Criminal Defense', 'Aggressive and strategic defense.', 'fas fa-gavel'],
        ['Real Estate', 'Expert navigation of property transactions.', 'fas fa-building']
    ];
    $stmt = $pdo->prepare("INSERT INTO practice_areas (title, description, icon) VALUES (?, ?, ?)");
    foreach ($areas as $a) $stmt->execute($a);

    // 2. Attorneys
    $attorneys = [
        ['Arthur Pendelton', 'Managing Partner', 'Arthur brings 25 years of litigation experience.', 'fas fa-user-tie', 'arthur@justicepartners.com'],
        ['Sarah Jenkins', 'Senior Corporate Counsel', 'Sarah specializes in international mergers.', 'fas fa-user-graduate', 'sarah@justicepartners.com']
    ];
    $stmt = $pdo->prepare("INSERT INTO attorneys (name, position, bio, image, email) VALUES (?, ?, ?, ?, ?)");
    foreach ($attorneys as $a) $stmt->execute($a);

    // 3. Case Results
    $cases = [
        ['Tech Giant Merger', '$4.2B', 'Corporate Law', 'Successfully negotiated a cross-border merger.'],
        ['IP Defense', '$12.5M', 'Litigation', 'Defended a startup resulting in a counter-suit victory.']
    ];
    $stmt = $pdo->prepare("INSERT INTO case_results (title, amount, case_type, description) VALUES (?, ?, ?, ?)");
    foreach ($cases as $c) $stmt->execute($c);

    // 4. Clients & Invoices
    $clients = [
        ['Acme Corp', 'billing@acme.com'],
        ['Global Logistics', 'finance@globallogistics.com']
    ];
    $pwd = password_hash('password123', PASSWORD_BCRYPT);
    $stmtClient = $pdo->prepare("INSERT INTO clients (name, email, password) VALUES (?, ?, ?)");
    
    // SQLite uses rowid for lastInsertId unless explicitly configured.
    // However, PDO lastInsertId works fine for standard auto increments.
    $stmtInvoice = $pdo->prepare("INSERT INTO invoices (client_id, amount, description, status, payment_gateway_used) VALUES (?, ?, ?, ?, ?)");
    
    foreach ($clients as $c) {
        $stmtClient->execute([$c[0], $c[1], $pwd]);
        $client_id = $pdo->lastInsertId();
        
        $stmtInvoice->execute([$client_id, 2500.00, 'Legal Retainer', 'paid', 'stripe']);
        $stmtInvoice->execute([$client_id, 500.00, 'Consultation', 'unpaid', null]);
    }

    echo "Database successfully seeded.\n";
} catch (Exception $e) {
    echo "Seeding error: " . $e->getMessage() . "\n";
}
