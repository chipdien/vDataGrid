<?php

namespace VGrid\Components\Columns;

class CurrencyColumn extends AbstractColumn
{
    protected string $symbol = 'đ';
    protected bool $colorize = false; // Có tô màu theo giá trị không?

    /**
     * Cấu hình ký hiệu tiền tệ (đ, $, ¥)
     */
    public function symbol(string $symbol): self
    {
        $this->symbol = $symbol;
        return $this;
    }

    /**
     * Bật chế độ tô màu: Dương (Xanh), Âm (Đỏ), 0 (Xám)
     */
    public function colorize(bool $enable = true): self
    {
        $this->colorize = $enable;
        return $this;
    }

    public function renderCell(array $row): string
    {
        $value = $row[$this->name] ?? 0;
        $numericValue = (float)$value;

        // 1. Format số: 1000000 -> 1.000.000
        // Trong thực tế có thể cấu hình decimals, dec_point, thousands_sep
        $formatted = number_format($numericValue, 0, ',', '.') . ' ' . $this->symbol;

        // 2. Xác định màu sắc (nếu bật colorize)
        $colorClass = '';
        if ($this->colorize) {
            if ($numericValue > 0) $colorClass = 'text-success fw-bold';
            elseif ($numericValue < 0) $colorClass = 'text-danger fw-bold';
            else $colorClass = 'text-muted';
        }

        // 3. Gọi View
        return $this->resolveView(
            __DIR__ . '/../../../views/bootstrap5/columns/currency.php',
            [
                'value' => $formatted,
                'colorClass' => $colorClass,
                'row' => $row
            ]
        );
    }
}