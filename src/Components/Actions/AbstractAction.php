<?php

namespace VGrid\Components\Actions;

use VGrid\Contracts\Renderable;
use VGrid\Traits\ViewResolver;

abstract class AbstractAction implements Renderable
{
    use ViewResolver;

    protected string $label;
    protected $visibleCallback = null; // Logic ẩn hiện (Permissions)

    public function __construct(string $label)
    {
        $this->label = $label;
    }

    public static function make(string $label): static
    {
        return new static($label);
    }

    /**
     * Điều kiện hiển thị nút bấm.
     * VD: ->canSee(fn($row) => $row['status'] == 'pending')
     */
    public function canSee(callable $callback): self
    {
        $this->visibleCallback = $callback;
        return $this;
    }

    /**
     * Kiểm tra xem nút có được render cho dòng này không
     */
    public function shouldRender(array $row): bool
    {
        if (is_callable($this->visibleCallback)) {
            return call_user_func($this->visibleCallback, $row);
        }
        return true;
    }

    // Các class con phải implement hàm này để trả về HTML
    abstract public function render(array $row = []): string;
}