<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['client_id'])) {
    header("Location: client_login.php");
    exit;
}

if (!isset($_GET['doc_id'])) {
    header("Location: client_dashboard.php");
    exit;
}

$doc_id = (int)$_GET['doc_id'];
$client_id = $_SESSION['client_id'];

$stmt = $pdo->prepare("SELECT * FROM client_documents WHERE id = ? AND client_id = ? AND requires_signature = 1");
$stmt->execute([$doc_id, $client_id]);
$doc = $stmt->fetch();

if (!$doc || $doc['is_signed']) {
    header("Location: client_dashboard.php");
    exit;
}

$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signature_data'])) {
    $sig_data = $_POST['signature_data'];
    $stmt = $pdo->prepare("UPDATE client_documents SET is_signed = 1, signature_data = ? WHERE id = ?");
    $stmt->execute([$sig_data, $doc_id]);
    $success = true;
}

$page_title = 'Sign Document';
require_once 'includes/header.php';
?>

<section class="section bg-light" style="min-height: 70vh; display: flex; align-items: center;">
    <div class="container" style="max-width: 600px;">
        <?php if ($success): ?>
            <div class="card text-center" style="padding: 3rem 2rem;">
                <i class="fas fa-file-signature" style="font-size: 4rem; color: #10b981; margin-bottom: 1.5rem;"></i>
                <h2 style="margin-bottom: 1rem;">Document Signed</h2>
                <p style="color: var(--text-light); margin-bottom: 2rem;">Thank you! Your signature has been securely recorded and attached to the document.</p>
                <a href="client_dashboard.php" class="btn btn-primary">Return to Dashboard</a>
            </div>
        <?php else: ?>
            <div class="card">
                <h3 style="margin-top: 0; color: var(--primary-color); margin-bottom: 0.5rem;">Review & Sign</h3>
                <p style="color: #64748b; margin-bottom: 1.5rem;">Document: <strong><?= htmlspecialchars($doc['title']) ?></strong></p>
                
                <div style="background-color: #f1f5f9; padding: 1rem; border-radius: 4px; margin-bottom: 1.5rem; text-align: center;">
                    <a href="<?= htmlspecialchars($doc['file_path']) ?>" target="_blank" class="btn btn-primary" style="background-color: #3b82f6; border: none;"><i class="fas fa-file-pdf"></i> View Full Document</a>
                </div>
                
                <form method="POST" action="sign_document.php?doc_id=<?= $doc_id ?>" id="signForm">
                    <div class="form-group">
                        <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Draw Your Signature *</label>
                        <div style="border: 2px dashed #cbd5e1; border-radius: 4px; background: white; margin-bottom: 0.5rem;">
                            <canvas id="sigCanvas" width="500" height="200" style="width: 100%; touch-action: none; cursor: crosshair;"></canvas>
                        </div>
                        <button type="button" id="clearBtn" class="btn-sm" style="background: #e2e8f0; border: none; padding: 0.25rem 0.75rem; border-radius: 4px; cursor: pointer;">Clear Signature</button>
                    </div>
                    <input type="hidden" name="signature_data" id="signatureData" required>
                    
                    <div style="margin-top: 1.5rem;">
                        <label style="display: flex; gap: 0.5rem; font-size: 0.9rem; color: #475569; align-items: flex-start; cursor: pointer;">
                            <input type="checkbox" required style="margin-top: 0.25rem;">
                            <span>I agree that this electronic signature is the legally binding equivalent to my handwritten signature.</span>
                        </label>
                    </div>
                    
                    <div style="margin-top: 2rem;">
                        <button type="submit" class="btn btn-primary" style="width: 100%; font-size: 1.1rem; padding: 1rem; background-color: #10b981; border: none;">
                            <i class="fas fa-check"></i> Accept & Sign Document
                        </button>
                    </div>
                </form>
            </div>

            <script>
                const canvas = document.getElementById('sigCanvas');
                const ctx = canvas.getContext('2d');
                let isDrawing = false;
                
                // Adjust canvas to match display size
                const rect = canvas.getBoundingClientRect();
                canvas.width = rect.width;
                canvas.height = 200;

                ctx.strokeStyle = '#0f172a';
                ctx.lineWidth = 3;
                ctx.lineCap = 'round';
                ctx.lineJoin = 'round';

                function startDrawing(e) {
                    isDrawing = true;
                    draw(e);
                }

                function stopDrawing() {
                    isDrawing = false;
                    ctx.beginPath();
                    document.getElementById('signatureData').value = canvas.toDataURL();
                }

                function draw(e) {
                    if (!isDrawing) return;
                    e.preventDefault();
                    
                    let clientX = e.clientX || (e.touches && e.touches[0].clientX);
                    let clientY = e.clientY || (e.touches && e.touches[0].clientY);
                    
                    const rect = canvas.getBoundingClientRect();
                    const x = clientX - rect.left;
                    const y = clientY - rect.top;

                    ctx.lineTo(x, y);
                    ctx.stroke();
                    ctx.beginPath();
                    ctx.moveTo(x, y);
                }

                canvas.addEventListener('mousedown', startDrawing);
                canvas.addEventListener('mousemove', draw);
                canvas.addEventListener('mouseup', stopDrawing);
                canvas.addEventListener('mouseout', stopDrawing);
                
                canvas.addEventListener('touchstart', startDrawing, {passive: false});
                canvas.addEventListener('touchmove', draw, {passive: false});
                canvas.addEventListener('touchend', stopDrawing);

                document.getElementById('clearBtn').addEventListener('click', () => {
                    ctx.clearRect(0, 0, canvas.width, canvas.height);
                    document.getElementById('signatureData').value = '';
                });

                document.getElementById('signForm').addEventListener('submit', function(e) {
                    if (!document.getElementById('signatureData').value) {
                        e.preventDefault();
                        alert('Please draw your signature before submitting.');
                    }
                });
            </script>
        <?php endif; ?>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
