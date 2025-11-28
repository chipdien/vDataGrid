<div class="card shadow-sm">
    <div class="card-body">
        <form action="<?= $action ?>" method="POST">
            <?php foreach ($inputs as $input): ?>
                <!-- Render từng input -->
                <?= $input->render() ?>
            <?php endforeach; ?>

            <div class="mt-4 pt-3 border-top d-flex justify-content-end gap-2">
                <a href="javascript:history.back()" class="btn btn-secondary">Hủy bỏ</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Lưu dữ liệu
                </button>
            </div>
        </form>
    </div>
</div>