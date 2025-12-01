<?php

namespace VGrid\Resources;

class BaseResource
{
    // Định nghĩa tên bảng DB
    public static function getTable(): string
    {
        return '';
    }

    // Định nghĩa các trường (Fields) để tái sử dụng
    // Trả về mảng các Component đã cấu hình sẵn
    public static function fields(): array
    {
        return [];
    }
}