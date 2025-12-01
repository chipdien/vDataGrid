<?php

namespace VGrid\Resources;

use VGrid\Components\Inputs\TextInput;
use VGrid\Components\Inputs\SelectInput;
use VGrid\Components\Inputs\DateInput;
use VGrid\Components\Inputs\TextareaInput;
use VGrid\Components\Columns\TextColumn;
use VGrid\Components\Columns\StatusColumn;
use VGrid\Components\Columns\CurrencyColumn;
use VGrid\Components\Columns\ActionColumn;
use VGrid\Components\Actions\Button;

class ClassResource extends BaseResource
{
    public static function getTable(): string { return 'classes'; }

    // --- 1. ĐỊNH NGHĨA CÁC FIELD TÁI SỬ DỤNG (Define Once) ---
    
    public static function fieldCode() {
        return TextInput::make('class_code', 'Mã Lớp')->required();
    }

    public static function fieldName() {
        return TextInput::make('class_name', 'Tên Lớp')->required();
    }

    public static function fieldFee() {
        return TextInput::make('tuition_fee', 'Học phí')->type('number');
    }

    public static function fieldStatus() {
        return SelectInput::make('status', 'Trạng thái')
            ->options([
                'planning' => 'Lập kế hoạch',
                'enrolling' => 'Tuyển sinh',
                'in_progress' => 'Đang học',
                'completed' => 'Kết thúc',
                'cancelled' => 'Đã hủy',
            ]);
    }

    // --- 2. ĐỊNH NGHĨA FORM (Compose Many Times) ---

    // Form thêm nhanh (Quick Add): Chỉ cần Mã và Tên, nằm trên 1 hàng (50-50)
    public static function formQuickAdd(): array
    {
        return [
            self::fieldCode()->span(6), // Tái sử dụng và override layout
            self::fieldName()->span(6),
        ];
    }

    // Form đầy đủ (Full Edit): Có đủ trường, chia cột phức tạp
    public static function formFull(): array
    {
        return [
            self::fieldCode()->span(4),
            self::fieldName()->span(8), // 30% - 70% layout
            
            self::fieldFee()->span(6),
            DateInput::make('start_date', 'Khai giảng')->span(6),
            
            self::fieldStatus()->span(12),
            
            TextareaInput::make('notes', 'Ghi chú')
                ->enableVditor()
                ->span(12)
        ];
    }

    // --- 3. ĐỊNH NGHĨA TABLE (Block Table) ---

    public static function tableColumns(): array
    {
        return [
            TextColumn::make('id', '#ID'),
            TextColumn::make('class_code', 'Mã'),
            TextColumn::make('class_name', 'Tên Lớp'),
            CurrencyColumn::make('tuition_fee', 'Học phí')->symbol('₫')->colorize(),
            StatusColumn::make('status', 'Trạng thái')
                ->options([
                    'planning' => ['class' => 'info'],
                    'enrolling' => ['class' => 'primary'],
                    'in_progress' => ['class' => 'success'],
                    'completed' => ['class' => 'secondary'],
                    'cancelled' => ['class' => 'danger'],
                ]),
            
            // Cột Action tiêu chuẩn
            ActionColumn::make('actions', 'Thao tác')
                ->add(Button::make('Sửa')->icon('bi bi-pencil')->class('btn-outline-primary')->url(fn($r)=>"?action=edit&id={$r['id']}"))
                ->add(Button::make('Xóa')->icon('bi bi-trash')->class('btn-outline-danger')->confirm()->url(fn($r)=>"?action=delete&id={$r['id']}"))
        ];
    }
}