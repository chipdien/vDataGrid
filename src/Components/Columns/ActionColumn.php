<?php

namespace VGrid\Components\Columns;

use VGrid\Components\Actions\AbstractAction;

class ActionColumn extends AbstractColumn
{
    protected array $actions = [];

    /**
     * Thêm nút vào cột
     */
    public function add(AbstractAction $action): self
    {
        $this->actions[] = $action;
        return $this;
    }

    public function renderCell(array $row): string
    {
        $html = '<div class="btn-group" role="group">';
        
        foreach ($this->actions as $action) {
            // Mỗi action tự quyết định việc render của nó (dựa trên canSee)
            $html .= $action->render($row);
        }
        
        $html .= '</div>';
        return $html;
    }
}