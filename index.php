<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VGrid Library Demo</title>
    
    <!-- Nhúng Bootstrap 5 CDN (Vì giao diện mặc định của thư viện dùng BS5) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body { background-color: #f8f9fa; }
        .demo-container { max-width: 1000px; margin: 50px auto; }
    </style>
</head>
<body>

    <div class="container demo-container">
        <div class="mb-4 text-center">
            <h1 class="display-6 fw-bold text-primary">VGrid System Demo</h1>
            <p class="text-muted">Hệ thống DataGrid PHP chuẩn Enterprise (Giai đoạn 1: Foundation)</p>
        </div>

        <?php
        // 1. Nạp Autoload của Composer (BẮT BUỘC)
        // Đảm bảo bạn đã chạy "composer dump-autoload"
        if (file_exists(__DIR__ . '/vendor/autoload.php')) {
            require_once __DIR__ . '/vendor/autoload.php';
        } else {
            die("<div class='alert alert-danger'>Lỗi: Chưa tìm thấy vendor/autoload.php. Hãy chạy lệnh <code>composer dump-autoload</code></div>");
        }

        // 2. Import các Class từ Namespace VGrid
        use VGrid\Grid\DataGrid;
        use VGrid\Components\Columns\TextColumn;

        // 3. Giả lập dữ liệu từ Database (Mảng thuần)
        $products = [
            [
                'id' => 101, 
                'sku' => 'LAP-DELL-XPS', 
                'name' => 'Dell XPS 15 9520', 
                'category' => 'Laptop',
                'stock' => 5
            ],
            [
                'id' => 102, 
                'sku' => 'MOB-IPH-15P', 
                'name' => 'iPhone 15 Pro Max', 
                'category' => 'Mobile',
                'stock' => 12
            ],
            [
                'id' => 103, 
                'sku' => 'KEY-LOG-MX', 
                'name' => 'Logitech MX Keys S', 
                'category' => 'Accessory',
                'stock' => 50
            ],
            [
                'id' => 104, 
                'sku' => 'MON-LG-UL', 
                'name' => 'LG UltraFine 4K', 
                'category' => 'Monitor',
                'stock' => 0 // Hết hàng
            ],
        ];

        try {
            // 4. Khởi tạo Grid với ID duy nhất
            $grid = new DataGrid('product_list_01');

            // 5. Cấu hình Dữ liệu và Cột
            $grid->setDataSource($products)
                 ->setColumns([
                     // Cột ID
                     TextColumn::make('id', '#ID'),
                     
                     // Cột Mã SP
                     TextColumn::make('sku', 'Mã SKU'),
                     
                     // Cột Tên SP (Tùy chỉnh Label)
                     TextColumn::make('name', 'Tên Sản Phẩm'),
                     
                     // Cột Danh mục
                     TextColumn::make('category', 'Danh mục'),

                     // Cột Tồn kho
                     TextColumn::make('stock', 'Tồn kho')
                 ]);

            // 6. Render ra HTML
            echo $grid->render();

        } catch (\Exception $e) {
            echo "<div class='alert alert-danger'>Đã xảy ra lỗi: " . $e->getMessage() . "</div>";
        }
        ?>

        <div class="mt-4 p-3 bg-white border rounded">
            <h5>🔍 Debug Info:</h5>
            <ul>
                <li><strong>Namespace:</strong> VGrid</li>
                <li><strong>Theme:</strong> Bootstrap 5 (Default)</li>
                <li><strong>Total Rows:</strong> <?= count($products) ?></li>
            </ul>
        </div>
    </div>

    <!-- Bootstrap JS (Optional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>