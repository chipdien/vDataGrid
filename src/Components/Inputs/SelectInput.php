<?php

namespace VGrid\Components\Inputs;

class SelectInput extends AbstractInput
{
    protected array $options = [];

    /**
     * Nạp danh sách options cho select.
     * Format: ['value' => 'Label']
     * VD: options(['active' => 'Hoạt động', 'inactive' => 'Khóa'])
     */
    public function options(array $options): self
    {
        $this->options = $options;
        return $this;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    protected function getViewPath(): string
    {
        // Đường dẫn tới view (Cần tạo file này ở bước sau)
        return __DIR__ . '/../../../views/bootstrap5/inputs/select.php';
    }
}