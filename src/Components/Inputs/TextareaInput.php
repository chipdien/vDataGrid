<?php

namespace VGrid\Components\Inputs;

class TextareaInput extends AbstractInput
{
    protected int $rows = 4;
    protected bool $useVditor = false; // Cờ hiệu bật editor
    protected bool $useMath = false;

    public function rows(int $rows): self
    {
        $this->rows = $rows;
        return $this;
    }

    public function enableVditor(bool $enable = true): self
    {
        $this->useVditor = $enable;
        return $this;
    }

    /**
     * Bật hỗ trợ gõ công thức toán học (LaTeX/KaTeX)
     * Ví dụ: $E=mc^2$
     */
    public function enableMath(bool $enable = true): self
    {
        $this->useMath = $enable;
        return $this;
    }

    public function getRows(): int { return $this->rows; }

    public function isMathEnabled(): bool { return $this->useMath; }

    public function isVditorEnabled(): bool { return $this->useVditor; }

    protected function getViewPath(): string
    {
        return __DIR__ . '/../../../views/bootstrap5/inputs/textarea.php';
    }
}