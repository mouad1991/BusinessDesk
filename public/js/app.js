// BusinessDesk — Global JS

// ── Auto-dismiss alerts after 5s ──────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {
    setTimeout(function () {
        document.querySelectorAll('.alert').forEach(function (el) {
            el.style.transition = 'opacity .5s';
            el.style.opacity = '0';
            setTimeout(function () { el.remove(); }, 500);
        });
    }, 5000);
});

// ── Theme toggle (Dark / Light) ───────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {
    var toggle = document.getElementById('theme-toggle');
    if (!toggle) return;

    function getTheme() {
        return localStorage.getItem('bd-theme') || 'light';
    }

    function applyTheme(theme) {
        if (theme === 'dark') {
            document.documentElement.setAttribute('data-theme', 'dark');
        } else {
            document.documentElement.removeAttribute('data-theme');
        }
    }

    function updateIcon(theme) {
        var icon = document.getElementById('theme-icon');
        if (!icon) return;
        if (theme === 'dark') {
            // Sun icon (switch to light)
            icon.innerHTML = '<circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>';
        } else {
            // Moon icon (switch to dark)
            icon.innerHTML = '<path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>';
        }
    }

    // Init
    var current = getTheme();
    applyTheme(current);
    updateIcon(current);

    // Toggle on click
    toggle.addEventListener('click', function () {
        var next = getTheme() === 'dark' ? 'light' : 'dark';
        localStorage.setItem('bd-theme', next);
        applyTheme(next);
        updateIcon(next);
    });
});
