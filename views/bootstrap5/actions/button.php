<?php
/**
 * @var string $url
 * @var string $label
 * @var string $class
 * @var string $icon
 * @var ?string $confirm
 */
$onclick = '';
if ($confirm) {
    // Escape dấu nháy đơn để tránh lỗi JS
    $safeMsg = addslashes($confirm);
    $onclick = "onclick=\"return confirm('$safeMsg');\"";
}
?>
<a href="<?= htmlspecialchars($url) ?>" 
   class="btn btn-sm <?= $class ?>" 
   target="<?= $target ?>"
   <?= $onclick ?>
   title="<?= htmlspecialchars($label) ?>">
    
    <?php if ($icon): ?>
        <i class="<?= $icon ?>"></i>
    <?php endif; ?>
    
    <!-- Chỉ hiện label nếu không có icon, hoặc có thể hiện cả hai tùy design -->
    <span class="d-none d-lg-inline ms-1"><?= htmlspecialchars($label) ?></span>
</a>