<?php
/** @var \VGrid\Components\Inputs\TextInput $field */
?>
<div class="mb-3">
    <label class="form-label fw-bold">
        <?= htmlspecialchars($field->label) ?>
        <?php if($field->required): ?> <span class="text-danger">*</span> <?php endif; ?>
    </label>
    <input type="<?= $field->getType() ?>" 
           name="<?= $field->name ?>" 
           class="form-control <?= !empty($error) ? 'is-invalid' : '' ?>"
           value="<?= htmlspecialchars($value ?? '') ?>"
           <?= $field->required ? 'required' : '' ?>
    >
    <?php if(!empty($error)): ?>
        <div class="invalid-feedback"><?= $error ?></div>
    <?php endif; ?>
</div>