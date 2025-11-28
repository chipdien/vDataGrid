<?php

namespace VGrid\Form;

use VGrid\Contracts\Renderable;
use VGrid\Components\Inputs\AbstractInput;
use VGrid\Traits\ViewResolver;
use Illuminate\Database\Capsule\Manager as DB;

class DataForm implements Renderable
{
    use ViewResolver;

    protected string $actionUrl;
    protected string $method = 'POST';
    protected array $inputs = [];
    protected array $data = []; // Dữ liệu model (dùng cho Edit)
    protected array $errors = []; // Lỗi validation

    public function __construct(string $actionUrl = '')
    {
        $this->actionUrl = $actionUrl;
    }

    public function setInputs(array $inputs): self
    {
        $this->inputs = $inputs;
        return $this;
    }

    /**
     * Nạp dữ liệu vào form (Dùng cho chức năng Edit)
     */
    public function bindData(array $data): self
    {
        $this->data = $data;
        // Điền value vào từng input
        foreach ($this->inputs as $input) {
            if (isset($data[$input->getName()])) {
                $input->setValue($data[$input->getName()]);
            }
        }
        return $this;
    }

    public function render(array $params = []): string
    {
        return $this->resolveView(
            __DIR__ . '/../../views/bootstrap5/form.php', 
            [
                'action' => $this->actionUrl,
                'method' => $this->method,
                'inputs' => $this->inputs,
                'data'   => $this->data
            ]
        );
    }
    
    // Helper để xử lý lưu dữ liệu cơ bản
    public function save(string $table, ?int $id = null): bool
    {
        $payload = [];
        foreach ($this->inputs as $input) {
            // Lấy dữ liệu từ $_POST
            $val = $_POST[$input->name] ?? null;
            if ($val !== null) {
                $payload[$input->name] = $val;
            }
        }

        if ($id) {
            // Update
            return DB::table($table)->where('id', $id)->update($payload);
        } else {
            // Insert
            return DB::table($table)->insert($payload);
        }
    }
}