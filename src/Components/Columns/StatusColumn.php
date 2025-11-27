<?php

namespace VGrid\Components\Columns;

class StatusColumn extends AbstractColumn
{
    protected array $options = [];

    /**
     * Cấu hình map giá trị sang màu sắc và nhãn hiển thị
     * Ví dụ: ['active' => ['label' => 'Hoạt động', 'class' => 'success']]
     */
    public function options(array $options): self
    {
        $this->options = $options;
        return $this;
    }

    public function renderCell(array $row): string
    {
        $value = $row[$this->name] ?? '';
        
        // Tìm cấu hình cho giá trị này
        $config = $this->options[$value] ?? [
            'label' => $value, 
            'class' => 'secondary' // Màu mặc định
        ];

        return $this->resolveView(
            __DIR__ . '/../../../views/bootstrap5/columns/status.php', 
            [
                'value' => $value,
                'label' => $config['label'] ?? $value,
                'class' => $config['class'] ?? 'secondary',
                'row'   => $row
            ]
        );
    }
}