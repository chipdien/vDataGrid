<?php

namespace VGrid\Components\Inputs;

class DateInput extends AbstractInput
{
    protected function getViewPath(): string
    {
        return __DIR__ . '/../../../views/bootstrap5/inputs/date.php';
    }
}