# Runs YAK Pro - Php Obfuscator on this project (Windows PowerShell)
# Prereqs: git, php (cli)

param(
  [string]$ToolDir = "$PSScriptRoot\yakpro-po"
)

$ErrorActionPreference = 'Stop'

if (!(Test-Path $ToolDir)) {
  Write-Host "Cloning yakpro-po..." -ForegroundColor Cyan
  git clone https://github.com/pk-fr/yakpro-po.git $ToolDir
  Push-Location $ToolDir
  git clone https://github.com/nikic/PHP-Parser.git --branch 4.x
  Pop-Location
}

$yak = Join-Path $ToolDir 'yakpro-po.php'
if (!(Test-Path $yak)) { throw "yakpro-po.php not found in $ToolDir" }

# Use project config yakpro-po.cnf
$env:YAKPRO_PO_CONFIG_FILE = Join-Path $PSScriptRoot '..' 'yakpro-po.cnf'

# Run
php "$yak" --help > $null
php "$yak" . -o ..\build\yakpro

Write-Host "Done. Output in build/yakpro/yakpro-po" -ForegroundColor Green
