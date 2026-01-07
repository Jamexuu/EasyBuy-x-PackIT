<?php
declare(strict_types=1);

function get_distance_fare_by_region(): array {
  return [
    "NCR" => 100,
    "NORTH" => 200,
    "SOUTH" => 200,
    "VISAYAS" => 300,
    "MINDANAO" => 500,
  ];
}

// Door-to-door removed
function get_door_to_door_amount(bool $yes): int {
  return 0;
}

function compute_distance_fare(?string $pickupRegion, ?string $dropRegion): ?int {
  if (!$pickupRegion || !$dropRegion) return null;

  $table = get_distance_fare_by_region();

  if ($pickupRegion === "MINDANAO" && $dropRegion === "MINDANAO") {
    return $table["NCR"];
  }

  return $table[$dropRegion] ?? null;
}

// Total fare excludes door-to-door
function compute_total_fare(int $baseAmount, int $distanceAmount, int $doorToDoorAmount = 0): int {
  return $baseAmount + $distanceAmount;
}