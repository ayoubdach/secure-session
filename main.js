document.addEventListener('contextmenu', function(e) {
    e.preventDefault();
});

document.addEventListener('keydown', function(e) {
    if (
        e.key === 'F12' || 
        (e.ctrlKey && e.shiftKey && e.key.toLowerCase() === 'i') || 
        (e.ctrlKey && e.key.toLowerCase() === 'u') || 
        (e.ctrlKey && e.key.toLowerCase() === 's') || 
        (e.ctrlKey && e.key.toLowerCase() === 'p')
    ) {
        e.preventDefault();
        e.stopPropagation();
    }
});

document.addEventListener('keydown', function(e) {
    if (e.ctrlKey && e.shiftKey && (e.key.toLowerCase() === 'j' || e.key.toLowerCase() === 'c')) {
        e.preventDefault();
        e.stopPropagation();
    }
});
