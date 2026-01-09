<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

require_once __DIR__ . "/../../api/classes/Database.php";

$userId = isset($_SESSION['user']['id']) ? (int)$_SESSION['user']['id'] : 0;

$fullName = "";
$contactNumber = "";
$canUse = false;

if ($userId > 0) {
  $db = new Database();
  $stmt = $db->executeQuery(
    "SELECT first_name, last_name, contact_number
     FROM users
     WHERE id = ?
     LIMIT 1",
    [$userId]
  );
  $rows = $db->fetch($stmt);

  if (!empty($rows)) {
    $fullName = trim(((string)$rows[0]['first_name']) . " " . ((string)$rows[0]['last_name']));
    $contactNumber = (string)($rows[0]['contact_number'] ?? '');
    $canUse = true;
  }
}

function h(string $s): string {
  return htmlspecialchars($s, ENT_QUOTES, "UTF-8");
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>PackIT - Seed Profile Address</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-7">
      <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-4">
          <h4 class="fw-bold">Seed Default Pickup Details (Frontend Only)</h4>
          <p class="text-muted small mb-4">
            This simulates default pickup details using LocalStorage. Address still uses <code>packit_profile_address</code>,
            and contact uses <code>packit_profile_contact</code>.
          </p>

          <?php if (!$canUse): ?>
            <div class="alert alert-warning small">
              You are not logged in (or user not found). Please log in first so we can pull your name & CP number from the database.
            </div>
          <?php else: ?>
            <div class="alert alert-info small mb-4">
              Loaded from DB (users table): <strong><?= h($fullName) ?></strong> • <strong><?= h($contactNumber) ?></strong>
            </div>
          <?php endif; ?>

          <div class="row g-3">
            <!-- Contact seed -->
            <div class="col-12">
              <h6 class="fw-bold mb-0">Pickup Contact (from current user)</h6>
              <div class="text-muted small">Editable before saving to LocalStorage.</div>
            </div>

            <div class="col-md-7">
              <label class="form-label">Full Name</label>
              <input class="form-control" id="contact_name" placeholder="e.g. Juan Dela Cruz"
                     value="<?= h($fullName) ?>" <?= $canUse ? "" : "disabled" ?>>
            </div>

            <div class="col-md-5">
              <label class="form-label">CP Number</label>
              <input class="form-control" id="contact_number" placeholder="09xxxxxxxxx"
                     value="<?= h($contactNumber) ?>" <?= $canUse ? "" : "disabled" ?>>
            </div>

            <hr class="my-3">

            <!-- Address seed -->
            <div class="col-12">
              <h6 class="fw-bold mb-0">Pickup Address</h6>
              <div class="text-muted small">This is the same address seed you already had.</div>
            </div>

            <div class="col-12">
              <label class="form-label">House Address</label>
              <input class="form-control" id="house" placeholder="e.g. Blk 1 Lot 2" value="Blk 1 Lot 2">
            </div>
            <div class="col-md-6">
              <label class="form-label">Barangay</label>
              <input class="form-control" id="barangay" placeholder="e.g. Brgy. Uno" value="Poblacion">
            </div>
            <div class="col-md-6">
              <label class="form-label">Municipality</label>
              <input class="form-control" id="municipality" placeholder="e.g. Calamba" value="Makati">
            </div>
            <div class="col-12">
              <label class="form-label">Province</label>
              <input class="form-control" id="province" placeholder="e.g. Laguna" value="NCR">
              <div class="form-text">
                Province aliases supported: <code>NCR</code>, <code>National Capital Region</code>, <code>Metro Manila</code>.
              </div>
            </div>
          </div>

          <div class="d-flex gap-2 mt-4 flex-wrap">
            <button class="btn btn-outline-primary flex-fill" type="button" id="sampleMakatiBtn">
              Use Sample (Makati, NCR)
            </button>
            <button class="btn btn-outline-primary flex-fill" type="button" id="sampleTagaytayBtn">
              Use Sample (Tagaytay, Cavite)
            </button>
          </div>

          <div class="d-flex gap-2 mt-3">
            <button class="btn btn-primary w-50" id="saveBtn" type="button" <?= $canUse ? "" : "disabled" ?>>
              Save to LocalStorage
            </button>
            <a class="btn btn-outline-secondary w-50" href="package.php">Go to Booking</a>
          </div>

          <hr class="my-4">
          <div class="small text-muted">
            Stored keys:
            <ul class="mb-0">
              <li><code>packit_profile_address</code> (pickup address)</li>
              <li><code>packit_profile_contact</code> (pickup contact)</li>
            </ul>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script>
  function setForm(data) {
    document.getElementById("house").value = data.house || "";
    document.getElementById("barangay").value = data.barangay || "";
    document.getElementById("municipality").value = data.municipality || "";
    document.getElementById("province").value = data.province || "";
  }

  document.getElementById("sampleMakatiBtn").addEventListener("click", () => {
    setForm({
      house: "Blk 1 Lot 2",
      barangay: "Poblacion",
      municipality: "Makati",
      province: "NCR",
    });
  });

  document.getElementById("sampleTagaytayBtn").addEventListener("click", () => {
    setForm({
      house: "Unit 3",
      barangay: "Silang Junction North",
      municipality: "Tagaytay",
      province: "Cavite",
    });
  });

  document.getElementById("saveBtn").addEventListener("click", () => {
    const address = {
      house: document.getElementById("house").value.trim(),
      barangay: document.getElementById("barangay").value.trim(),
      municipality: document.getElementById("municipality").value.trim(),
      province: document.getElementById("province").value.trim(),
    };

    const contact = {
      name: document.getElementById("contact_name").value.trim(),
      contact_number: document.getElementById("contact_number").value.trim(),
    };

    localStorage.setItem("packit_profile_address", JSON.stringify(address));
    localStorage.setItem("packit_profile_contact", JSON.stringify(contact));

    alert("Saved! Address + Contact stored. You can now proceed to booking.");
  });
</script>
</body>
</html>