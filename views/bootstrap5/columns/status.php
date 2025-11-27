<?php
/**
 * @var string $label
 * @var string $class (primary, success, danger, warning, info, dark)
 */
?>
<span class="badge rounded-pill bg-<?= $class ?> bg-opacity-75 px-3 py-2">
    <?= htmlspecialchars($label) ?>
</span>