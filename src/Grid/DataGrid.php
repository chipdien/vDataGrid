<?php

namespace VGrid\Grid;

use VGrid\Contracts\Renderable;
use VGrid\Components\Columns\AbstractColumn;
use VGrid\Traits\ViewResolver;

class DataGrid implements Renderable
{
    use ViewResolver;

    protected string $id;
    protected array $columns = [];
    protected array $rows = []; // Giai đoạn 1: Dùng mảng thô

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    /**
     * Thêm các cột vào bảng
     * @param AbstractColumn[] $columns
     */
    public function setColumns(array $columns): self
    {
        $this->columns = $columns;
        return $this;
    }

    /**
     * Set dữ liệu cho bảng
     */
    public function setDataSource(array $rows): self
    {
        $this->rows = $rows;
        return $this;
    }

    /**
     * Render toàn bộ bảng
     */
    public function render(array $data = []): string
    {
        // Chuẩn bị dữ liệu cho View
        $viewData = [
            'gridId'  => $this->id,
            'columns' => $this->columns,
            'rows'    => $this->rows,
        ];

        // Gọi View chính của Grid
        return $this->resolveView(
            __DIR__ . '/../../views/bootstrap5/grid.php', 
            $viewData
        );
    }
}