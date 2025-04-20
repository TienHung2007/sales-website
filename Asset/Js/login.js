document.addEventListener('DOMContentLoaded', function() {
    const toast = document.querySelector('[data-toast-login]');
    if (toast.classList.contains('active')) {
        setTimeout(() => toast.classList.remove('active'), 3000);
    }
});