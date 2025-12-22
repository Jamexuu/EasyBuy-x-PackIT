<?php
/* * ==========================================================
 * SERVER-SIDE LOGIC
 * ==========================================================
 */

// 1. DATA LOADING FUNCTION
function loadJson($filename) {
    $path = __DIR__ . '/' . $filename;
    if (!file_exists($path)) return [];
    $json = file_get_contents($path);
    return json_decode($json, true);
}

// 2. ISLAND GROUP LOGIC
function getIslandGroup($region_code) {
    if ($region_code === '13') return 'NCR';
    if (in_array($region_code, ['01', '02', '03', '14'])) return 'NORTH';
    if (in_array($region_code, ['04', '05', '17'])) return 'SOUTH';
    if (in_array($region_code, ['06', '07', '08'])) return 'VISAYAS';
    if (in_array($region_code, ['09', '10', '11', '12', '15', '16'])) return 'MINDANAO';
    return 'UNKNOWN';
}

// 3. AJAX API HANDLER (For the dropdowns)
if (isset($_GET['action'])) {
    header('Content-Type: application/json');
    $action = $_GET['action'];
    $response = [];

    if ($action === 'get_regions') {
        $data = loadJson('region.json');
        foreach ($data as &$region) {
            $region['island_group'] = getIslandGroup($region['region_code']);
        }
        $response = $data;
    } elseif ($action === 'get_provinces') {
        $code = $_GET['region_code'] ?? '';
        $data = loadJson('province.json');
        $response = array_filter($data, fn($i) => $i['region_code'] === $code);
    } elseif ($action === 'get_cities') {
        $code = $_GET['province_code'] ?? '';
        $data = loadJson('city.json');
        $response = array_filter($data, fn($i) => $i['province_code'] === $code);
    } elseif ($action === 'get_barangays') {
        $code = $_GET['city_code'] ?? '';
        $data = loadJson('barangay.json');
        $response = array_filter($data, fn($i) => $i['city_code'] === $code);
    }

    echo json_encode(array_values($response));
    exit;
}

// 4. FORM SUBMISSION HANDLER (What happens when you click Submit)
$submitted_data = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // This is where you would save to database
    $submitted_data = [
        'region'   => $_POST['region_name'] ?? 'Unknown',
        'province' => $_POST['province_name'] ?? 'Unknown',
        'city'     => $_POST['city_name'] ?? 'Unknown',
        'barangay' => $_POST['barangay_name'] ?? 'Unknown',
        'group'    => $_POST['island_group'] ?? 'Unknown'
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Address Selector</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; padding: 20px; background: #f0f2f5; }
        .container { max-width: 500px; background: white; padding: 30px; margin: 20px auto; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        h3 { margin-top: 0; color: #1a1a1a; text-align: center; }
        
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-weight: 600; font-size: 0.9rem; color: #444; }
        select { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 1rem; transition: border 0.3s; }
        select:focus { border-color: #3498db; outline: none; }
        select:disabled { background-color: #f8f9fa; color: #999; cursor: not-allowed; }

        /* Submit Button Style */
        .btn-submit {
            width: 100%;
            padding: 14px;
            background-color: #2c3e50;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s;
        }
        .btn-submit:hover { background-color: #1a252f; }

        /* Badge Style */
        .badge {
            display: inline-block; padding: 4px 10px; font-size: 11px; border-radius: 12px; 
            color: white; font-weight: bold; float: right; display: none; text-transform: uppercase;
        }
        .bg-ncr { background: #e74c3c; }
        .bg-north { background: #3498db; }
        .bg-south { background: #f39c12; }
        .bg-visayas { background: #9b59b6; }
        .bg-mindanao { background: #2ecc71; }

        /* Result Box */
        .result-box { background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #c3e6cb; }
    </style>
</head>
<body>

<div class="container">
    <h3>Shipping Address</h3>

    <?php if ($submitted_data): ?>
        <div class="result-box">
            <strong>Success!</strong> You selected:<br>
            Island Group: <b><?= htmlspecialchars($submitted_data['group']) ?></b><br>
            Location: <?= htmlspecialchars($submitted_data['city']) ?>, <?= htmlspecialchars($submitted_data['province']) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <input type="hidden" name="region_name" id="input_region_name">
        <input type="hidden" name="province_name" id="input_province_name">
        <input type="hidden" name="city_name" id="input_city_name">
        <input type="hidden" name="barangay_name" id="input_barangay_name">
        <input type="hidden" name="island_group" id="input_island_group">

        <div class="form-group">
            <label>Region <span id="island-badge" class="badge"></span></label>
            <select id="region" name="region_code" required>
                <option value="">Select Region</option>
            </select>
        </div>

        <div class="form-group">
            <label>Province</label>
            <select id="province" name="province_code" disabled required>
                <option value="">Select Province</option>
            </select>
        </div>

        <div class="form-group">
            <label>City / Municipality</label>
            <select id="city" name="city_code" disabled required>
                <option value="">Select City</option>
            </select>
        </div>

        <div class="form-group">
            <label>Barangay</label>
            <select id="barangay" name="brgy_code" disabled required>
                <option value="">Select Barangay</option>
            </select>
        </div>

        <button type="submit" class="btn-submit">Confirm Location</button>
    </form>
</div>

<script>
    // DOM Elements
    const regionEl = document.getElementById('region');
    const provinceEl = document.getElementById('province');
    const cityEl = document.getElementById('city');
    const barangayEl = document.getElementById('barangay');
    const badgeEl = document.getElementById('island-badge');

    // Hidden Inputs (to save names for PHP)
    const hiddenRegion = document.getElementById('input_region_name');
    const hiddenProvince = document.getElementById('input_province_name');
    const hiddenCity = document.getElementById('input_city_name');
    const hiddenBarangay = document.getElementById('input_barangay_name');
    const hiddenGroup = document.getElementById('input_island_group');

    // --- API HELPER ---
    async function fetchData(params) {
        try {
            const res = await fetch(`?${params}`);
            return await res.json();
        } catch(e) { console.error(e); return []; }
    }

    // --- UI HELPER ---
    function populate(element, data, codeKey, nameKey, groupKey = null) {
        element.innerHTML = '<option value="">Select Option</option>';
        element.disabled = false;
        
        // Sort Alphabetically
        data.sort((a,b) => a[nameKey].localeCompare(b[nameKey]));

        data.forEach(item => {
            const opt = document.createElement('option');
            opt.value = item[codeKey];
            opt.textContent = item[nameKey];
            // Store extra data in dataset
            opt.dataset.name = item[nameKey]; 
            if(groupKey && item[groupKey]) opt.dataset.group = item[groupKey];
            element.appendChild(opt);
        });
    }

    function reset(...elements) {
        elements.forEach(el => { el.innerHTML = '<option value="">Select Option</option>'; el.disabled = true; });
    }

    // --- UPDATE HIDDEN INPUTS ---
    function updateHiddenName(selectElement, hiddenInput) {
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        hiddenInput.value = selectedOption.dataset.name || '';
    }

    // --- EVENT LISTENERS ---

    // 1. LOAD REGIONS
    fetchData('action=get_regions').then(data => {
        populate(regionEl, data, 'region_code', 'region_name', 'island_group');
    });

    // 2. REGION CHANGE
    regionEl.addEventListener('change', function() {
        const code = this.value;
        reset(provinceEl, cityEl, barangayEl);
        badgeEl.style.display = 'none';
        
        updateHiddenName(this, hiddenRegion); // Save name

        if (code) {
            // Handle Badge & Group
            const selectedOpt = this.options[this.selectedIndex];
            const group = selectedOpt.dataset.group;
            if(group) {
                badgeEl.textContent = group;
                badgeEl.className = 'badge bg-' + group.toLowerCase(); // e.g. bg-ncr
                badgeEl.style.display = 'inline-block';
                hiddenGroup.value = group; // Save group to hidden input
            }

            fetchData(`action=get_provinces&region_code=${code}`).then(data => {
                populate(provinceEl, data, 'province_code', 'province_name');
            });
        }
    });

    // 3. PROVINCE CHANGE
    provinceEl.addEventListener('change', function() {
        const code = this.value;
        reset(cityEl, barangayEl);
        updateHiddenName(this, hiddenProvince); // Save name

        if (code) {
            fetchData(`action=get_cities&province_code=${code}`).then(data => {
                populate(cityEl, data, 'city_code', 'city_name');
            });
        }
    });

    // 4. CITY CHANGE
    cityEl.addEventListener('change', function() {
        const code = this.value;
        reset(barangayEl);
        updateHiddenName(this, hiddenCity); // Save name

        if (code) {
            fetchData(`action=get_barangays&city_code=${code}`).then(data => {
                populate(barangayEl, data, 'brgy_code', 'brgy_name');
            });
        }
    });

    // 5. BARANGAY CHANGE
    barangayEl.addEventListener('change', function() {
        updateHiddenName(this, hiddenBarangay); // Save name
    });

</script>
</body>
</html>