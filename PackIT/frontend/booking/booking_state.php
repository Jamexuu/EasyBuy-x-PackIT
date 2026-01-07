<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

require_once __DIR__ . "/../../api/classes/Database.php";

function get_packages(): array {
  $db = new Database();

  $stmt = $db->executeQuery(
    "SELECT id, name, package_type, fare, max_kg, size_length_m, size_width_m, size_height_m
     FROM vehicles
     ORDER BY id ASC"
  );
  $rows = $db->fetch($stmt);

  $packages = [];
  foreach ($rows as $r) {
    $id = (int)($r['id'] ?? 0);
    if ($id <= 0) continue;

    $key = (string)$id;

    $packages[$key] = [
      "label" => (string)($r["name"] ?? ''),
      "vehicle_id" => $id,
      "vehicle_label" => (string)($r["name"] ?? ''),
      "package_type" => (string)($r["package_type"] ?? ''),
      "amount" => (float)($r["fare"] ?? 0),
      "max_kg" => (int)($r["max_kg"] ?? 0),
      "size_length_m" => (float)($r["size_length_m"] ?? 0),
      "size_width_m" => (float)($r["size_width_m"] ?? 0),
      "size_height_m" => (float)($r["size_height_m"] ?? 0),
    ];
  }

  return $packages;
}

function get_package(string $key): ?array {
  $packages = get_packages();
  return $packages[$key] ?? null;
}

function set_selected_package(string $key): void {
  $pkg = get_package($key);
  if (!$pkg) return;

  $_SESSION["booking"] ??= [];
  $_SESSION["booking"]["package_key"] = $key;
  $_SESSION["booking"]["package_label"] = $pkg["label"];

  $_SESSION["booking"]["vehicle_id"] = $pkg["vehicle_id"];
  $_SESSION["booking"]["vehicle_label"] = $pkg["vehicle_label"];

  $_SESSION["booking"]["base_amount"] = $pkg["amount"];

  // store extra details for later display and DB
  $_SESSION["booking"]["package_type"] = $pkg["package_type"];
  $_SESSION["booking"]["max_kg"] = $pkg["max_kg"];
  $_SESSION["booking"]["size_length_m"] = $pkg["size_length_m"];
  $_SESSION["booking"]["size_width_m"] = $pkg["size_width_m"];
  $_SESSION["booking"]["size_height_m"] = $pkg["size_height_m"];

  // Local helper (no global function)
  $fmt1 = function (float $x): string {
    return rtrim(rtrim(number_format($x, 1), '0'), '.');
  };

  // Human-friendly description string (AUTO SPECS)
  $desc = sprintf(
    "Type: %s | Max: %d kg | Size: %s x %s x %s Meter",
    (string)$pkg["package_type"],
    (int)$pkg["max_kg"],
    $fmt1((float)$pkg["size_length_m"]),
    $fmt1((float)$pkg["size_width_m"]),
    $fmt1((float)$pkg["size_height_m"])
  );

  // IMPORTANT: do NOT overwrite user's package_desc.
  $_SESSION["booking"]["package_specs_desc"] = $desc;

  // defaults for new required fields
  $_SESSION["booking"]["package_desc"] ??= "";
  $_SESSION["booking"]["package_quantity"] ??= 1;
}

function get_booking_state(): array {
  return $_SESSION["booking"] ?? [];
}