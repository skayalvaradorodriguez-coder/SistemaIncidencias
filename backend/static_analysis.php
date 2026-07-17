<?php
/**
 * static_analysis.php
 * Analizador estático local reproducible (formato "Log 1").
 *
 * Uso:
 *   php static_analysis.php > static_analysis.log
 *
 * Analiza: app/, database/, tests/, resources/views/
 * Excluye: vendor/, node_modules/, public/plugins/, storage/, bootstrap/cache/
 */

$root = $argv[1] ?? __DIR__;
$targets = ['app', 'database', 'tests', 'resources/views'];
$excludeDirs = ['vendor', 'node_modules', 'plugins', 'storage', 'bootstrap/cache', '.git'];

function shouldExclude($path, $excludeDirs) {
    foreach ($excludeDirs as $ex) {
        if (strpos($path, DIRECTORY_SEPARATOR . $ex . DIRECTORY_SEPARATOR) !== false ||
            strpos($path, DIRECTORY_SEPARATOR . $ex) === strlen($path) - strlen(DIRECTORY_SEPARATOR . $ex)) {
            return true;
        }
    }
    return false;
}

function collectFiles($root, $targets, $excludeDirs) {
    $files = [];
    foreach ($targets as $target) {
        $base = $root . DIRECTORY_SEPARATOR . $target;
        if (!is_dir($base)) continue;
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($base, FilesystemIterator::SKIP_DOTS)
        );
        foreach ($iterator as $file) {
            $path = $file->getPathname();
            if (shouldExclude($path, $excludeDirs)) continue;
            $ext = strtolower($file->getExtension());
            $name = $file->getFilename();
            if (str_ends_with($name, '.blade.php')) {
                $files[] = ['path' => $path, 'type' => 'blade'];
            } elseif ($ext === 'php') {
                $files[] = ['path' => $path, 'type' => 'php'];
            } elseif ($ext === 'js') {
                $files[] = ['path' => $path, 'type' => 'js'];
            }
        }
    }
    return $files;
}

function analyzeLines($lines) {
    $physical = count($lines);
    $blank = 0;
    $comment = 0;
    $inBlockComment = false;
    foreach ($lines as $line) {
        $trim = trim($line);
        if ($trim === '') { $blank++; continue; }
        if ($inBlockComment) {
            $comment++;
            if (strpos($trim, '*/') !== false) $inBlockComment = false;
            continue;
        }
        if (str_starts_with($trim, '//') || str_starts_with($trim, '#') || str_starts_with($trim, '*')) {
            $comment++;
            continue;
        }
        if (str_starts_with($trim, '/*')) {
            $comment++;
            if (strpos($trim, '*/') === false) $inBlockComment = true;
            continue;
        }
    }
    $code = $physical - $blank - $comment;
    return [$physical, $code, $comment, $blank];
}

function findMethods($path, $lines) {
    $methods = [];
    $totalLines = count($lines);
    for ($i = 0; $i < $totalLines; $i++) {
        if (preg_match('/function\s+([A-Za-z0-9_]+)\s*\(/', $lines[$i], $m)) {
            $name = $m[1];
            $startLine = $i + 1;
            // localizar el bloque por conteo de llaves
            $depth = 0;
            $started = false;
            $endLine = $startLine;
            $body = [];
            for ($j = $i; $j < $totalLines; $j++) {
                $body[] = $lines[$j];
                $opens = substr_count($lines[$j], '{');
                $closes = substr_count($lines[$j], '}');
                if ($opens > 0) $started = true;
                $depth += $opens - $closes;
                if ($started && $depth <= 0) {
                    $endLine = $j + 1;
                    break;
                }
                if ($j - $i > 400) { $endLine = $j + 1; break; } // límite de seguridad
            }
            $loc = $endLine - $startLine + 1;
            $bodyText = implode("\n", $body);
            // Complejidad ciclomática aproximada: 1 base + puntos de decisión
            $cc = 1;
            $cc += preg_match_all('/\bif\s*\(/', $bodyText);
            $cc += preg_match_all('/\belseif\s*\(/', $bodyText);
            $cc += preg_match_all('/\bfor\s*\(/', $bodyText);
            $cc += preg_match_all('/\bforeach\s*\(/', $bodyText);
            $cc += preg_match_all('/\bwhile\s*\(/', $bodyText);
            $cc += preg_match_all('/\bcase\s+/', $bodyText);
            $cc += preg_match_all('/\bcatch\s*\(/', $bodyText);
            $cc += preg_match_all('/&&/', $bodyText);
            $cc += preg_match_all('/\|\|/', $bodyText);
            $cc += preg_match_all('/\?\?/', $bodyText);
            $cc += preg_match_all('/\?(?!\?)/', $bodyText); // ternario simple

            $methods[] = [
                'file' => $path,
                'line' => $startLine,
                'name' => $name,
                'loc' => $loc,
                'cc' => $cc,
            ];
            $i = $endLine - 1;
        }
    }
    return $methods;
}

function normalizeBlockLine($line) {
    return trim(preg_replace('/\s+/', ' ', $line));
}

// ---------------------------------------------------------------------
$files = collectFiles($root, $targets, $excludeDirs);

$totalFiles = count($files);
$phpFiles = count(array_filter($files, fn($f) => $f['type'] === 'php'));
$bladeFiles = count(array_filter($files, fn($f) => $f['type'] === 'blade'));
$jsFiles = count(array_filter($files, fn($f) => $f['type'] === 'js'));

$totalPhysical = 0;
$totalCode = 0;
$totalComment = 0;
$totalBlank = 0;

$allMethods = [];
$fileSizes = []; // path => physical lines

// Para duplicación: bloques de 6 líneas normalizadas
$blockSize = 6;
$blockMap = []; // hash => count

foreach ($files as $f) {
    $raw = file($f['path']);
    if ($raw === false) continue;
    $lines = array_map(fn($l) => rtrim($l, "\r\n"), $raw);

    [$physical, $code, $comment, $blank] = analyzeLines($lines);
    $totalPhysical += $physical;
    $totalCode += $code;
    $totalComment += $comment;
    $totalBlank += $blank;

    $relPath = ltrim(str_replace($root, '', $f['path']), DIRECTORY_SEPARATOR);
    $relPath = str_replace('\\', '/', $relPath);
    $fileSizes[$relPath] = $physical;

    if ($f['type'] === 'php' || $f['type'] === 'blade') {
        $methods = findMethods($relPath, $lines);
        $allMethods = array_merge($allMethods, $methods);
    }

    // bloques para duplicación (solo líneas no vacías, normalizadas)
    $nonBlank = array_values(array_filter($lines, fn($l) => trim($l) !== ''));
    $n = count($nonBlank);
    for ($i = 0; $i + $blockSize <= $n; $i += $blockSize) {
        $block = array_slice($nonBlank, $i, $blockSize);
        $norm = array_map('normalizeBlockLine', $block);
        $hash = md5(implode("\n", $norm));
        if (!isset($blockMap[$hash])) $blockMap[$hash] = 0;
        $blockMap[$hash]++;
    }
}

// Duplicación aproximada
$duplicateGroups = 0;
$duplicateLineOccurrences = 0;
foreach ($blockMap as $hash => $count) {
    if ($count > 1) {
        $duplicateGroups++;
        $duplicateLineOccurrences += ($count - 1) * $blockSize;
    }
}
$duplicationDensity = $totalCode > 0 ? round(($duplicateLineOccurrences / $totalCode) * 100, 2) : 0;

// Top métodos por complejidad (y luego por LOC)
usort($allMethods, function ($a, $b) {
    if ($b['cc'] !== $a['cc']) return $b['cc'] <=> $a['cc'];
    return $b['loc'] <=> $a['loc'];
});
$topMethods = array_slice($allMethods, 0, 10);

// Top archivos más grandes
arsort($fileSizes);
$topFiles = array_slice($fileSizes, 0, 5, true);

// ---------------------------------------------------------------------
// Salida con el mismo formato del Log 1
echo "ANALISIS ESTATICO LOCAL - SISTEMA DE INCIDENCIAS\n";
echo str_repeat("=", 55) . "\n";
echo "files: {$totalFiles}\n";
echo "php_files: {$phpFiles}\n";
echo "blade_files: {$bladeFiles}\n";
echo "js_files: {$jsFiles}\n";
echo "physical_lines: {$totalPhysical}\n";
echo "code_lines: {$totalCode}\n";
echo "comment_lines: {$totalComment}\n";
echo "blank_lines: {$totalBlank}\n";
echo "duplicate_groups_6line: {$duplicateGroups}\n";
echo "duplicate_line_occurrences: {$duplicateLineOccurrences}\n";
echo "duplication_density_approx_pct: {$duplicationDensity}\n";
echo "\n";
echo "METODOS CON MAYOR COMPLEJIDAD APROXIMADA\n";
foreach ($topMethods as $m) {
    echo "{$m['file']}:{$m['line']} {$m['name']} LOC={$m['loc']} CC~={$m['cc']}\n";
}
echo "\n";
echo "ARCHIVOS MAS GRANDES\n";
foreach ($topFiles as $path => $lines) {
    echo "{$path}: {$lines} lineas de codigo\n";
}
