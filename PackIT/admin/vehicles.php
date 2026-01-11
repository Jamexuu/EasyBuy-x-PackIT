<?php
require_once '../api/classes/Auth.php';
require_once '../api/classes/Database.php';

Auth::requireAdmin();
$basePath = '../';
$activePage = 'vehicles';
$db = new Database();
$success = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ... [Keep Existing Logic for Update] ...
    // Assuming logic remains identical to original file provided
    $id = (int)($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $package_type = trim($_POST['package_type'] ?? '');
    $fare = trim($_POST['fare'] ?? '');
    $max_kg = trim($_POST['max_kg'] ?? '');
    $size_length_m = trim($_POST['size_length_m'] ?? '');
    $size_width_m = trim($_POST['size_width_m'] ?? '');
    $size_height_m = trim($_POST['size_height_m'] ?? '');
    $image_file = trim($_POST['image_file'] ?? '');

    if ($id <= 0 || $name === '') {
         $error = "Invalid input.";
    } else {
        $stmt = $db->executeQuery("UPDATE vehicles SET name=?, package_type=?, fare=?, max_kg=?, size_length_m=?, size_width_m=?, size_height_m=?, image_file=? WHERE id=?", 
        [$name, $package_type, $fare, $max_kg, $size_length_m, $size_width_m, $size_height_m, $image_file, $id]);
        $stmt ? $success = "Updated successfully." : $error = "Update failed.";
    }
}
$stmt = $db->executeQuery("SELECT * FROM vehicles ORDER BY id ASC");
$vehicles = $db->fetch($stmt);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PackIT Admin - Vehicles</title>
</head>
<body class="bg-light">

<?php include __DIR__ . '/../frontend/components/adminNavbar.php'; ?>

    <div class="col-lg-9 col-md-8">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body p-4 p-lg-5">
                <div class="mb-4">
                    <h4 class="fw-bold mb-1">Vehicles</h4>
                    <p class="text-muted mb-0">Edit vehicle type, fare, max kg, size, and image.</p>
                </div>

                <?php if ($success): ?>
                    <div class="alert alert-success rounded-3 mb-4"><?= htmlspecialchars($success) ?></div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div class="alert alert-danger rounded-3 mb-4"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Fare</th>
                                <th>Max (kg)</th>
                                <th>Size (m)</th>
                                <th>Image</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($vehicles as $v): ?>
                            <tr>
                                <td><?= htmlspecialchars((string)$v['id']) ?></td>
                                <td class="fw-semibold"><?= htmlspecialchars($v['name']) ?></td>
                                <td><span class="badge bg-secondary-subtle text-secondary-emphasis rounded-pill"><?= htmlspecialchars($v['package_type']) ?></span></td>
                                <td>₱<?= htmlspecialchars(number_format((float)$v['fare'], 0)) ?></td>
                                <td><?= htmlspecialchars((string)$v['max_kg']) ?></td>
                                <td class="text-nowrap small text-muted">
                                    <?= htmlspecialchars((string)$v['size_length_m']) ?> ×
                                    <?= htmlspecialchars((string)$v['size_width_m']) ?> ×
                                    <?= htmlspecialchars((string)$v['size_height_m']) ?>
                                </td>
                                <td class="text-truncate" style="max-width: 100px;"><?= htmlspecialchars($v['image_file']) ?></td>
                                <td class="text-end">
                                    <button
                                        class="btn btn-dark btn-sm rounded-pill px-3"
                                        type="button"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editVehicleModal"
                                        data-id="<?= htmlspecialchars((string)$v['id']) ?>"
                                        data-name="<?= htmlspecialchars($v['name']) ?>"
                                        data-package_type="<?= htmlspecialchars($v['package_type']) ?>"
                                        data-fare="<?= htmlspecialchars((string)$v['fare']) ?>"
                                        data-max_kg="<?= htmlspecialchars((string)$v['max_kg']) ?>"
                                        data-size_length_m="<?= htmlspecialchars((string)$v['size_length_m']) ?>"
                                        data-size_width_m="<?= htmlspecialchars((string)$v['size_width_m']) ?>"
                                        data-size_height_m="<?= htmlspecialchars((string)$v['size_height_m']) ?>"
                                        data-image_file="<?= htmlspecialchars($v['image_file']) ?>"
                                    >
                                        Edit
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="modal fade" id="editVehicleModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content rounded-4 border-0 shadow">
                            <div class="modal-header border-bottom-0 pb-0">
                                <h5 class="modal-title fw-bold">Edit Vehicle</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>

                            <form method="POST" class="modal-body pt-4">
                                <input type="hidden" name="id" id="v_id">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">Name</label>
                                        <input class="form-control" name="name" id="v_name" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">Package Type</label>
                                        <input class="form-control" name="package_type" id="v_package_type" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold">Fare (PHP)</label>
                                        <input class="form-control" name="fare" id="v_fare" type="number" step="0.01" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold">Max KG</label>
                                        <input class="form-control" name="max_kg" id="v_max_kg" type="number" step="1" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold">Image File</label>
                                        <input class="form-control" name="image_file" id="v_image_file" required>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label small fw-bold">Dimensions (L x W x H)</label>
                                        <div class="input-group">
                                            <input class="form-control" name="size_length_m" id="v_size_length_m" placeholder="Length" type="number" step="0.01" required>
                                            <span class="input-group-text bg-light">m</span>
                                            <input class="form-control" name="size_width_m" id="v_size_width_m" placeholder="Width" type="number" step="0.01" required>
                                            <span class="input-group-text bg-light">m</span>
                                            <input class="form-control" name="size_height_m" id="v_size_height_m" placeholder="Height" type="number" step="0.01" required>
                                            <span class="input-group-text bg-light">m</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end gap-2 mt-4">
                                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold shadow-sm">Save Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

</div> </div> <?php include '../frontend/components/adminFooter.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
const modal = document.getElementById('editVehicleModal');
modal.addEventListener('show.bs.modal', function (event) {
    const btn = event.relatedTarget;
    // ... [JS Logic remains identical, just ensuring IDs match] ...
    document.getElementById('v_id').value = btn.getAttribute('data-id');
    document.getElementById('v_name').value = btn.getAttribute('data-name');
    document.getElementById('v_package_type').value = btn.getAttribute('data-package_type');
    document.getElementById('v_fare').value = btn.getAttribute('data-fare');
    document.getElementById('v_max_kg').value = btn.getAttribute('data-max_kg');
    document.getElementById('v_size_length_m').value = btn.getAttribute('data-size_length_m');
    document.getElementById('v_size_width_m').value = btn.getAttribute('data-size_width_m');
    document.getElementById('v_size_height_m').value = btn.getAttribute('data-size_height_m');
    document.getElementById('v_image_file').value = btn.getAttribute('data-image_file');
});
</script>
</body>
</html>