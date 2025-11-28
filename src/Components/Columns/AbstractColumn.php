<?php

namespace VGrid\Components\Columns;

use VGrid\Contracts\ColumnInterface;
use VGrid\Traits\ViewResolver;

abstract class AbstractColumn implements ColumnInterface
{
    use ViewResolver; // Tích hợp khả năng load view

    protected string $name;       // Tên field trong DB
    protected string $label;      // Tên hiển thị
    protected bool $sortable = false;
    protected int $filterLocation = 3; // 3 = BOTH (Ví dụ)
    protected $formatter = null; 

    public function __construct(string $name, ?string $label = null)
    {
        $this->name = $name;
        $this->label = $label ?? ucfirst($name);
    }

    // Static constructor để code gọn hơn: TextColumn::make(...)
    public static function make(string $name, ?string $label = null): static
    {
        return new static($name, $label);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function sortable(bool $enable = true): self
    {
        $this->sortable = $enable;
        return $this;
    }

    public function format(callable $callback): self
    {
        $this->formatter = $callback;
        return $this;
    }

    // Hàm render chính (Gọi từ Interface Renderable)
    // Mặc định render cell, nhưng có thể mở rộng
    public function render(array $data = []): string 
    {
        // Nếu người dùng đã set format tùy chỉnh, dùng nó ngay lập tức
        if ($this->formatter && is_callable($this->formatter)) {
            return call_user_func($this->formatter, $data);
        }
        
        return $this->renderCell($data);
    }

    // Các class con (TextColumn, StatusColumn) sẽ phải override hàm này
    // nếu muốn logic hiển thị khác biệt, hoặc dùng view mặc định ở đây.
    abstract public function renderCell(array $row): string;
    
    // Tạm thời để trống hàm filter, ta sẽ quay lại sau
    public function renderFilter(string $location): string
    {
        return ''; 
    }
}