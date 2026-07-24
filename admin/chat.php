<?php
require_once 'includes/admin_header.php';

if (!isset($_SESSION['admin_id'])) {
    exit;
}
?>

<div class="card" style="height: 70vh; display: flex; flex-direction: column;">
    <h3 style="margin-top: 0; color: var(--admin-primary); border-bottom: 1px solid #e2e8f0; padding-bottom: 1rem;"><i class="fas fa-comments"></i> Internal Firm Chat</h3>
    
    <div id="chatBox" style="flex: 1; overflow-y: auto; padding: 1rem; background-color: #f8fafc; border-radius: 4px; margin-bottom: 1rem; border: 1px solid #e2e8f0;">
        <!-- Messages loaded via AJAX -->
    </div>
    
    <form id="chatForm" style="display: flex; gap: 1rem;">
        <input type="text" id="chatInput" required placeholder="Type a secure message to your team..." style="flex: 1; padding: 0.75rem; border: 1px solid #cbd5e1; border-radius: 4px; font-family: inherit;">
        <button type="submit" class="btn btn-primary" style="padding: 0.75rem 1.5rem; background-color: #3b82f6; border: none; border-radius: 4px;"><i class="fas fa-paper-plane"></i> Send</button>
    </form>
</div>

<script>
const chatBox = document.getElementById('chatBox');
const chatForm = document.getElementById('chatForm');
const chatInput = document.getElementById('chatInput');

function loadMessages() {
    fetch('chat_api.php')
        .then(res => res.json())
        .then(data => {
            let html = '';
            data.forEach(msg => {
                const isMe = msg.sender_id == <?= $_SESSION['admin_id'] ?>;
                html += `
                    <div style="margin-bottom: 1rem; display: flex; flex-direction: column; align-items: ${isMe ? 'flex-end' : 'flex-start'};">
                        <span style="font-size: 0.75rem; color: #64748b; margin-bottom: 0.25rem;">${msg.username} (${msg.role}) • ${msg.time}</span>
                        <div style="background-color: ${isMe ? '#3b82f6' : '#e2e8f0'}; color: ${isMe ? 'white' : '#1e293b'}; padding: 0.75rem 1rem; border-radius: 8px; max-width: 70%;">
                            ${msg.message}
                        </div>
                    </div>
                `;
            });
            const shouldScroll = chatBox.scrollTop + chatBox.clientHeight === chatBox.scrollHeight;
            chatBox.innerHTML = html;
            if (shouldScroll || html.length > 0) {
                chatBox.scrollTop = chatBox.scrollHeight;
            }
        });
}

chatForm.addEventListener('submit', (e) => {
    e.preventDefault();
    const msg = chatInput.value.trim();
    if (msg) {
        fetch('chat_api.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'message=' + encodeURIComponent(msg)
        }).then(() => {
            chatInput.value = '';
            loadMessages();
        });
    }
});

setInterval(loadMessages, 3000);
loadMessages();
</script>

<?php require_once 'includes/admin_footer.php'; ?>
