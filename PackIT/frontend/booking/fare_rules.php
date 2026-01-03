<?php
// booking/fare_rules.php
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

function get_door_to_door_amount(bool $yes): int {
  return $yes ? 100 : 0;
}

/**
 * Distance fare rule:
 * - If pickup/drop region not known => return null (force user/admin to add mapping)
 * - Special: Mindanao -> Mindanao = NCR amount (100)
 */
function compute_distance_fare(?string $pickupRegion, ?string $dropRegion): ?int {
  if (!$pickupRegion || !$dropRegion) return null;

  $table = get_distance_fare_by_region();

  // Special rule
  if ($pickupRegion === "MINDANAO" && $dropRegion === "MINDANAO") {
    return $table["NCR"];
  }

  // Otherwise use drop region amount (typical destination-based charge)
  return $table[$dropRegion] ?? null;
}

/**
 * Total fare:
 * total = base_amount + distance_amount + door_to_door
 */
function compute_total_fare(int $baseAmount, int $distanceAmount, int $doorToDoorAmount): int {
  return $baseAmount + $distanceAmount + $doorToDoorAmount;
}