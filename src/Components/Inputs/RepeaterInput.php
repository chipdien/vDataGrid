<?php

namespace VGrid\Components\Inputs;

class RepeaterInput extends AbstractInput
{
    protected array $schema = [];
    protected string $addButtonLabel = 'Thêm dòng';

    /**
     * Định nghĩa các field con sẽ lặp lại.
     * VD: schema([ TextInput::make('day'), TextInput::make('time') ])
     */
    public function schema(array $inputs): self
    {
        $this->schema = $inputs;
        return $this;
    }

    public function setAddButtonLabel(string $label): self
    {
        $this->addButtonLabel = $label;
        return $this;
    }

    public function getSchema(): array
    {
        return $this->schema;
    }

    public function getAddButtonLabel(): string
    {
        return $this->addButtonLabel;
    }

    protected function getViewPath(): string
    {
        return __DIR__ . '/../../../views/bootstrap5/inputs/repeater.php';
    }
}