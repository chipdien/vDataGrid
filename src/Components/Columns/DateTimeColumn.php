<?php

namespace VGrid\Components\Columns;

class DateTimeColumn extends AbstractColumn
{
    protected string $format = 'd/m/Y H:i'; // Default format

    /**
     * Cấu hình định dạng ngày tháng (theo chuẩn PHP date format)
     * VD: 'd/m/Y' (Ngày/Tháng/Năm), 'H:i' (Giờ:Phút)
     */
    public function formatStr(string $format): self
    {
        $this->format = $format;
        return $this;
    }

    public function renderCell(array $row): string
    {
        $value = $row[$this->name] ?? null;

        if (!$value) return '';

        // Chuyển đổi string sang timestamp an toàn
        $timestamp = is_numeric($value) ? $value : strtotime($value);
        
        if (!$timestamp) return $value; // Fallback nếu không parse được

        $formattedValue = date($this->format, $timestamp);

        return $this->resolveView(
            __DIR__ . '/../../../views/bootstrap5/columns/datetime.php',
            [
                'value' => $formattedValue,
                'row' => $row
            ]
        );
    }
}