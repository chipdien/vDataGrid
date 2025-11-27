<?php

/**
 * File: DataProviderInterface.php
 * Path: src/Contracts/DataProviderInterface.php
 * Mô tả: Interface quy định các phương thức bắt buộc cho mọi Data Provider (SQL, Array, API...).
 */

namespace VGrid\Contracts;

interface DataProviderInterface
{
    /**
     * Lấy dữ liệu cho trang hiện tại
     * @return array Danh sách các dòng dữ liệu (Rows)
     */
    public function getData(): array;

    /**
     * Lấy tổng số bản ghi (để tính phân trang)
     */
    public function getTotalCount(): int;

    /**
     * Thiết lập trang hiện tại và số dòng mỗi trang
     */
    public function paginate(int $page, int $perPage): self;
    
    /**
     * Sắp xếp dữ liệu
     */
    public function sort(string $field, string $direction): self;
}