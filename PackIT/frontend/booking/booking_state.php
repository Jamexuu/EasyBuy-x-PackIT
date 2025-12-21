<?php
// booking/booking_state.php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

/**
 * Package table:
 * key => [label, vehicle_key, vehicle_label, base_amount]
 */
function get_packages(): array {
  return [
    "envelop" => [
      "label" => "Envelop",
      "vehicle_key" => "motor",
      "vehicle_label" => "Motor",
      "amount" => 100,
    ],
    "small_box" => [
      "label" => "Small Box",
      "vehicle_key" => "tricycle",
      "vehicle_label" => "Tricycle",
      "amount" => 150,
    ],
    "medium_box" => [
      "label" => "Medium Box",
      "vehicle_key" => "sedan",
      "vehicle_label" => "Sedan",
      "amount" => 200,
    ],
    "big_box" => [
      "label" => "Big Box",
      "vehicle_key" => "pickup",
      "vehicle_label" => "Pick up",
      "amount" => 250,
    ],
    "pallet_perishable" => [
      "label" => "Pallet Perishable Goods",
      "vehicle_key" => "closed_van",
      "vehicle_label" => "Closed Van",
      "amount" => 300,
    ],
    "pallet_nonperishable" => [
      "label" => "Pallet non Perishable Goods",
      "vehicle_key" => "forward",
      "vehicle_label" => "Forward",
      "amount" => 350,
    ],
  ];
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
  $_SESSION["booking"]["vehicle_key"] = $pkg["vehicle_key"];
  $_SESSION["booking"]["vehicle_label"] = $pkg["vehicle_label"];
  $_SESSION["booking"]["base_amount"] = $pkg["amount"];
}

function get_booking_state(): array {
  return $_SESSION["booking"] ?? [];
}