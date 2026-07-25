<footer>
    <div class="container">
        <div class="footer-grid">
            <div class="footer-widget">
                <h4 style="font-family: 'Playfair Display', serif; font-size: 1.8rem; color: white; font-weight: 800;">
                    Justice<span style="color: var(--secondary-color);">.</span>
                </h4>
                <p style="margin-top: 1rem;">Providing world-class legal representation for individuals and corporations worldwide. Your justice is our priority.</p>
            </div>
            <div class="footer-widget">
                <h4 style="color: white; margin-bottom: 1.5rem; font-size: 1.25rem;"><?= $lang['subscribe_newsletter'] ?></h4>
                <p style="color: #cbd5e1; margin-bottom: 1rem; font-size: 0.95rem;"><?= $lang['newsletter_desc'] ?></p>
                <form action="subscribe.php" method="POST" style="display: flex; margin-bottom: 0.5rem;">
                    <input type="email" name="email" placeholder="<?= $lang['email_address'] ?>" required style="flex: 1; padding: 0.75rem; border: none; border-radius: 4px 0 0 4px; font-family: inherit;">
                    <button type="submit" class="btn btn-primary" style="border-radius: 0 4px 4px 0; padding: 0.75rem 1rem;"><?= $lang['subscribe'] ?></button>
                </form>
                <?php if (isset($_GET['newsletter_msg'])): ?>
                    <div style="color: #4ade80; font-size: 0.85rem;"><?= htmlspecialchars($_GET['newsletter_msg']) ?></div>
                <?php endif; ?>
            </div>
            <div class="footer-widget">
                <h4><?= $lang['quick_links'] ?></h4>
                <ul class="footer-links">
                    <li><a href="index.php"><?= $lang['home'] ?></a></li>
                    <li><a href="about.php"><?= $lang['about_us'] ?></a></li>
                    <li><a href="practice-areas.php"><?= $lang['practice_areas'] ?></a></li>
                    <li><a href="attorneys.php"><?= $lang['our_attorneys'] ?></a></li>
                    <li><a href="blog.php"><?= $lang['news'] ?></a></li>
                    <li><a href="contact.php"><?= $lang['contact'] ?></a></li>
                    <li><a href="client_login.php" style="color: var(--secondary-color); font-weight: bold;"><i class="fas fa-lock"></i> <?= $lang['client_portal'] ?></a></li>
                </ul>
            </div>
            <div class="footer-widget">
                <h4><?= $lang['contact_info'] ?></h4>
                <ul class="footer-links">
                    <li><i class="fas fa-map-marker-alt" style="color: var(--secondary-color); margin-right: 10px;"></i> <?= htmlspecialchars(get_setting($pdo, 'site_address')) ?></li>
                    <li><i class="fas fa-phone" style="color: var(--secondary-color); margin-right: 10px;"></i> <?= htmlspecialchars(get_setting($pdo, 'site_phone')) ?></li>
                    <li><i class="fas fa-envelope" style="color: var(--secondary-color); margin-right: 10px;"></i> <?= htmlspecialchars(get_setting($pdo, 'site_email')) ?></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?= date('Y') ?> <?= htmlspecialchars(get_setting($pdo, 'site_name')) ?>. All Rights Reserved. Designed by Antigravity.</p>
        </div>
    </div>
</footer>
<?php $chat_widget_type = get_setting($pdo, 'chat_widget_type') ?: 'tawk'; ?>
<?php if ($chat_widget_type === 'ai'): ?>
<!-- AI Legal Assistant Widget -->
<div id="aiChatWidget" style="position: fixed; bottom: 20px; right: 20px; z-index: 9999; font-family: 'Inter', sans-serif;">
    <div id="aiChatWindow" style="display: none; width: 300px; height: 400px; background: white; border-radius: 8px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); flex-direction: column; overflow: hidden; border: 1px solid #e2e8f0; margin-bottom: 10px;">
        <div style="background: var(--primary-color); color: white; padding: 10px; display: flex; justify-content: space-between; align-items: center;">
            <strong style="font-size: 0.95rem;"><i class="fas fa-robot"></i> AI Legal Assistant</strong>
            <button onclick="document.getElementById('aiChatWindow').style.display='none'" style="background: none; border: none; color: white; cursor: pointer;"><i class="fas fa-times"></i></button>
        </div>
        <div id="aiChatHistory" style="flex: 1; padding: 10px; overflow-y: auto; background: #f8fafc; font-size: 0.85rem; display: flex; flex-direction: column; gap: 10px;">
            <div style="background: #e2e8f0; padding: 8px 12px; border-radius: 8px; align-self: flex-start; max-width: 85%;">
                Hello! I am your virtual legal assistant. How can I help you today?
            </div>
        </div>
        <form id="aiChatForm" style="display: flex; border-top: 1px solid #e2e8f0;">
            <input type="text" id="aiChatInput" required placeholder="Type a question..." style="flex: 1; padding: 10px; border: none; outline: none; font-size: 0.85rem;">
            <button type="submit" style="background: var(--secondary-color); color: white; border: none; padding: 0 15px; cursor: pointer;"><i class="fas fa-paper-plane"></i></button>
        </form>
    </div>
    <button onclick="const w = document.getElementById('aiChatWindow'); w.style.display = w.style.display === 'none' ? 'flex' : 'none';" style="background: var(--primary-color); color: white; border: none; width: 60px; height: 60px; border-radius: 50%; box-shadow: 0 4px 6px rgba(0,0,0,0.1); cursor: pointer; float: right; font-size: 1.5rem; display: flex; align-items: center; justify-content: center;">
        <i class="fas fa-comment-dots"></i>
    </button>
</div>
<script>
document.getElementById('aiChatForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const input = document.getElementById('aiChatInput');
    const msg = input.value.trim();
    if (!msg) return;
    
    const history = document.getElementById('aiChatHistory');
    history.innerHTML += `<div style="background: var(--secondary-color); color: white; padding: 8px 12px; border-radius: 8px; align-self: flex-end; max-width: 85%;">${msg}</div>`;
    history.scrollTop = history.scrollHeight;
    input.value = '';
    
    const formData = new URLSearchParams();
    formData.append('message', msg);
    
    fetch('chat_bot_api.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        history.innerHTML += `<div style="background: #e2e8f0; padding: 8px 12px; border-radius: 8px; align-self: flex-start; max-width: 85%;">${data.reply}</div>`;
        history.scrollTop = history.scrollHeight;
    });
});
</script>
<?php elseif ($chat_widget_type === 'tawk'): ?>
<script>
var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
(function(){
var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
s1.async=true;
s1.src='https://embed.tawk.to/64b5f8c9cc26a871b0290514/1h5jb2t8r'; // Example ID
s1.charset='UTF-8';
s1.setAttribute('crossorigin','*');
s0.parentNode.insertBefore(s1,s0);
})();
</script>
<?php endif; ?>

<script>
const toggleBtnFront = document.getElementById('darkModeToggleFront');
if (toggleBtnFront) {
    const iconFront = toggleBtnFront.querySelector('i');
    
    if (localStorage.getItem('theme') === 'dark') {
        document.body.classList.add('dark-mode');
        iconFront.classList.remove('fa-moon');
        iconFront.classList.add('fa-sun');
    }
    
    toggleBtnFront.addEventListener('click', () => {
        document.body.classList.toggle('dark-mode');
        if (document.body.classList.contains('dark-mode')) {
            localStorage.setItem('theme', 'dark');
            iconFront.classList.remove('fa-moon');
            iconFront.classList.add('fa-sun');
        } else {
            localStorage.setItem('theme', 'light');
            iconFront.classList.remove('fa-sun');
            iconFront.classList.add('fa-moon');
        }
    });
}

const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
const navLinks = document.querySelector('.nav-links');
if (mobileMenuBtn && navLinks) {
    mobileMenuBtn.addEventListener('click', () => {
        navLinks.classList.toggle('active');
    });
}
</script>

</body>
</html>
