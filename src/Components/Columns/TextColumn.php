<?php

namespace VGrid\Components\Columns;

class TextColumn extends AbstractColumn
{
    protected bool $copyable = false;
    protected int $limit = 0; // Giới hạn ký tự hiển thị (0 = không giới hạn)

    public function copyable(bool $enable = true): self
    {
        $this->copyable = $enable;
        return $this;
    }

    public function limit(int $chars): self
    {
        $this->limit = $chars;
        return $this;
    }

    public function renderCell(array $row): string
    {
        $value = $row[$this->name] ?? '';
        
        // Xử lý cắt chuỗi nếu quá dài
        $displayValue = $value;
        if ($this->limit > 0 && mb_strlen($value) > $this->limit) {
            $displayValue = mb_substr($value, 0, $this->limit) . '...';
        }

        return $this->resolveView(
            __DIR__ . '/../../../views/bootstrap5/columns/text.php',
            [
                'value' => $value, // Giá trị gốc (để copy)
                'displayValue' => $displayValue, // Giá trị hiển thị
                'copyable' => $this->copyable,
                'row'   => $row
            ]
        );
    }
}