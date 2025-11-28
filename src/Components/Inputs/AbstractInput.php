<?php

namespace VGrid\Components\Inputs;

use VGrid\Contracts\Renderable;
use VGrid\Traits\ViewResolver;

abstract class AbstractInput implements Renderable
{
    use ViewResolver;

    protected string $name;
    protected string $label;
    protected mixed $value = null;
    protected bool $required = false;

    public function __construct(string $name, string $label)
    {
        $this->name = $name;
        $this->label = $label;
    }

    public static function make(string $name, string $label): static
    {
        return new static($name, $label);
    }

    public function required(bool $required = true): self
    {
        $this->required = $required;
        return $this;
    }

    public function setValue($value): self
    {
        $this->value = $value;
        return $this;
    }

    // Hàm render chính gọi view
    public function render(array $data = []): string
    {
        return $this->resolveView($this->getViewPath(), [
            'field' => $this,
            'value' => $this->value, // Giá trị hiện tại (dùng khi Edit)
            'error' => $data['error'] ?? null // Lỗi validation (nếu có)
        ]);
    }

    // Class con phải định nghĩa view mặc định của nó
    abstract protected function getViewPath(): string;
}