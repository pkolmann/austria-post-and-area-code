<?php

$start = 1010;
$maxDist = 30;
$order = 'P';

function printHelp() {
    print "Usage:\n";
    print "-s PLZ: PLZ des Startortes\n";
    print "-d KM: Distanz in km\n";
    print "-D: Sortiert nach Distanz (Sonst nach PLZ)\n";
    die("\n\n");
}

$opt = getOpt("d:Dhs:");
if (
    array_key_exists('d', $opt)
    && is_numeric($opt['d'])
) {
    $maxDist = floatval($opt['d']);
    }

if (array_key_exists('D', $opt)) {
    $order = "D";
}

if (array_key_exists('s', $opt)) {
    $start = $opt['s'];
}

function calculateDistance($lat1, $lon1, $lat2, $lon2) {
    // Radius of the Earth in meters
    $earthRadius = 6371; // Approximate value for WGS84

    // Convert latitude and longitude from degrees to radians
    $lat1Rad = deg2rad($lat1);
    $lon1Rad = deg2rad($lon1);
    $lat2Rad = deg2rad($lat2);
    $lon2Rad = deg2rad($lon2);

    // Haversine formula
    $deltaLat = $lat2Rad - $lat1Rad;
    $deltaLon = $lon2Rad - $lon1Rad;
    $a = sin($deltaLat / 2) * sin($deltaLat / 2) +
         cos($lat1Rad) * cos($lat2Rad) *
         sin($deltaLon / 2) * sin($deltaLon / 2);
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    $distance = $earthRadius * $c;

    return $distance;
}

$data = json_decode(file_get_contents('vorwahlen+plz.json'), true);

if (is_null($data)) {
    print "Error loading vorwahlen+plz.json!\n\n";
}

$startCity = null;
foreach ($data['features'] as $g) {
    if (!array_key_exists('properties', $g)) continue;
    if (!array_key_exists('plz', $g['properties'])) continue;

    if (array_key_exists($start, $g['properties']['plz'])) {
        $startCity = $g['properties'];
        break;
    }
}

$latitude1 = $startCity['centroid'][1];
$longitude1 = $startCity['centroid'][0];
$exp = [];
foreach ($data['features'] as $g) {
    if (!array_key_exists('plz', $g['properties'])) continue;
    $latitude2 = $g['properties']['centroid'][1];
    $longitude2 = $g['properties']['centroid'][0];
    $distance = calculateDistance($latitude1, $longitude1, $latitude2, $longitude2);
    if ($distance > $maxDist) continue;

    #print "Distance between {$startCity['name']} and {$g['properties']['name']}: ";
    #print round($distance) . " km \n\n";

    foreach ($g['properties']['plz'] as $plz => $name) {
        $key = $plz;
        if ($order == 'D') {
            $key = intval(round($distance * 1000000));
        }
        $exp[$key][] = [
            'plz'  => $plz,
            'name' => $name,
            'dist' => round($distance)
        ];
    }
}

ksort($exp);

foreach ($exp as $x) {
    foreach ($x as $e) {
        print "{$e['plz']} {$e['name']} - {$e['dist']}km\n";
    }
}
