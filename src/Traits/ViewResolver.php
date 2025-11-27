<?php

namespace VGrid\Traits;

trait ViewResolver
{
    /** @var string|null Đường dẫn view tùy biến của user */
    protected ?string $customView = null;

    /** @var array Dữ liệu chia sẻ cho view */
    protected array $viewData = [];

    /**
     * Cho phép User override view mặc định.
     * VD: $column->setView('path/to/my_view.php');
     */
    public function setView(string $path): self
    {
        $this->customView = $path;
        return $this;
    }

    /**
     * Hàm nội bộ để tìm và render file view.
     * * @param string $defaultViewPath Đường dẫn mặc định (trong thư viện)
     * @param array $data Dữ liệu truyền vào view
     */
    protected function resolveView(string $defaultViewPath, array $data = []): string
    {
        // 1. Ưu tiên view của User, nếu không có thì dùng Default
        $path = $this->customView ?? $defaultViewPath;

        // 2. Merge dữ liệu
        $data = array_merge($this->viewData, $data);

        // 3. Render
        if (!file_exists($path)) {
            return "<!-- View not found: {$path} -->";
        }

        ob_start();
        extract($data);
        include $path;
        return ob_get_clean();
    }
}