<?php

namespace VGrid\Contracts;

interface Renderable
{
    /**
     * Render đối tượng thành chuỗi HTML.
     *
     * @param array $data Dữ liệu truyền vào view (nếu có)
     * @return string
     */
    public function render(array $data = []): string;
}