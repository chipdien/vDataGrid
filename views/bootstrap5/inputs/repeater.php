<?php
/** @var \VGrid\Components\Inputs\RepeaterInput $field */
/** @var array|null $value Dữ liệu mảng các dòng đã lưu (nếu edit) */

$repeaterId = 'repeater_' . $field->getName() . '_' . uniqid();
$schema = $field->getSchema();

// Đảm bảo value là mảng để loop
$rows = is_array($value) ? $value : [];
// Nếu chưa có dữ liệu, tạo sẵn 1 dòng trống (tùy chọn)
if (empty($rows)) {
    $rows = [[]]; 
}
?>

<div class="mb-4" id="<?= $repeaterId ?>">
    <label class="form-label fw-bold d-block mb-2">
        <?= htmlspecialchars($field->getLabel()) ?>
        <?php if($field->isRequired()): ?> <span class="text-danger">*</span> <?php endif; ?>
    </label>

    <div class="repeater-container d-flex flex-column gap-3">
        <?php 
        // Vòng lặp render các dòng dữ liệu hiện có (Server-side render)
        foreach ($rows as $index => $rowData): 
        ?>
            <div class="card repeater-item bg-light border">
                <div class="card-body position-relative pt-4">
                    <!-- Nút xóa dòng -->
                    <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2" 
                            onclick="removeRepeaterItem(this)">
                        <i class="bi bi-x-lg"></i>
                    </button>

                    <div class="row g-3">
                        <?php foreach ($schema as $subInput): ?>
                            <?php 
                                // Clone object để không ảnh hưởng schema gốc
                                $inputClone = clone $subInput; 
                                
                                // QUAN TRỌNG: Rename input thành dạng array nested
                                // VD: schedules[0][day_of_week]
                                $originalName = $subInput->getName();
                                $newName = $field->getName() . "[$index][$originalName]";
                                
                                // Set value cho input con từ data dòng hiện tại
                                $subValue = $rowData[$originalName] ?? null;
                                $inputClone->setValue($subValue);
                                
                                // Render input con
                                // Lưu ý: Cần can thiệp vào AbstractInput để hỗ trợ setName() public
                                // Ở đây ta dùng Reflection hoặc sửa AbstractInput (xem bước 3 bên dưới)
                                $inputClone->overrideName($newName); 
                            ?>
                            <div class="col-12 col-md-<?= method_exists($inputClone, 'getColSpan') ? $inputClone->getColSpan() : 12 ?>">
                                <?= $inputClone->render() ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Nút thêm dòng -->
    <button type="button" class="btn btn-outline-primary mt-2" onclick="addRepeaterItem('<?= $repeaterId ?>')">
        <i class="bi bi-plus-circle me-1"></i> <?= htmlspecialchars($field->getAddButtonLabel()) ?>
    </button>

    <!-- TEMPLATE ẨN DÙNG ĐỂ CLONE JAVASCRIPT -->
    <template id="<?= $repeaterId ?>_template">
        <div class="card repeater-item bg-light border">
            <div class="card-body position-relative pt-4">
                <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2" onclick="removeRepeaterItem(this)">
                    <i class="bi bi-x-lg"></i>
                </button>
                <div class="row g-3">
                    <?php foreach ($schema as $subInput): ?>
                        <div class="col-12 col-md-<?= method_exists($subInput, 'getColSpan') ? $subInput->getColSpan() : 12 ?>">
                            <?php 
                                // Render input ở trạng thái "template"
                                // Tên sẽ được JS thay thế placeholder INDEX
                                $inputClone = clone $subInput;
                                $inputClone->overrideName($field->getName() . "[INDEX][" . $subInput->getName() . "]");
                                $inputClone->setValue(null); // Reset value
                                echo $inputClone->render();
                            ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </template>
</div>

<script>
    // Hàm JS xử lý thêm dòng
    if (typeof addRepeaterItem === 'undefined') {
        window.addRepeaterItem = function(containerId) {
            const container = document.querySelector('#' + containerId + ' .repeater-container');
            const template = document.getElementById(containerId + '_template');
            
            // Tính toán index mới dựa trên số lượng item hiện có
            const newIndex = container.querySelectorAll('.repeater-item').length;
            
            // Clone template content
            const clone = template.content.cloneNode(true);
            
            // Thay thế placeholder INDEX bằng số index thực
            // Cách này đơn giản nhưng hiệu quả cho input name
            const html = clone.querySelector('.repeater-item').innerHTML;
            const newHtml = html.replace(/\[INDEX\]/g, '[' + newIndex + ']');
            
            // Tạo div wrapper và append vào
            const div = document.createElement('div');
            div.className = 'card repeater-item bg-light border';
            div.innerHTML = newHtml;
            
            container.appendChild(div);
            
            // Trigger event nếu có init JS cho input con (như Vditor, Datepicker)
            // (Phần nâng cao: Cần re-init JS plugin cho các field mới thêm)
        }

        window.removeRepeaterItem = function(btn) {
            if (confirm('Bạn có chắc muốn xóa dòng này?')) {
                btn.closest('.repeater-item').remove();
                // (Optional) Re-index lại name attributes nếu cần thiết để đảm bảo mảng liên tục
            }
        }
    }
</script>