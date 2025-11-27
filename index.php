<?php

/**
 * File: index.php
 * Mô tả: Demo VGrid với bảng 'classes' trong database 'saas_centers'.
 */

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;
use VGrid\Grid\DataGrid;
use VGrid\Data\SqlDataProvider;
use VGrid\Components\Columns\TextColumn;
use VGrid\Components\Columns\StatusColumn;

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

// 2. SEED DATA MẪU (Chỉ chạy nếu bảng classes trống)
// Lưu ý: Tôi sẽ tạo dữ liệu giả cho Tenants và Courses để thỏa mãn Khóa Ngoại
try {
    if (Capsule::table('classes')->count() == 0) {
        Capsule::schema()->disableForeignKeyConstraints(); // Tắt check FK để seed nhanh

        // Tạo Tenant mẫu
        $tenantId = Capsule::table('tenants')->insertGetId([
            'name' => 'Demo Center', 'slug' => 'demo', 'code' => 'DEMO'
        ]);

        // Tạo Subject mẫu
        $subjectId = Capsule::table('subjects')->insertGetId([
            'tenant_id' => $tenantId, 'subject_code' => 'MATH', 'subject_name' => 'Toán Học'
        ]);

        // Tạo Course mẫu
        $courseId = Capsule::table('courses')->insertGetId([
            'tenant_id' => $tenantId, 'subject_id' => $subjectId, 
            'course_code' => 'MATH-10', 'course_name' => 'Toán Lớp 10'
        ]);

        // Tạo 30 Lớp học mẫu
        $statuses = ['planning', 'enrolling', 'in_progress', 'completed', 'cancelled'];
        $data = [];
        for ($i = 1; $i <= 30; $i++) {
            $data[] = [
                'tenant_id' => $tenantId,
                'course_id' => $courseId,
                'class_code' => 'CLS-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'class_name' => 'Lớp Toán K' . rand(10, 12) . '-A' . $i,
                'academic_year' => '2024-2025',
                'start_date' => date('Y-m-d', strtotime("+$i days")),
                'max_capacity' => 20,
                'current_enrollment' => rand(0, 20),
                'tuition_fee' => rand(20, 50) * 100000, // 2tr - 5tr
                'status' => $statuses[array_rand($statuses)],
                'created_at' => date('Y-m-d H:i:s')
            ];
        }
        Capsule::table('classes')->insert($data);
        
        Capsule::schema()->enableForeignKeyConstraints(); // Bật lại FK
        echo "<div class='alert alert-success m-3'>Đã seed dữ liệu mẫu cho bảng classes!</div>";
    }
} catch (\Exception $e) {
    die("Lỗi kết nối/seed data: " . $e->getMessage());
}

// 3. KHỞI TẠO GRID
try {
    // Query Builder: Lấy dữ liệu và Format tiền tệ ngay trong SQL cho tiện (hoặc dùng MoneyColumn sau này)
    $query = Capsule::table('classes')
        ->select([
            'id', 
            'class_code', 
            'class_name', 
            'academic_year', 
            'current_enrollment', 
            'max_capacity',
            'tuition_fee',
            'status',
            'start_date'
        ])
        ->orderBy('id', 'desc');

    $dataProvider = new SqlDataProvider($query);

    $grid = new DataGrid('classes_grid');
    $grid->setDataProvider($dataProvider)
         ->setColumns([
             TextColumn::make('id', '#ID'),
             
             TextColumn::make('class_code', 'Mã Lớp')
                ->sortable(),

             TextColumn::make('class_name', 'Tên Lớp')
                ->sortable(),

             // Custom hiển thị sĩ số: Hiện tại / Tối đa
             // Tạm dùng TextColumn nhưng có thể override view nếu muốn đẹp hơn
             TextColumn::make('current_enrollment', 'Sĩ số'),

             TextColumn::make('tuition_fee', 'Học phí')
                ->sortable(), // Sẽ hiển thị số thô, giai đoạn sau ta làm MoneyColumn

             // SỬ DỤNG STATUS COLUMN MỚI
             StatusColumn::make('status', 'Trạng thái')
                ->options([
                    'planning'    => ['label' => 'Đang lập kế hoạch', 'class' => 'info'],
                    'enrolling'   => ['label' => 'Đang tuyển sinh',   'class' => 'primary'],
                    'in_progress' => ['label' => 'Đang diễn ra',      'class' => 'success'],
                    'completed'   => ['label' => 'Đã kết thúc',       'class' => 'secondary'],
                    'cancelled'   => ['label' => 'Đã hủy',            'class' => 'danger'],
                ]),

             TextColumn::make('start_date', 'Ngày bắt đầu'),
         ]);

} catch (\Exception $e) {
    die("Lỗi VGrid: " . $e->getMessage());
}

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Lớp Học (Classes Demo)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Thêm Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background: #f4f6f9; font-family: 'Segoe UI', sans-serif; }
        .grid-container { padding: 40px; }
        .card { border-radius: 12px; border: none; box-shadow: 0 2px 12px rgba(0,0,0,0.08); }
        .table thead th { font-weight: 600; color: #6c757d; }
    </style>
</head>
<body>
    <div class="container grid-container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-dark mb-0">Danh sách Lớp Học</h2>
                <p class="text-muted">Quản lý các lớp học trong hệ thống SaaS Centers</p>
            </div>
            <button class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Thêm lớp mới
            </button>
        </div>

        <!-- Render Grid -->
        <?= $grid->render() ?>

        <!-- Pagination Controls (Manual Test) -->
        <div class="d-flex justify-content-between align-items-center mt-3 p-3 bg-white rounded shadow-sm">
            <span class="text-muted small">
                Trang <?= $_GET['classes_grid_page'] ?? 1 ?> 
                (Tổng: <?= Capsule::table('classes')->count() ?> lớp)
            </span>
            <div class="btn-group">
                <a href="?classes_grid_page=1" class="btn btn-sm btn-outline-secondary">1</a>
                <a href="?classes_grid_page=2" class="btn btn-sm btn-outline-secondary">2</a>
                <a href="?classes_grid_page=3" class="btn btn-sm btn-outline-secondary">3</a>
            </div>
        </div>
    </div>
</body>
</html>