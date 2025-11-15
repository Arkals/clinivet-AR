<?php
// Simple build-time obfuscation/minification tool for Clinivet
// - Removes comments and excess whitespace from PHP
// - Lightly minifies JS/CSS
// - Copies everything else
// OUTPUT: build/obfuscated/...
// NOTE: It intentionally SKIPS view templates to avoid breaking variable scope
//       and skips vendor/uploads/storage.

declare(strict_types=1);

$root = realpath(__DIR__ . '/..');
if ($root === false) { fwrite(STDERR, "Cannot resolve project root\n"); exit(1); }
$outRoot = $root . DIRECTORY_SEPARATOR . 'build' . DIRECTORY_SEPARATOR . 'obfuscated';

$skipDirs = [
    $root . DIRECTORY_SEPARATOR . 'vendor',
    $root . DIRECTORY_SEPARATOR . 'storage',
    $root . DIRECTORY_SEPARATOR . 'build',
    $root . DIRECTORY_SEPARATOR . 'frontend' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'uploads',
    $root . DIRECTORY_SEPARATOR . '.git'
];

$skipDirPrefixes = array_map(function($p){ return rtrim($p, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR; }, $skipDirs);

$skipPhpFiles = [
    // Add specific files to skip if needed
];

@mkdir($outRoot, 0777, true);

$rii = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS | FilesystemIterator::FOLLOW_SYMLINKS),
    RecursiveIteratorIterator::SELF_FIRST
);

$copied = 0; $minified = 0; $skipped = 0; $errors = 0;
foreach ($rii as $fileInfo) {
    $path = $fileInfo->getPathname();
    if (strpos($path, $outRoot) === 0) { $skipped++; continue; }

    // Skip directories we don't want to process
    $skip = false;
    foreach ($skipDirPrefixes as $prefix) {
        if (strpos($path, $prefix) === 0) { $skip = true; break; }
    }
    if ($skip) { $skipped++; continue; }

    $rel = substr($path, strlen($root) + 1);
    $target = $outRoot . DIRECTORY_SEPARATOR . $rel;

    if ($fileInfo->isDir()) {
        @mkdir($target, 0777, true);
        continue;
    }

    $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

    // Skip PHP view templates to avoid variable-scope issues
    $isView = (strpos($path, DIRECTORY_SEPARATOR . 'frontend' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Views' . DIRECTORY_SEPARATOR) !== false);
    if ($ext === 'php' && $isView) {
        // just copy as-is
        @mkdir(dirname($target), 0777, true);
        if (!copy($path, $target)) { $errors++; } else { $copied++; }
        continue;
    }

    if ($ext === 'php') {
        // Basic PHP minify via tokens (remove comments, compress whitespace)
        $code = file_get_contents($path);
        if ($code === false) { $errors++; continue; }
        $min = php_minify($code);
        @mkdir(dirname($target), 0777, true);
        if (file_put_contents($target, $min) === false) { $errors++; } else { $minified++; }
    } elseif ($ext === 'js') {
        $src = file_get_contents($path);
        if ($src === false) { $errors++; continue; }
        $min = minify_js_css($src);
        @mkdir(dirname($target), 0777, true);
        if (file_put_contents($target, $min) === false) { $errors++; } else { $minified++; }
    } elseif ($ext === 'css') {
        $src = file_get_contents($path);
        if ($src === false) { $errors++; continue; }
        $min = minify_js_css($src);
        @mkdir(dirname($target), 0777, true);
        if (file_put_contents($target, $min) === false) { $errors++; } else { $minified++; }
    } else {
        @mkdir(dirname($target), 0777, true);
        if (!copy($path, $target)) { $errors++; } else { $copied++; }
    }
}

echo "Obfuscation build complete.\n";
echo "- Minified files: $minified\n";
echo "- Copied files:   $copied\n";
echo "- Skipped items:  $skipped\n";
if ($errors) echo "- Errors:         $errors\n";

// ---------------- helpers ---------------- //
function php_minify(string $code): string {
    $tokens = token_get_all($code);
    $out = '';
    $prevNonSpace = '';
    foreach ($tokens as $tok) {
        if (is_string($tok)) { // simple 1-char token
            $out .= $tok;
            $prevNonSpace = $tok;
            continue;
        }
        list($id, $text) = $tok;
        switch ($id) {
            case T_COMMENT:
            case T_DOC_COMMENT:
                // drop comments
                break;
            case T_WHITESPACE:
                // compress whitespace to a single space only when needed
                if (!needs_space($prevNonSpace)) {
                    // no space needed
                } else {
                    $out .= ' ';
                }
                break;
            default:
                $out .= $text;
                if (trim($text) !== '') {
                    $prevNonSpace = $text[strlen($text)-1];
                }
        }
    }
    // remove trailing spaces/tabs
    $out = preg_replace("/[\t ]+$/m", '', $out);
    return $out;
}

function needs_space(string $prev): bool {
    // add a space between identifiers/numbers to avoid token merging
    return ctype_alnum($prev) || $prev === '_' || $prev === '$';
}

function minify_js_css(string $s): string {
    // naive removal of comments + whitespace
    $s = preg_replace('#/\*.*?\*/#s', '', $s);      // /* */ comments
    $s = preg_replace('#(^|\s)//.*$#m', '$1', $s);   // // comments
    $s = preg_replace("/\s+/", ' ', $s);
    $s = preg_replace("/\s*([{};,:=\(\)\[\]<>\+\-\*\|&!\?])\s*/", '$1', $s);
    return trim($s);
}
