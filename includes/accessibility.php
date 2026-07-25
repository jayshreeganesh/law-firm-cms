<!-- Accessibility Widget -->
<style>
.a11y-widget {
    position: fixed;
    bottom: 20px;
    left: 20px;
    z-index: 9999;
}
.a11y-toggle {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background-color: var(--secondary-color);
    color: white;
    border: none;
    font-size: 24px;
    cursor: pointer;
    box-shadow: 0 4px 6px rgba(0,0,0,0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    transition: transform 0.3s ease;
}
.a11y-toggle:hover {
    transform: scale(1.1);
}
.a11y-panel {
    display: none;
    position: absolute;
    bottom: 60px;
    left: 0;
    width: 250px;
    background: var(--bg-color);
    border-radius: 8px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.2);
    padding: 15px;
    border: 1px solid var(--border-color);
    flex-direction: column;
    gap: 10px;
}
.a11y-panel.show {
    display: flex;
}
.a11y-btn {
    background: transparent;
    border: 1px solid var(--border-color);
    padding: 10px;
    border-radius: 5px;
    color: var(--text-color);
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 14px;
    text-align: left;
    transition: background 0.2s;
}
.a11y-btn:hover {
    background: rgba(0,0,0,0.05);
}
.a11y-btn i {
    font-size: 16px;
    width: 20px;
    color: var(--secondary-color);
}
/* A11y Classes applied to body */
body.a11y-high-contrast {
    filter: contrast(150%) saturate(150%);
    background-color: #000 !important;
    color: #fff !important;
}
body.a11y-high-contrast * {
    color: #fff !important;
    border-color: #555 !important;
}
body.a11y-dyslexia {
    font-family: "OpenDyslexic", "Comic Sans MS", Arial, sans-serif !important;
}
</style>

<div class="a11y-widget">
    <div class="a11y-panel" id="a11yPanel">
        <h4 style="margin:0 0 10px 0; font-size:16px;">Accessibility Tools</h4>
        <button class="a11y-btn" onclick="a11yTextSize(1)"><i class="fas fa-search-plus"></i> Increase Text Size</button>
        <button class="a11y-btn" onclick="a11yTextSize(-1)"><i class="fas fa-search-minus"></i> Decrease Text Size</button>
        <button class="a11y-btn" onclick="a11yHighContrast()"><i class="fas fa-adjust"></i> High Contrast Mode</button>
        <button class="a11y-btn" onclick="a11yDyslexia()"><i class="fas fa-font"></i> Dyslexia Friendly</button>
        <button class="a11y-btn" id="a11ySpeakBtn" onclick="a11ySpeakPage()"><i class="fas fa-volume-up"></i> Read Page Aloud</button>
        <button class="a11y-btn" onclick="a11yReset()" style="color:#e74c3c; border-color:#e74c3c;"><i class="fas fa-undo" style="color:#e74c3c;"></i> Reset Settings</button>
    </div>
    <button class="a11y-toggle" id="a11yToggle" title="Accessibility Tools" aria-label="Accessibility Options">
        <i class="fas fa-wheelchair"></i>
    </button>
</div>

<script>
let a11yFontSize = 100;
let synth = window.speechSynthesis;
let isSpeaking = false;

document.getElementById('a11yToggle').addEventListener('click', function() {
    document.getElementById('a11yPanel').classList.toggle('show');
});

function a11yTextSize(dir) {
    a11yFontSize += dir * 10;
    if(a11yFontSize < 80) a11yFontSize = 80;
    if(a11yFontSize > 150) a11yFontSize = 150;
    document.body.style.fontSize = a11yFontSize + '%';
    localStorage.setItem('a11y_fontsize', a11yFontSize);
}

function a11yHighContrast() {
    document.body.classList.toggle('a11y-high-contrast');
    localStorage.setItem('a11y_contrast', document.body.classList.contains('a11y-high-contrast'));
}

function a11yDyslexia() {
    document.body.classList.toggle('a11y-dyslexia');
    localStorage.setItem('a11y_dyslexia', document.body.classList.contains('a11y-dyslexia'));
}

function a11ySpeakPage() {
    if(isSpeaking) {
        synth.cancel();
        isSpeaking = false;
        document.getElementById('a11ySpeakBtn').innerHTML = '<i class="fas fa-volume-up"></i> Read Page Aloud';
        return;
    }
    
    // Read headers and paragraphs
    let elements = document.querySelectorAll('h1, h2, h3, p, li');
    let textToRead = "";
    elements.forEach(el => {
        textToRead += el.innerText + ". ";
    });
    
    if(textToRead.trim() !== '') {
        let utterance = new SpeechSynthesisUtterance(textToRead);
        utterance.onend = function() {
            isSpeaking = false;
            document.getElementById('a11ySpeakBtn').innerHTML = '<i class="fas fa-volume-up"></i> Read Page Aloud';
        };
        synth.speak(utterance);
        isSpeaking = true;
        document.getElementById('a11ySpeakBtn').innerHTML = '<i class="fas fa-volume-mute"></i> Stop Reading';
    }
}

function a11yReset() {
    a11yFontSize = 100;
    document.body.style.fontSize = '100%';
    document.body.classList.remove('a11y-high-contrast', 'a11y-dyslexia');
    synth.cancel();
    isSpeaking = false;
    document.getElementById('a11ySpeakBtn').innerHTML = '<i class="fas fa-volume-up"></i> Read Page Aloud';
    localStorage.removeItem('a11y_fontsize');
    localStorage.removeItem('a11y_contrast');
    localStorage.removeItem('a11y_dyslexia');
}

// Load preferences
window.addEventListener('DOMContentLoaded', () => {
    let savedSize = localStorage.getItem('a11y_fontsize');
    if(savedSize) {
        a11yFontSize = parseInt(savedSize);
        document.body.style.fontSize = a11yFontSize + '%';
    }
    if(localStorage.getItem('a11y_contrast') === 'true') {
        document.body.classList.add('a11y-high-contrast');
    }
    if(localStorage.getItem('a11y_dyslexia') === 'true') {
        document.body.classList.add('a11y-dyslexia');
    }
});
</script>
