<?php
/** * @var \VGrid\Components\Inputs\DateInput $field 
 * @var mixed $value 
 */

// Xử lý format value về Y-m-d chuẩn HTML5
$displayValue = $value;
if ($value && strtotime($value)) {
    $displayValue = date('Y-m-d', strtotime($value));
}
?>
<div class="mb-3">
    <label class="form-label fw-bold">
        <?= htmlspecialchars($field->label ?? '') ?>
        <?php if($field->required ?? false): ?> <span class="text-danger">*</span> <?php endif; ?>
    </label>
    <input type="date" 
           name="<?= $field->name ?>" 
           class="form-control <?= !empty($error) ? 'is-invalid' : '' ?>"
           value="<?= htmlspecialchars($displayValue ?? '') ?>"
           <?= ($field->required ?? false) ? 'required' : '' ?>
    >
    <?php if(!empty($error)): ?>
        <div class="invalid-feedback"><?= $error ?></div>
    <?php endif; ?>
</div>