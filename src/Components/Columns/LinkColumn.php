<?php

namespace VGrid\Components\Columns;

class LinkColumn extends AbstractColumn
{
    protected $urlCallback = null;
    protected string $target = '_self';
    protected bool $underline = false;

    /**
     * Callback tạo URL động.
     * VD: ->url(fn($row) => '/students/' . $row['id'])
     */
    public function url(callable $callback): self
    {
        $this->urlCallback = $callback;
        return $this;
    }

    public function openNewTab(): self
    {
        $this->target = '_blank';
        return $this;
    }

    public function renderCell(array $row): string
    {
        $value = $row[$this->name] ?? '';
        
        // Tính toán URL
        $href = '#';
        if ($this->urlCallback && is_callable($this->urlCallback)) {
            $href = call_user_func($this->urlCallback, $row);
        }

        return $this->resolveView(
            __DIR__ . '/../../../views/bootstrap5/columns/link.php',
            [
                'value' => $value,
                'href' => $href,
                'target' => $this->target
            ]
        );
    }
}