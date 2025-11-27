<?php
/**
 * @var string $gridId
 * @var \VGrid\Components\Columns\AbstractColumn[] $columns
 * @var array $rows
 */
?>
<div class="card shadow-sm border-0" id="<?= $gridId ?>">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <?php foreach ($columns as $col): ?>
                        <th class="py-3 fw-bold text-uppercase text-secondary small" 
                            style="letter-spacing: 0.5px;">
                            <?= htmlspecialchars($col->getLabel()) ?>
                        </th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($rows)): ?>
                    <tr>
                        <td colspan="<?= count($columns) ?>" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            Không có dữ liệu hiển thị
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($rows as $row): ?>
                        <tr>
                            <?php foreach ($columns as $col): ?>
                                <td class="py-3">
                                    <!-- Cột tự render nội dung của nó -->
                                    <?= $col->render($row) ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>