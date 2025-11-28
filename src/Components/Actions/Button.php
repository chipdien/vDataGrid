<?php

namespace VGrid\Components\Actions;

class Button extends AbstractAction
{
    protected $urlCallback;
    protected string $icon = '';
    protected string $class = 'btn-light'; // Mặc định màu trắng/xám nhẹ
    protected ?string $confirmMessage = null; // Thông báo xác nhận
    protected string $target = '_self';

    /**
     * URL đích khi click vào nút
     */
    public function url(callable $callback): self
    {
        $this->urlCallback = $callback;
        return $this;
    }

    public function icon(string $icon): self
    {
        $this->icon = $icon;
        return $this;
    }

    /**
     * Style nút (btn-primary, btn-danger, btn-sm...)
     */
    public function class(string $class): self
    {
        $this->class = $class;
        return $this;
    }

    /**
     * Thêm hộp thoại xác nhận (Javascript confirm)
     */
    public function confirm(string $message = 'Bạn có chắc chắn muốn thực hiện hành động này?'): self
    {
        $this->confirmMessage = $message;
        return $this;
    }

    public function openNewTab(): self
    {
        $this->target = '_blank';
        return $this;
    }

    public function render(array $row = []): string
    {
        // 1. Kiểm tra quyền hiển thị
        if (!$this->shouldRender($row)) {
            return '';
        }

        // 2. Tính toán URL
        $url = '#';
        if (is_callable($this->urlCallback)) {
            $url = call_user_func($this->urlCallback, $row);
        }

        // 3. Render View
        return $this->resolveView(
            __DIR__ . '/../../../views/bootstrap5/actions/button.php',
            [
                'label'   => $this->label,
                'url'     => $url,
                'icon'    => $this->icon,
                'class'   => $this->class,
                'confirm' => $this->confirmMessage,
                'target'  => $this->target
            ]
        );
    }
}