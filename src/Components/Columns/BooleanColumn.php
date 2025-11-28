<?php

namespace VGrid\Components\Columns;

class BooleanColumn extends AbstractColumn
{
    protected string $trueIcon = 'bi-check-circle-fill text-success';
    protected string $falseIcon = 'bi-x-circle-fill text-danger';

    public function icons(string $trueIcon, string $falseIcon): self
    {
        $this->trueIcon = $trueIcon;
        $this->falseIcon = $falseIcon;
        return $this;
    }

    public function renderCell(array $row): string
    {
        $value = $row[$this->name] ?? false;
        
        // Chấp nhận cả boolean true/false, số 1/0, chuỗi '1'/'0'
        $isTrue = filter_var($value, FILTER_VALIDATE_BOOLEAN);

        return $this->resolveView(
            __DIR__ . '/../../../views/bootstrap5/columns/boolean.php',
            [
                'isTrue' => $isTrue,
                'iconClass' => $isTrue ? $this->trueIcon : $this->falseIcon
            ]
        );
    }
}