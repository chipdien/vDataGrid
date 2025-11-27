<?php

namespace VGrid\Components\Columns;

class TextColumn extends AbstractColumn
{
    /**
     * Override hàm renderCell của cha.
     * Nhiệm vụ: Lấy value và gọi view.
     */
    public function renderCell(array $row): string
    {
        // 1. Lấy giá trị từ mảng dữ liệu (Safely)
        $value = $row[$this->name] ?? '';

        // 2. Gọi ViewResolver để render
        // Mặc định sẽ tìm file: views/bootstrap5/columns/text.php
        // Lưu ý: Đường dẫn view nên để tương đối hoặc dùng config, ở đây tôi hardcode mẫu
        return $this->resolveView(
            __DIR__ . '/../../../views/bootstrap5/columns/text.php', 
            [
                'value' => $value,
                'row'   => $row
            ]
        );
    }
}