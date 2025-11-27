<?php

/**
 * File: SqlDataProvider.php
 * Path: src/Data/SqlDataProvider.php
 * Mô tả: Adapter sử dụng Illuminate Query Builder để truy vấn Database an toàn.
 */

namespace VGrid\Data;

use VGrid\Contracts\DataProviderInterface;
use Illuminate\Database\Query\Builder;

class SqlDataProvider implements DataProviderInterface
{
    protected Builder $query;
    protected int $page = 1;
    protected int $perPage = 10;

    public function __construct(Builder $query)
    {
        $this->query = $query;
    }

    public function sort(string $field, string $direction): self
    {
        // Chống SQL Injection bằng cách chỉ cho phép ký tự hợp lệ
        if (preg_match('/^[a-z0-9_]+$/i', $field) && in_array(strtoupper($direction), ['ASC', 'DESC'])) {
            $this->query->orderBy($field, $direction);
        }
        return $this;
    }

    public function paginate(int $page, int $perPage): self
    {
        $this->page = max(1, $page);
        $this->perPage = $perPage;
        return $this;
    }

    public function getTotalCount(): int
    {
        // Clone query để đếm mà không ảnh hưởng query chính (bỏ limit/offset)
        return $this->query->getCountForPagination();
    }

    public function getData(): array
    {
        // Tính toán offset tự động nhờ forPage()
        $items = $this->query->forPage($this->page, $this->perPage)->get();
        
        // Chuyển đổi Collection object thành Array thuần
        return $items->map(fn($item) => (array) $item)->toArray();
    }
}