function setupDropdown() {
    const signBtn = document.getElementById('SignUpButton');
    const menu = document.getElementById('SignUpMenu');
    if (!signBtn || !menu) return;
    signBtn.addEventListener('click', function (e) {
        e.preventDefault();
        menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
    });
    document.addEventListener('click', function (e) {
        if (!signBtn.contains(e.target) && !menu.contains(e.target)) {
            menu.style.display = 'none';
        }
    });
}
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', setupDropdown);
} else {
    setupDropdown();
}