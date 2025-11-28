<?php
/** * @var \VGrid\Components\Inputs\SelectInput $field 
 * @var mixed $value Giá trị hiện tại của field
 */
?>
<div class="mb-3">
    <label class="form-label fw-bold">
        <?= htmlspecialchars($field->label ?? '') ?>
        <?php if($field->required ?? false): ?> <span class="text-danger">*</span> <?php endif; ?>
    </label>
    <select name="<?= $field->name ?>" 
            class="form-select <?= !empty($error) ? 'is-invalid' : '' ?>"
            <?= ($field->required ?? false) ? 'required' : '' ?>>
        
        <option value="">-- Chọn --</option>
        
        <?php foreach ($field->getOptions() as $optVal => $optLabel): ?>
            <?php 
                // Kiểm tra selected (lỏng lẻo để match '1' với 1)
                $selected = ($value == $optVal) ? 'selected' : ''; 
            ?>
            <option value="<?= htmlspecialchars($optVal) ?>" <?= $selected ?>>
                <?= htmlspecialchars($optLabel) ?>
            </option>
        <?php endforeach; ?>
    </select>
    
    <?php if(!empty($error)): ?>
        <div class="invalid-feedback"><?= $error ?></div>
    <?php endif; ?>
</div>