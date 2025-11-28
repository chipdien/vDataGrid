<?php
// Tạo ID duy nhất cho mỗi ô để tránh xung đột JS
$uniqueId = 'copy_' . uniqid();
?>
<div class="d-flex align-items-center group-hover-parent">
    <span class="text-body me-2">
        <?= htmlspecialchars($displayValue ?? $value) ?>
    </span>

    <?php if (!empty($copyable) && !empty($value)): ?>
        <button class="btn btn-link p-0 text-muted opacity-50 hover-opacity-100" 
                onclick="copyToClipboard('<?= htmlspecialchars($value) ?>', this)"
                title="Sao chép">
            <i class="bi bi-copy"></i>
        </button>
    <?php endif; ?>
</div>

<script>
// Hàm JS nhỏ gọn để xử lý copy (Chỉ cần khai báo 1 lần trong trang, nhưng để ở đây cho tiện demo)
if (typeof copyToClipboard === 'undefined') {
    window.copyToClipboard = function(text, btn) {
        navigator.clipboard.writeText(text).then(function() {
            // Đổi icon thành check xanh
            let icon = btn.querySelector('i');
            icon.className = 'bi bi-check-lg text-success';
            setTimeout(() => { icon.className = 'bi bi-copy'; }, 2000);
        });
    }
}
</script>