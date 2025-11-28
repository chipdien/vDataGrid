<?php
/** @var \VGrid\Components\Inputs\TextareaInput $field */
// Tạo ID duy nhất cho mỗi editor để tránh xung đột nếu có nhiều editor trên 1 trang
$uniqueId = 'vditor_' . $field->getName() . '_' . uniqid();
$isVditor = $field->isVditorEnabled();
$isMath   = $field->isMathEnabled();
?>

<div class="mb-3">
    <label class="form-label fw-bold">
        <?= htmlspecialchars($field->getLabel() ?? '') ?>
        <?php if($field->isRequired()): ?> <span class="text-danger">*</span> <?php endif; ?>
    </label>

    <!-- 1. Textarea gốc (Sẽ bị ẩn nếu dùng Vditor, dùng để gửi dữ liệu qua POST) -->
    <textarea name="<?= $field->getName() ?>" 
              id="<?= $uniqueId ?>_input"
              class="form-control <?= !empty($error) ? 'is-invalid' : '' ?>"
              rows="<?= $field->getRows() ?>"
              <?= $field->isRequired() ? 'required' : '' ?>
              <?= $isVditor ? 'style="display:none;"' : '' ?> 
    ><?= htmlspecialchars($value ?? '') ?></textarea>

    <!-- 2. Container hiển thị Vditor -->
    <?php if ($isVditor): ?>
        <div id="<?= $uniqueId ?>" class="mt-1"></div>
        
        <!-- Nhúng CSS/JS Vditor (Chỉ nên nhúng 1 lần trong thực tế, ở đây nhúng conditional cho tiện demo) -->
        <link rel="stylesheet" href="https://unpkg.com/vditor/dist/index.css" />
        <script src="https://unpkg.com/vditor/dist/index.min.js"></script>

        <script>
            document.addEventListener("DOMContentLoaded", function() {
                // Khởi tạo Vditor
                const vditor = new Vditor('<?= $uniqueId ?>', {
                    height: 360,
                    mode: 'wysiwyg', // Chế độ WYSIWYG dễ dùng cho user thường
                    toolbarConfig: { pin: true },
                    cache: { enable: false },
                    // Load giá trị ban đầu từ PHP (dùng json_encode để escape an toàn)
                    value: <?= json_encode($value ?? '') ?>,

                    // --- CẤU HÌNH TOÁN HỌC ---
                    preview: {
                        math: {
                            inlineDigit: true,
                            engine: 'KaTeX' // Sử dụng KaTeX cho nhẹ và nhanh
                        }
                    },
                    
                    // Gợi ý cho người dùng nếu Math được bật
                    placeholder: '<?= $isMath ? "Hỗ trợ gõ công thức toán học dạng LaTeX. Ví dụ: $ E=mc^2 $" : "" ?>',

                    // Sự kiện quan trọng: Khi gõ, cập nhật vào textarea ẩn
                    input: (val) => {
                        document.getElementById('<?= $uniqueId ?>_input').value = val;
                    },
                    // Khi load xong, cũng set giá trị lần đầu (phòng hờ)
                    after: () => {
                        document.getElementById('<?= $uniqueId ?>_input').value = vditor.getValue();
                    }
                });
            });
        </script>
    <?php endif; ?>
    
    <?php if(!empty($error)): ?>
        <div class="invalid-feedback d-block"><?= $error ?></div>
    <?php endif; ?>
</div>