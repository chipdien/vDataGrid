<?php
// ... (Phần kết nối DB giữ nguyên như cũ) ...
require_once __DIR__ . '/vendor/autoload.php';
use Illuminate\Database\Capsule\Manager as Capsule;
use VGrid\Grid\DataGrid;
use VGrid\Form\DataForm; // Class mới
use VGrid\Data\SqlDataProvider;
use VGrid\Resources\ClassResource;

// 1. KẾT NỐI DATABASE
$capsule = new Capsule;
$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => '103.77.175.34',
    'database'  => 'saas_centers',
    'username'  => 'saas_centers',
    'password'  => 'wnf7iNmEFkJfNW42',
    'charset'   => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix'    => '',
]);
$capsule->setAsGlobal();
$capsule->bootEloquent();

// --- ROUTING ĐƠN GIẢN ---
$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? null;

// --- HÀM HELPER ĐỂ ĐỊNH NGHĨA FIELDS (DRY: Dùng chung cho Create và Edit) ---
// function getClassFormFields() {
//     return [
//         TextInput::make('class_code', 'Mã Lớp')->required(),
//         TextInput::make('class_name', 'Tên Lớp')->required(),
        
//         // Input số tiền
//         TextInput::make('tuition_fee', 'Học phí (VND)')->type('number'),
//         TextInput::make('max_capacity', 'Sĩ số tối đa')->type('number'),
        
//         // Input chọn Ngày
//         DateInput::make('start_date', 'Ngày khai giảng'),
        
//         // Input Dropdown chọn Trạng thái
//         SelectInput::make('status', 'Trạng thái')
//             ->options([
//                 'planning' => 'Đang lập kế hoạch',
//                 'enrolling' => 'Đang tuyển sinh',
//                 'in_progress' => 'Đang diễn ra',
//                 'completed' => 'Đã kết thúc',
//                 'cancelled' => 'Đã hủy',
//             ])->required(),
            
//         // Input Textarea (ví dụ ghi chú) - Giả sử DB có cột notes
//         TextareaInput::make('notes', 'Ghi chú nội bộ')->rows(5)->enableVditor(),
//     ];
// }

// XỬ LÝ POST REQUEST (Lưu dữ liệu)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form = new DataForm();
    
    // TÙY CHỌN: Dùng form full hay form quick tùy ngữ cảnh
    $isQuickAdd = isset($_GET['quick']);
    $fields = $isQuickAdd ? ClassResource::formQuickAdd() : ClassResource::formFull();
    
    $form->setInputs($fields);
    
    try {
        if ($action === 'store') {
            $form->save('classes');
        } elseif ($action === 'update' && $id) {
            $form->save('classes', $id);
        }

        // Redirect về trang danh sách
        header('Location: index.php');
        exit;
    } catch (\Exception $e) {
        die("Lỗi lưu dữ liệu: " . $e->getMessage());
    }
}

// ... (Phần xóa ID logic giữ nguyên nếu có) ...
if ($action === 'delete' && $id) {
    Capsule::table('classes')->delete($id);
    header('Location: index.php');
    exit;
}

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <title>Demo CRUD VGrid</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="bg-light p-4">
<div class="container">

<?php if ($action === 'list'): ?>
    
    <!-- MÀN HÌNH 1: DANH SÁCH (GRID) -->
    <div class="d-flex justify-content-between mb-4">
        <h3>Danh sách Lớp</h3>
        <div>
            <a href="?action=create&quick=1" class="btn btn-outline-primary">Thêm nhanh</a>
            <a href="?action=create" class="btn btn-primary">Thêm đầy đủ</a>
        </div>
    </div>

    <?php
        // BLOCK TABLE: Định nghĩa 1 lần, dùng mãi mãi
        $grid = new DataGrid('classes');
        $grid->setDataProvider(new SqlDataProvider(Capsule::table('classes')->orderBy('id', 'desc')))
             ->setColumns(ClassResource::tableColumns()); // <--- GỌI RESOURCE
             
        echo $grid->render();
    ?>
    
    <?php
        // $query = Capsule::table('classes')->orderBy('id', 'desc');
        // $grid = new DataGrid('classes');
        // $grid->setDataProvider(new SqlDataProvider($query))
        //      ->setColumns([
        //         TextColumn::make('id', 'ID'),
        //         TextColumn::make('class_code', 'Mã Lớp')
        //             ->copyable()
        //             ->sortable(),
        //         LinkColumn::make('class_name', 'Tên Lớp')
        //             ->url(fn($row) => "/classes/detail/" . $row['id'])
        //             ->openNewTab(),

        //         //  TextColumn::make('class_name', 'Tên Lớp'),
        //         CurrencyColumn::make('tuition_fee', 'Học phí')
        //             ->symbol('₫')    // Tùy chỉnh ký hiệu
        //             ->colorize(true), // Bật tô màu: >0 xanh, <0 đỏ (Hữu ích cho cột Lợi nhuận/Công nợ)
        //         StatusColumn::make('status', 'Trạng thái')
        //             ->options([
        //                 'planning'    => ['label' => 'Đang lập KH', 'class' => 'info'],
        //                 'enrolling'   => ['label' => 'Tuyển sinh',  'class' => 'primary'],
        //                 'in_progress' => ['label' => 'Đang học',    'class' => 'success'],
        //                 'completed'   => ['label' => 'Kết thúc',    'class' => 'secondary'],
        //                 'cancelled'   => ['label' => 'Đã hủy',      'class' => 'danger'],
        //             ]),   
        //         DateTimeColumn::make('created_at', 'Ngày tạo')
        //             ->formatStr('d/m/Y'), // Chỉ hiện ngày    
        //         BooleanColumn::make('is_active', 'Active')
        //             ->format(fn($row) => $row['status'] == 'in_progress'), // Nếu đang học thì là True

        //          // Cột Hành động (Edit) - Chúng ta dùng thủ thuật TextColumn render HTML link
        //          // Giai đoạn sau sẽ có ActionColumn chuyên nghiệp hơn
        //         ActionColumn::make('actions', 'Thao tác')
        //             ->add(
        //                 // Nút Sửa (Màu xanh, Icon bút chì)
        //                 Button::make('Sửa')
        //                     ->icon('bi bi-pencil-square')
        //                     ->class('btn-outline-primary')
        //                     ->url(fn($row) => "?action=edit&id={$row['id']}")
        //             )
        //             ->add(
        //                 // Nút Xóa (Màu đỏ, Icon thùng rác, Có xác nhận)
        //                 Button::make('Hủy')
        //                     ->icon('bi bi-trash')
        //                     ->class('btn-outline-danger')
        //                     ->url(fn($row) => "?action=delete&id={$row['id']}")
        //                     ->confirm('Bạn có chắc muốn hủy lớp học này không?')
        //                     // Logic hiển thị: Chỉ hiện nút Hủy khi lớp chưa Kết thúc hoặc chưa Hủy
        //                     ->canSee(fn($row) => !in_array($row['status'], ['completed', 'cancelled']))
        //             )
        //             ->add(
        //                 // Nút Xem chi tiết (Màu xám)
        //                 Button::make('Chi tiết')
        //                     ->icon('bi bi-eye')
        //                     ->class('btn-outline-secondary')
        //                     ->url(fn($row) => "/classes/view/{$row['id']}")
        //                     ->openNewTab()
        //             )
        //      ]);
        // echo $grid->render();
    ?>

<?php elseif ($action === 'create'): ?>

    <!-- MÀN HÌNH 2: FORM THÊM MỚI -->
    <!-- DEMO GRID LAYOUT TRONG FORM -->
    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h4><?= isset($_GET['quick']) ? 'Thêm Nhanh (Ít trường)' : 'Thêm Đầy Đủ (Grid Layout)' ?></h4>
        </div>
        <div class="card-body">
            <?php
                $form = new DataForm('?action=store');
                
                // Chọn Form Definition dựa trên ngữ cảnh
                if (isset($_GET['quick'])) {
                    // Form 1: Quick Add (Tái sử dụng fieldCode, fieldName)
                    $form->setInputs(ClassResource::formQuickAdd());
                } else {
                    // Form 2: Full (Tái sử dụng + Grid 30/70)
                    $form->setInputs(ClassResource::formFull());
                }
                
                echo $form->render();
            ?>
        </div>
    </div>

<?php elseif ($action === 'edit' && $id): ?>

    <!-- MÀN HÌNH 3: FORM CHỈNH SỬA -->
    <!-- Form Edit cũng dùng chung định nghĩa Full -->
    <?php
        $classData = Capsule::table('classes')->find($id);
        $form = new DataForm('?action=update&id=' . $id);
        
        // Tái sử dụng Form Full cho màn hình Edit
        $form->setInputs(ClassResource::formFull())
             ->bindData((array)$classData);
             
        echo $form->render();
    ?>

    <!-- <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h4 class="mb-0">Chỉnh sửa Lớp #<?= $id ?></h4>
                </div>
                <div class="card-body">
                    <?php
                        $classData = Capsule::table('classes')->find($id);
                        
                        if ($classData) {
                            $form = new DataForm('?action=update&id=' . $id);
                            $form->setInputs(getClassFormFields())
                                 ->bindData((array)$classData); // Nạp data cũ
                            
                            echo $form->render();
                        } else {
                            echo "<div class='alert alert-danger'>Không tìm thấy dữ liệu.</div>";
                            echo "<a href='index.php' class='btn btn-secondary mt-3'>Quay lại</a>";
                        }
                    ?>
                </div>
            </div>
        </div>
    </div> -->

<?php endif; ?>

</div>
</body>
</html>