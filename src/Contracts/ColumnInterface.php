<?php

namespace VGrid\Contracts;

interface ColumnInterface extends Renderable
{
    public function getName(): string;
    public function getLabel(): string;
    
    /**
     * Render ô dữ liệu (Cell) cho một dòng cụ thể.
     */
    public function renderCell(array $row): string;

    /**
     * Render ô input lọc (Filter) dựa trên vị trí (inline/form).
     */
    public function renderFilter(string $location): string;
}