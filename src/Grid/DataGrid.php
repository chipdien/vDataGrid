<?php

/**
 * File: DataGrid.php
 * Path: src/Grid/DataGrid.php
 * Mô tả: Class chính để khởi tạo bảng, quản lý cột và điều phối luồng dữ liệu.
 */

namespace VGrid\Grid;

use VGrid\Contracts\Renderable;
use VGrid\Contracts\DataProviderInterface;
use VGrid\Components\Columns\AbstractColumn;
use VGrid\Traits\ViewResolver;

class DataGrid implements Renderable
{
    use ViewResolver;

    protected string $id;
    protected array $columns = [];
    protected array $rows = []; // Dữ liệu tĩnh (nếu có)
    protected ?DataProviderInterface $dataProvider = null; // Dữ liệu động
    
    // Config mặc định
    protected int $perPage = 10;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    /**
     * Thêm các cột vào bảng
     */
    public function setColumns(array $columns): self
    {
        $this->columns = $columns;
        return $this;
    }

    /**
     * Set dữ liệu tĩnh (Mảng)
     */
    public function setDataSource(array $rows): self
    {
        $this->rows = $rows;
        return $this;
    }

    /**
     * Set dữ liệu động (Database/API)
     */
    public function setDataProvider(DataProviderInterface $provider): self
    {
        $this->dataProvider = $provider;
        return $this;
    }

    /**
     * Xử lý Request từ URL (Page, Sort)
     * Đây là State Manager thu nhỏ
     */
    protected function handleRequest(): void
    {
        if (!$this->dataProvider) return;

        // 1. Lấy tham số từ URL
        // Ví dụ: grid_id_page, grid_id_sort
        $pageKey = $this->id . '_page';
        $page = (int)($_GET[$pageKey] ?? 1);

        // 2. Đẩy xuống DataProvider
        $this->dataProvider->paginate($page, $this->perPage);
        
        // (Tạm thời chưa xử lý Sort ở bước này)
    }

    /**
     * Render toàn bộ bảng
     */
    public function render(array $data = []): string
    {
        // 1. Xử lý logic trước khi vẽ
        $this->handleRequest();

        // 2. Lấy dữ liệu cuối cùng
        $finalRows = $this->rows;
        if ($this->dataProvider) {
            $finalRows = $this->dataProvider->getData();
            // TODO: Lấy thêm $totalCount để vẽ phân trang sau này
        }

        // 3. Chuẩn bị dữ liệu cho View
        $viewData = [
            'gridId'  => $this->id,
            'columns' => $this->columns,
            'rows'    => $finalRows,
        ];

        return $this->resolveView(
            __DIR__ . '/../../views/bootstrap5/grid.php', 
            $viewData
        );
    }
}