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

    // Mặc định chiếm 12/12 cột (100% width)
    protected int $colSpan = 12; 

    // Biến tạm để lưu tên đã bị override (dùng cho repeater)
    protected ?string $overriddenName = null;

    public function __construct(string $name, string $label)
    {
        $this->name = $name;
        $this->label = $label;
    }

    public static function make(string $name, string $label): static
    {
        return new static($name, $label);
    }

    /**
     * Cho phép đổi tên field tạm thời (Dùng cho Repeater render template)
     */
    public function overrideName(string $newName): self
    {
        $this->overriddenName = $newName;
        return $this;
    }

    public function getName(): string
    {
        // Ưu tiên trả về tên đã override nếu có
        return $this->overriddenName ?? $this->name;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }

    /**
     * MAGIC METHOD QUAN TRỌNG
     * Cho phép View truy cập $field->label, $field->required
     * dù chúng là property protected.
     */
    public function __get($prop)
    {
        if (property_exists($this, $prop)) {
            return $this->$prop;
        }
        return null;
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

    /**
     * Cấu hình độ rộng cột (Hệ thống 12 cột của Bootstrap).
     * ->span(6) = 50%
     * ->span(4) = 33%
     * ->span(3) = 25%
     */
    public function span(int $span): self
    {
        // Đảm bảo trong khoảng 1-12
        $this->colSpan = max(1, min(12, $span));
        return $this;
    }

    public function getColSpan(): int
    {
        return $this->colSpan;
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