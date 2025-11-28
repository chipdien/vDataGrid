<?php
/**
 * @var string $value Giá trị đã format (VD: 1.000.000 đ)
 * @var string $colorClass Class màu sắc (text-success, text-danger...)
 */
?>
<span class="<?= $colorClass ?>" style="font-feature-settings: 'tnum'">
    <?= $value ?>
</span>