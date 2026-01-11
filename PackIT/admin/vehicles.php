<?php
require_once '../api/classes/Auth.php';
require_once '../api/classes/Database.php';

Auth::requireAdmin();

$basePath = '../';
$activePage = 'vehicles';

$db = new Database();

$success = null;
$error = null;

// Update record
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);

    $name = trim($_POST['name'] ?? '');
    $package_type = trim($_POST['package_type'] ?? '');
    $fare = trim($_POST['fare'] ?? '');
    $max_kg = trim($_POST['max_kg'] ?? '');
    $size_length_m = trim($_POST['size_length_m'] ?? '');
    $size_width_m = trim($_POST['size_width_m'] ?? '');
    $size_height_m = trim($_POST['size_height_m'] ?? '');
    $image_file = trim($_POST['image_file'] ?? '');

    if ($id <= 0) {
        $error = "Invalid vehicle ID.";
    } elseif ($name === '' || $package_type === '' || $image_file === '') {
        $error = "Name, Package Type, and Image File are required.";
    } elseif (!is_numeric($fare) || !is_numeric($max_kg) || !is_numeric($size_length_m) || !is_numeric($size_width_m) || !is_numeric($size_height_m)) {
        $error = "Fare, Max KG, and Size fields must be numeric.";
    } else {
        $stmt = $db->executeQuery(
            "UPDATE vehicles
             SET name = ?, package_type = ?, fare = ?, max_kg = ?,
                 size_length_m = ?, size_width_m = ?, size_height_m = ?,
                 image_file = ?
             WHERE id = ?",
            [
                $name,
                $package_type,
                (string)$fare,
                (string)$max_kg,
                (string)$size_length_m,
                (string)$size_width_m,
                (string)$size_height_m,
                $image_file,
                (string)$id,
            ]
        );

        if ($stmt) {
            $success = "Vehicle updated successfully.";
        } else {
            $error = "Update failed.";
        }
    }
}

// Fetch vehicles
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
<body>

<?php include __DIR__ . '/../frontend/components/adminNavbar.php'; ?>

        <div class="col-lg-9 col-md-8">
            <div class="content-area shadow-sm p-5">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                    <div>
                        <h4 class="fw-bold mb-1">Vehicles</h4>
                        <div class="text-muted">Edit vehicle type, fare, max kg, size, and image.</div>
                    </div>
                </div>

                <?php if ($success): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Package Type</th>
                                <th>Fare</th>
                                <th>Max (kg)</th>
                                <th>Size (L×W×H)</th>
                                <th>Image</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($vehicles as $v): ?>
                            <tr>
                                <td><?= htmlspecialchars((string)$v['id']) ?></td>
                                <td><?= htmlspecialchars($v['name']) ?></td>
                                <td><?= htmlspecialchars($v['package_type']) ?></td>
                                <td>₱<?= htmlspecialchars(number_format((float)$v['fare'], 0)) ?></td>
                                <td><?= htmlspecialchars((string)$v['max_kg']) ?></td>
                                <td class="text-nowrap">
                                    <?= htmlspecialchars((string)$v['size_length_m']) ?> ×
                                    <?= htmlspecialchars((string)$v['size_width_m']) ?> ×
                                    <?= htmlspecialchars((string)$v['size_height_m']) ?> m
                                </td>
                                <td class="text-nowrap"><?= htmlspecialchars($v['image_file']) ?></td>
                                <td class="text-end">
                                    <button
                                        class="btn btn-dark btn-sm rounded-pill"
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

                <!-- Modal -->
                <div class="modal fade" id="editVehicleModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content rounded-4">
                            <div class="modal-header">
                                <h5 class="modal-title fw-bold">Edit Vehicle</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>

                            <form method="POST" class="modal-body">
                                <input type="hidden" name="id" id="v_id">

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Name</label>
                                        <input class="form-control" name="name" id="v_name" required>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Package Type</label>
                                        <input class="form-control" name="package_type" id="v_package_type" required>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">Fare (PHP)</label>
                                        <input class="form-control" name="fare" id="v_fare" type="number" step="0.01" min="0" required>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">Max KG</label>
                                        <input class="form-control" name="max_kg" id="v_max_kg" type="number" step="1" min="0" required>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">Image File</label>
                                        <input class="form-control" name="image_file" id="v_image_file" required>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">Length (m)</label>
                                        <input class="form-control" name="size_length_m" id="v_size_length_m" type="number" step="0.01" min="0" required>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">Width (m)</label>
                                        <input class="form-control" name="size_width_m" id="v_size_width_m" type="number" step="0.01" min="0" required>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">Height (m)</label>
                                        <input class="form-control" name="size_height_m" id="v_size_height_m" type="number" step="0.01" min="0" required>
                                    </div>
                                </div>

                                <div class="modal-footer px-0 pb-0 mt-4">
                                    <button type="button" class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-dark rounded-pill">Save changes</button>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div><!-- row -->
</div><!-- container -->

        <?php include '../frontend/components/adminFooter.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
const modal = document.getElementById('editVehicleModal');
modal.addEventListener('show.bs.modal', function (event) {
    const btn = event.relatedTarget;

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