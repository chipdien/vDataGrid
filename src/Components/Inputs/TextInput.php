<?php

namespace VGrid\Components\Inputs;

class TextInput extends AbstractInput
{
    protected string $type = 'text'; // text, password, email...

    public function type(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    protected function getViewPath(): string
    {
        return __DIR__ . '/../../../views/bootstrap5/inputs/text.php';
    }

    public function getType(): string { return $this->type; }
}