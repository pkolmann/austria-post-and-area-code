#!/usr/bin/php
<?php

const RTR_PLZ_URL = 'https://data.rtr.at/api/v1/tables/plz.json';
const OUTPUT_JSON = __DIR__.'/plz.json';
const OUTPUT_ZIP = __DIR__.'/plz.json.zip';

$rtrPayload = fetchRtrPlzData(RTR_PLZ_URL);
$latestWorkbook = findLatestWorkbook(__DIR__);

if ($latestWorkbook === null) {
    fwrite(STDERR, "Error: no file matching 'PLZ_Verzeichnis_*.xlsx' found in ".__DIR__."\n");
    exit(1);
}

$xlsxRows = loadPlzRowsFromXlsx($latestWorkbook);
if (count($xlsxRows) === 0) {
    fwrite(STDERR, "Error: workbook contains no usable PLZ rows: {$latestWorkbook}\n");
    exit(1);
}

$apiRows = isset($rtrPayload['data']) && is_array($rtrPayload['data']) ? $rtrPayload['data'] : array();
$mergedRows = mergeRows($apiRows, $xlsxRows);

$output = array(
    'timestamp' => date(DATE_ATOM),
    'source' => array(
        'rtr' => RTR_PLZ_URL,
        'rtr_timestamp' => extractRtrTimestamp($rtrPayload),
        'xlsx' => basename($latestWorkbook),
        'xlsxMtime' => date(DATE_ATOM, filemtime($latestWorkbook) ?: time()),
    ),
    'data' => array_values($mergedRows),
);

$json = json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
if ($json === false) {
    fwrite(STDERR, "Error encoding output JSON: ".json_last_error_msg()."\n");
    exit(1);
}

file_put_contents(OUTPUT_JSON, $json."\n");
writeZip(OUTPUT_JSON, OUTPUT_ZIP);

printf(
    "Done. API rows: %d, XLSX rows: %d, merged rows: %d\nWrote: %s\nWrote: %s\n",
    count($apiRows),
    count($xlsxRows),
    count($mergedRows),
    OUTPUT_JSON,
    OUTPUT_ZIP
);

function fetchRtrPlzData($url): array
{
    $raw = @file_get_contents($url);
    if ($raw === false) {
        fwrite(STDERR, "Warning: RTR data fetch failed, continuing with XLSX only.\n");
        return array('data' => array());
    }

    $payload = json_decode($raw, true);
    if (!is_array($payload)) {
        fwrite(STDERR, "Warning: RTR data parse failed (".json_last_error_msg()."), continuing with XLSX only.\n");
        return array('data' => array());
    }

    return $payload;
}

function findLatestWorkbook($directory)
{
    $matches = glob($directory.'/PLZ_Verzeichnis_*.xlsx');
    if (!is_array($matches) || count($matches) === 0) {
        return null;
    }

    usort($matches, 'compareByFileMtimeDesc');

    return $matches[0] ?? null;
}

function compareByFileMtimeDesc($a, $b): int
{
    $timeA = filemtime($a);
    $timeB = filemtime($b);

    if ($timeA === $timeB) {
        return 0;
    }

    return ($timeA > $timeB) ? -1 : 1;
}

function loadPlzRowsFromXlsx($xlsxPath): array
{
    $zip = new ZipArchive();
    if ($zip->open($xlsxPath) !== true) {
        throw new RuntimeException("Unable to open XLSX: {$xlsxPath}");
    }

    $sheetPath = resolveFirstSheetPath($zip);
    $sharedStrings = loadSharedStrings($zip);
    $xml = $zip->getFromName($sheetPath);
    $zip->close();

    if ($xml === false) {
        throw new RuntimeException("Unable to read worksheet XML: {$sheetPath}");
    }

    $sheet = simplexml_load_string($xml);
    if ($sheet === false) {
        throw new RuntimeException('Workbook sheet data is missing or invalid.');
    }

    $sheetRows = $sheet->xpath('//*[local-name()="sheetData"]/*[local-name()="row"]');
    if ($sheetRows === false || count($sheetRows) === 0) {
        throw new RuntimeException('Workbook sheet data is missing or invalid.');
    }

    $headerByColumn = array();
    $rows = array();

    foreach ($sheetRows as $row) {
        $cells = array();
        $rowCells = $row->xpath('./*[local-name()="c"]');
        if ($rowCells === false) {
            continue;
        }
        foreach ($rowCells as $cell) {
            $cellRef = (string)$cell['r'];
            $column = extractColumn($cellRef);
            if ($column === '') {
                continue;
            }
            $cells[$column] = readCellValue($cell, $sharedStrings);
        }

        if (count($headerByColumn) === 0) {
            foreach ($cells as $column => $headerValue) {
                $headerByColumn[$column] = normalizeHeader((string)$headerValue);
            }
            continue;
        }

        $mapped = mapRowByHeader($cells, $headerByColumn);
        $normalized = normalizeWorkbookRow($mapped);
        if ($normalized !== null) {
            $rows[] = $normalized;
        }
    }

    return $rows;
}

function resolveFirstSheetPath($zip): string
{
    $workbookXml = $zip->getFromName('xl/workbook.xml');
    $relsXml = $zip->getFromName('xl/_rels/workbook.xml.rels');

    if ($workbookXml === false || $relsXml === false) {
        return 'xl/worksheets/sheet1.xml';
    }

    $workbook = simplexml_load_string($workbookXml);
    $rels = simplexml_load_string($relsXml);

    if ($workbook === false || $rels === false) {
        return 'xl/worksheets/sheet1.xml';
    }

    $sheets = $workbook->xpath('//*[local-name()="sheets"]/*[local-name()="sheet"]');
    if ($sheets === false || !isset($sheets[0])) {
        return 'xl/worksheets/sheet1.xml';
    }

    $firstSheet = $sheets[0];
    $attributes = $firstSheet->attributes('r', true);
    $sheetRelId = isset($attributes['id']) ? (string)$attributes['id'] : '';

    if ($sheetRelId === '') {
        return 'xl/worksheets/sheet1.xml';
    }

    $relationships = $rels->xpath('//*[local-name()="Relationship"]');
    if ($relationships === false) {
        return 'xl/worksheets/sheet1.xml';
    }

    foreach ($relationships as $relationship) {
        if ((string)$relationship['Id'] !== $sheetRelId) {
            continue;
        }

        $target = (string)$relationship['Target'];
        if (str_starts_with($target, '/')) {
            return ltrim($target, '/');
        }

        if (str_starts_with($target, 'xl/')) {
            return $target;
        }

        return 'xl/'.$target;
    }

    return 'xl/worksheets/sheet1.xml';
}

function loadSharedStrings($zip): array
{
    $sharedStringsXml = $zip->getFromName('xl/sharedStrings.xml');
    if ($sharedStringsXml === false) {
        return array();
    }

    $sharedStrings = simplexml_load_string($sharedStringsXml);
    if ($sharedStrings === false) {
        return array();
    }

    $items = $sharedStrings->xpath('//*[local-name()="si"]');
    if ($items === false || count($items) === 0) {
        return array();
    }

    $out = array();
    foreach ($items as $item) {
        $text = '';
        $parts = $item->xpath('.//*[local-name()="t"]');
        if ($parts !== false) {
            foreach ($parts as $node) {
                $text .= (string)$node;
            }
        }
        $out[] = $text;
    }

    return $out;
}

function extractColumn($cellRef): string
{
    if (preg_match('/^([A-Z]+)\d+$/', $cellRef, $matches) !== 1) {
        return '';
    }

    return $matches[1];
}

function readCellValue($cell, $sharedStrings): string
{
    $type = (string)$cell['t'];

    if ($type === 'inlineStr') {
        $inlineParts = $cell->xpath('.//*[local-name()="t"]');
        if ($inlineParts !== false && count($inlineParts) > 0) {
            $inlineText = '';
            foreach ($inlineParts as $part) {
                $inlineText .= (string)$part;
            }
            return trim($inlineText);
        }
    }

    $valueNodes = $cell->xpath('./*[local-name()="v"]');
    $value = ($valueNodes !== false && isset($valueNodes[0])) ? (string)$valueNodes[0] : '';
    if ($type === 's') {
        $index = (int)$value;
        return isset($sharedStrings[$index]) ? trim($sharedStrings[$index]) : '';
    }

    return trim($value);
}

function normalizeHeader($header): array|string
{
    $header = strtolower(trim($header));
    $header = str_replace(
        array('ä', 'ö', 'ü', 'ß'),
        array('ae', 'oe', 'ue', 'ss'),
        $header
    );
    $header = str_replace(array(' ', '-', '_'), '', $header);
    return str_replace(array('.', ',', ':'), '', $header);
}

function mapRowByHeader($cells, $headerByColumn): array
{
    $mapped = array();
    foreach ($cells as $column => $value) {
        if (!isset($headerByColumn[$column])) {
            continue;
        }
        $mapped[$headerByColumn[$column]] = $value;
    }
    return $mapped;
}

function normalizeWorkbookRow($row): ?array
{
    $plzRaw = isset($row['plz']) ? (string)$row['plz'] : '';
    $plz = preg_replace('/\D+/', '', $plzRaw);
    if ($plz === '') {
        return null;
    }

    $gueltigAbRaw = null;
    if (isset($row['gueltigab'])) {
        $gueltigAbRaw = $row['gueltigab'];
    } elseif (isset($row['gltigab'])) {
        $gueltigAbRaw = $row['gltigab'];
    }

    $gueltigBisRaw = null;
    if (isset($row['gueltigbis'])) {
        $gueltigBisRaw = $row['gueltigbis'];
    } elseif (isset($row['gltigbis'])) {
        $gueltigBisRaw = $row['gltigbis'];
    }

    $gueltigAb = normalizeDateValue($gueltigAbRaw);
    $gueltigBis = normalizeDateValue($gueltigBisRaw);

    return array(
        'plz' => (int)$plz,
        'ort' => trim(isset($row['ort']) ? (string)$row['ort'] : ''),
        'bundesland' => trim(isset($row['bundesland']) ? (string)$row['bundesland'] : ''),
        'gueltigab' => $gueltigAb,
        'gueltigbis' => $gueltigBis,
        'plztyp' => trim(isset($row['nameplztyp']) ? (string)$row['nameplztyp'] : ''),
        'internextern' => trim(isset($row['internextern']) ? (string)$row['internextern'] : ''),
        'adressierbar' => trim(isset($row['adressierbar']) ? (string)$row['adressierbar'] : ''),
        'postfach' => trim(isset($row['postfach']) ? (string)$row['postfach'] : ''),
    );
}

function normalizeDateValue($raw): float|int|string|null
{
    $value = trim((string)$raw);
    if ($value === '' || strtoupper($value) === 'NULL') {
        return null;
    }

    if (is_numeric($value)) {
        // Excel date serials are days since 1899-12-30.
        $days = (int)$value;
        $timestamp = strtotime('1899-12-30 +'.$days.' days');
        return $timestamp === false ? $value : date('Y-m-d', $timestamp);
    }

    $timestamp = strtotime($value);
    if ($timestamp === false) {
        return $value;
    }

    return date('Y-m-d', $timestamp);
}

function normalizeApiRow($row): ?array
{
    if (!isset($row['plz'])) {
        return null;
    }

    return array(
        'plz' => (int)$row['plz'],
        'ort' => isset($row['ort']) ? (string)$row['ort'] : '',
        'bundesland' => isset($row['bundesland']) ? (string)$row['bundesland'] : '',
        'gueltigab' => empty($row['gueltigab']) ? null : (string)$row['gueltigab'],
        'gueltigbis' => empty($row['gueltigbis']) ? null : (string)$row['gueltigbis'],
        'plztyp' => isset($row['plztyp']) ? (string)$row['plztyp'] : '',
        'internextern' => isset($row['internextern']) ? (string)$row['internextern'] : '',
        'adressierbar' => isset($row['adressierbar']) ? (string)$row['adressierbar'] : '',
        'postfach' => isset($row['postfach']) ? (string)$row['postfach'] : '',
    );
}

function mergeRows($apiRows, $xlsxRows): array
{
    $merged = array();

    foreach ($apiRows as $apiRow) {
        if (!is_array($apiRow)) {
            continue;
        }
        $normalized = normalizeApiRow($apiRow);
        if ($normalized === null) {
            continue;
        }
        $merged[makeRowKey($normalized)] = $normalized;
    }

    // Prefer workbook values when the same PLZ/Ort/type tuple exists.
    foreach ($xlsxRows as $xlsxRow) {
        $merged[makeRowKey($xlsxRow)] = $xlsxRow;
    }

    uasort($merged, 'compareRowsForSort');

    return $merged;
}

function compareRowsForSort($a, $b): int
{
    $left = array((int)$a['plz'], (string)$a['ort'], (string)$a['plztyp']);
    $right = array((int)$b['plz'], (string)$b['ort'], (string)$b['plztyp']);

    if ($left == $right) {
        return 0;
    }

    return ($left < $right) ? -1 : 1;
}

function makeRowKey($row): string
{
    return implode('|', array(
        (string)$row['plz'],
        strtolower(trim((string)$row['ort'])),
        strtolower(trim((string)$row['bundesland'])),
        strtolower(trim((string)$row['plztyp'])),
    ));
}

function writeZip($sourceFile, $zipFile): void
{
    $zip = new ZipArchive();
    if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
        throw new RuntimeException("Unable to create zip file: {$zipFile}");
    }

    $zip->addFile($sourceFile, basename($sourceFile));
    $zip->close();
}

function extractRtrTimestamp($rtrPayload): ?string
{
    if (is_array($rtrPayload) && isset($rtrPayload['timestamp']) && $rtrPayload['timestamp'] !== '') {
        return (string)$rtrPayload['timestamp'];
    }

    return null;
}

