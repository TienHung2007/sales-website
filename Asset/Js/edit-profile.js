document.getElementById('avatar').addEventListener('change', function(event) {
    const input = event.target;
    const preview = document.getElementById('avatarPreview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            document.getElementById('profileForm').submit();
        };
        reader.readAsDataURL(input.files[0]);
    }
});

document.getElementById('deleteAccountBtn').addEventListener('click', function() {
    if (confirm('Bạn có chắc chắn muốn xóa tài khoản? Hành động này không thể hoàn tác.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.style.display = 'none';
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'delete_account';
        input.value = '1';
        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
    }
});