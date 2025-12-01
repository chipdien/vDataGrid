<div class="card shadow-sm">
    <div class="card-body">
        <form action="<?= $action ?>" method="POST">
            
            <!-- BẮT ĐẦU GRID ROW -->
            <div class="row g-3"> 
                <?php foreach ($inputs as $input): ?>
                    <?php 
                        // Lấy độ rộng cột (mặc định 12)
                        $span = method_exists($input, 'getColSpan') ? $input->getColSpan() : 12; 
                    ?>
                    
                    <!-- Wrapper chia cột -->
                    <div class="col-12 col-md-<?= $span ?>">
                        <?= $input->render() ?>
                    </div>
                    
                <?php endforeach; ?>
            </div>
            <!-- KẾT THÚC GRID ROW -->

            <div class="mt-4 pt-3 border-top d-flex justify-content-end gap-2">
                <a href="javascript:history.back()" class="btn btn-secondary">Hủy bỏ</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Lưu dữ liệu
                </button>
            </div>
        </form>
    </div>
</div>