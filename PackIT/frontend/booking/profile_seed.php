<?php
declare(strict_types=1);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>PackIT - Seed Profile Address</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-7">
      <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-4">
          <h4 class="fw-bold">Seed Default Pickup Address (Frontend Only)</h4>
          <p class="text-muted small mb-4">This simulates the user registered address using LocalStorage.</p>

          <div class="alert alert-info small mb-4">
            Quick fill: click <strong>Use Sample (Makati, NCR)</strong> then click <strong>Save to LocalStorage</strong>.
            This should make <code>address.php</code> show Region <strong>NCR</strong> using your province alias rule.
          </div>

          <div class="row g-3">
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
            <button class="btn btn-primary w-50" id="saveBtn" type="button">Save to LocalStorage</button>
            <a class="btn btn-outline-secondary w-50" href="package.php">Go to Booking</a>
          </div>

          <hr class="my-4">
          <div class="small text-muted">
            Stored key: <code>packit_profile_address</code>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

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
    const data = {
      house: document.getElementById("house").value.trim(),
      barangay: document.getElementById("barangay").value.trim(),
      municipality: document.getElementById("municipality").value.trim(),
      province: document.getElementById("province").value.trim(),
    };
    localStorage.setItem("packit_profile_address", JSON.stringify(data));
    alert("Saved! Now open address.php and click 'Use default pickup address'.");
  });
</script>
</body>
</html>