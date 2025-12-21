<?php
declare(strict_types=1);

/**
 * Region codes:
 * - NCR
 * - NORTH
 * - SOUTH
 * - VISAYAS
 * - MINDANAO
 *
 * Conflict-free strategy:
 * 1) Try match by (municipality + province) with province alias normalization
 * 2) Fall back to municipality-only for SAFE entries (exclude conflict-prone names)
 */

function normalize_place(string $s): string {
  $s = trim(mb_strtolower($s));
  $s = preg_replace('/\s+/', ' ', $s);
  $s = str_replace([".", ","], "", $s);
  return $s ?? "";
}

/**
 * Province alias normalization:
 * - "NCR" / "National Capital Region" / "Metro Manila" -> "metro manila"
 * - "Quezon Province" -> "quezon"
 */
function normalize_province(string $province): string {
  $p = normalize_place($province);
  if ($p === "") return "";

  $ncrAliases = [
    "ncr",
    "national capital region",
    "metro manila",
    "metromanila",
    "metropolitan manila",
    "mm",
  ];
  if (in_array($p, $ncrAliases, true)) {
    return "metro manila";
  }

  if ($p === "quezon province") return "quezon";

  return $p;
}

function key_muni_prov(string $municipality, string $province): string {
  return normalize_place($municipality) . "|" . normalize_province($province);
}

function get_muni_prov_region_map(): array {
  return [
    // NCR
    key_muni_prov("Manila", "Metro Manila") => "NCR",
    key_muni_prov("Quezon City", "Metro Manila") => "NCR",
    key_muni_prov("Makati", "Metro Manila") => "NCR",
    key_muni_prov("Pasig", "Metro Manila") => "NCR",
    key_muni_prov("Taguig", "Metro Manila") => "NCR",
    key_muni_prov("Mandaluyong", "Metro Manila") => "NCR",
    key_muni_prov("Parañaque", "Metro Manila") => "NCR",
    key_muni_prov("Las Piñas", "Metro Manila") => "NCR",
    key_muni_prov("Muntinlupa", "Metro Manila") => "NCR",
    key_muni_prov("Caloocan", "Metro Manila") => "NCR",
    key_muni_prov("Marikina", "Metro Manila") => "NCR",
    key_muni_prov("San Juan", "Metro Manila") => "NCR",
    key_muni_prov("Pasay", "Metro Manila") => "NCR",
    key_muni_prov("Valenzuela", "Metro Manila") => "NCR",

    // NORTH
    key_muni_prov("Angeles", "Pampanga") => "NORTH",
    key_muni_prov("San Fernando", "Pampanga") => "NORTH",
    key_muni_prov("Malolos", "Bulacan") => "NORTH",
    key_muni_prov("Meycauayan", "Bulacan") => "NORTH",
    key_muni_prov("Baliuag", "Bulacan") => "NORTH",
    key_muni_prov("Cabanatuan", "Nueva Ecija") => "NORTH",
    key_muni_prov("San Jose", "Nueva Ecija") => "NORTH",
    key_muni_prov("Tarlac City", "Tarlac") => "NORTH",
    key_muni_prov("Dagupan", "Pangasinan") => "NORTH",
    key_muni_prov("Urdaneta", "Pangasinan") => "NORTH",
    key_muni_prov("San Fernando", "La Union") => "NORTH",
    key_muni_prov("Baguio", "Benguet") => "NORTH",
    key_muni_prov("Laoag", "Ilocos Norte") => "NORTH",
    key_muni_prov("Vigan", "Ilocos Sur") => "NORTH",
    key_muni_prov("Olongapo", "Zambales") => "NORTH",

    // SOUTH
    key_muni_prov("Calamba", "Laguna") => "SOUTH",
    key_muni_prov("Santa Rosa", "Laguna") => "SOUTH",
    key_muni_prov("Biñan", "Laguna") => "SOUTH",
    key_muni_prov("San Pedro", "Laguna") => "SOUTH",

    key_muni_prov("Dasmariñas", "Cavite") => "SOUTH",
    key_muni_prov("Imus", "Cavite") => "SOUTH",
    key_muni_prov("Bacoor", "Cavite") => "SOUTH",
    key_muni_prov("Tagaytay", "Cavite") => "SOUTH",

    key_muni_prov("Batangas City", "Batangas") => "SOUTH",
    key_muni_prov("Lipa", "Batangas") => "SOUTH",
    key_muni_prov("Tanauan", "Batangas") => "SOUTH",

    key_muni_prov("Antipolo", "Rizal") => "SOUTH",
    key_muni_prov("Taytay", "Rizal") => "SOUTH",
    key_muni_prov("Cainta", "Rizal") => "SOUTH",

    key_muni_prov("Lucena", "Quezon") => "SOUTH",

    key_muni_prov("San Jose", "Occidental Mindoro") => "SOUTH",
    key_muni_prov("Calapan", "Oriental Mindoro") => "SOUTH",

    // VISAYAS
    key_muni_prov("Cebu City", "Cebu") => "VISAYAS",
    key_muni_prov("Mandaue", "Cebu") => "VISAYAS",
    key_muni_prov("Lapu-Lapu", "Cebu") => "VISAYAS",
    key_muni_prov("Iloilo City", "Iloilo") => "VISAYAS",
    key_muni_prov("Bacolod", "Negros Occidental") => "VISAYAS",
    key_muni_prov("Dumaguete", "Negros Oriental") => "VISAYAS",
    key_muni_prov("Tagbilaran", "Bohol") => "VISAYAS",
    key_muni_prov("Tacloban", "Leyte") => "VISAYAS",
    key_muni_prov("Catbalogan", "Samar") => "VISAYAS",
    key_muni_prov("Kalibo", "Aklan") => "VISAYAS",
    key_muni_prov("Roxas City", "Capiz") => "VISAYAS",

    // MINDANAO
    key_muni_prov("Davao City", "Davao del Sur") => "MINDANAO",
    key_muni_prov("Cagayan de Oro", "Misamis Oriental") => "MINDANAO",
    key_muni_prov("Zamboanga City", "Zamboanga del Sur") => "MINDANAO",
    key_muni_prov("General Santos", "South Cotabato") => "MINDANAO",
    key_muni_prov("Malaybalay", "Bukidnon") => "MINDANAO",
    key_muni_prov("Valencia", "Bukidnon") => "MINDANAO",
    key_muni_prov("Butuan", "Agusan del Norte") => "MINDANAO",
    key_muni_prov("Bayugan", "Agusan del Sur") => "MINDANAO",
    key_muni_prov("Surigao City", "Surigao del Norte") => "MINDANAO",
    key_muni_prov("Iligan", "Lanao del Norte") => "MINDANAO",
    key_muni_prov("Cotabato City", "Maguindanao") => "MINDANAO",
  ];
}

/**
 * SAFE municipality-only fallback.
 * We EXCLUDE conflict-prone names: "San Jose", "San Fernando".
 */
function get_safe_muni_only_region_map(): array {
  return [
    // NCR
    "manila" => "NCR",
    "quezon city" => "NCR",
    "makati" => "NCR",
    "pasig" => "NCR",
    "taguig" => "NCR",
    "mandaluyong" => "NCR",
    "parañaque" => "NCR",
    "paranaque" => "NCR",
    "las piñas" => "NCR",
    "las pinas" => "NCR",
    "muntinlupa" => "NCR",
    "caloocan" => "NCR",
    "marikina" => "NCR",
    "san juan" => "NCR",
    "pasay" => "NCR",
    "valenzuela" => "NCR",

    // NORTH
    "angeles" => "NORTH",
    "malolos" => "NORTH",
    "meycauayan" => "NORTH",
    "baliuag" => "NORTH",
    "cabanatuan" => "NORTH",
    "tarlac city" => "NORTH",
    "dagupan" => "NORTH",
    "urdaneta" => "NORTH",
    "baguio" => "NORTH",
    "laoag" => "NORTH",
    "vigan" => "NORTH",
    "olongapo" => "NORTH",

    // SOUTH
    "calamba" => "SOUTH",
    "santa rosa" => "SOUTH",
    "biñan" => "SOUTH",
    "binan" => "SOUTH",
    "san pedro" => "SOUTH",
    "dasmarinas" => "SOUTH",
    "dasmariñas" => "SOUTH",
    "imus" => "SOUTH",
    "bacoor" => "SOUTH",
    "tagaytay" => "SOUTH",
    "batangas city" => "SOUTH",
    "lipa" => "SOUTH",
    "tanauan" => "SOUTH",
    "antipolo" => "SOUTH",
    "taytay" => "SOUTH",
    "cainta" => "SOUTH",
    "lucena" => "SOUTH",
    "calapan" => "SOUTH",

    // VISAYAS
    "cebu city" => "VISAYAS",
    "mandaue" => "VISAYAS",
    "lapu-lapu" => "VISAYAS",
    "lapu lapu" => "VISAYAS",
    "iloilo city" => "VISAYAS",
    "bacolod" => "VISAYAS",
    "dumaguete" => "VISAYAS",
    "tagbilaran" => "VISAYAS",
    "tacloban" => "VISAYAS",
    "catbalogan" => "VISAYAS",
    "kalibo" => "VISAYAS",
    "roxas city" => "VISAYAS",

    // MINDANAO
    "davao city" => "MINDANAO",
    "cagayan de oro" => "MINDANAO",
    "zamboanga city" => "MINDANAO",
    "general santos" => "MINDANAO",
    "malaybalay" => "MINDANAO",
    "valencia" => "MINDANAO",
    "butuan" => "MINDANAO",
    "bayugan" => "MINDANAO",
    "surigao city" => "MINDANAO",
    "iligan" => "MINDANAO",
    "cotabato city" => "MINDANAO",
  ];
}

function resolve_region(string $municipality, string $province = ""): ?string {
  $muni = normalize_place($municipality);
  $prov = normalize_province($province);

  if ($muni === "") return null;

  $mp = get_muni_prov_region_map();
  if ($prov !== "") {
    $k = $muni . "|" . $prov;
    if (isset($mp[$k])) return $mp[$k];
  }

  $safe = get_safe_muni_only_region_map();
  return $safe[$muni] ?? null;
}