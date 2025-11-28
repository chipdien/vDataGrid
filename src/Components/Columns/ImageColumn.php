<?php

namespace VGrid\Components\Columns;

class ImageColumn extends AbstractColumn
{
    protected int $width = 50;
    protected int $height = 50;
    protected bool $rounded = false;
    protected bool $circular = false;
    protected string $fallback = 'https://via.placeholder.com/50?text=IMG'; // Ảnh mặc định nếu null

    public function size(int $width, int $height): self
    {
        $this->width = $width;
        $this->height = $height;
        return $this;
    }

    public function rounded(): self { $this->rounded = true; return $this; }
    public function circular(): self { $this->circular = true; return $this; }

    public function renderCell(array $row): string
    {
        $src = $row[$this->name] ?? '';
        
        if (empty($src)) $src = $this->fallback;

        // Xử lý class CSS
        $classes = 'img-fluid border';
        if ($this->circular) $classes .= ' rounded-circle';
        elseif ($this->rounded) $classes .= ' rounded';

        return $this->resolveView(
            __DIR__ . '/../../../views/bootstrap5/columns/image.php',
            [
                'src' => $src,
                'width' => $this->width,
                'height' => $this->height,
                'classes' => $classes,
                'alt' => $this->label
            ]
        );
    }
}