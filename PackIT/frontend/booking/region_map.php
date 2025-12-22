<?php
declare(strict_types=1);

/**
 * Fare region labels expected by fare_rules.php:
 * NCR, NORTH, SOUTH, VISAYAS, MINDANAO
 */
function region_code_to_fare_region(string $region_code): ?string {
  $code = str_pad(trim($region_code), 2, "0", STR_PAD_LEFT);

  if ($code === "13") return "NCR";
  if (in_array($code, ["01", "02", "03", "14"], true)) return "NORTH";
  if (in_array($code, ["04", "05", "17"], true)) return "SOUTH";
  if (in_array($code, ["06", "07", "08"], true)) return "VISAYAS";
  if (in_array($code, ["09", "10", "11", "12", "15", "16"], true)) return "MINDANAO";

  return null;
}

/**
 * Backwards-compatible function name used by your old address.php.
 * We'll keep it, but you should prefer region_code_to_fare_region().
 */
function resolve_region(string $municipality, string $province = ""): ?string {
  // If you still have old pages calling resolve_region by text,
  // you can keep a legacy mapping here.
  // For now, return null so you notice missing legacy calls.
  return null;
}