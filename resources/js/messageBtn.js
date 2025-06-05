document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.messageBtn').forEach(btn => {
        btn.addEventListener('click', () => {
            const message = btn.closest('.message');
            if (message) {
                message.remove();
            }
        });
    });
});
