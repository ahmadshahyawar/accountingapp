<#
.SYNOPSIS
    Assembles a self-contained UnicAccountingShell build: the WPF exe, a
    portable PHP runtime, and a production copy of the Laravel app, all in
    one folder a user can copy anywhere and double-click - no PHP, no
    Composer, no Node, nothing else preinstalled.

.PARAMETER PhpZip
    Path to an already-downloaded portable PHP NTS x64 zip
    (e.g. php-8.2.33-nts-Win32-vs16-x64.zip from
    https://windows.php.net/downloads/releases/archives/). Required the
    first time; cached under shell/.publish-cache/ afterwards so re-running
    this script doesn't need it again.

.PARAMETER OutDir
    Where to assemble the build. Defaults to shell/dist next to this script.
#>
param(
    [string]$PhpZip,
    [string]$OutDir = (Join-Path $PSScriptRoot "dist")
)

$ErrorActionPreference = "Stop"
$repoRoot = Split-Path $PSScriptRoot -Parent
$cacheDir = Join-Path $PSScriptRoot ".publish-cache"
$phpCacheDir = Join-Path $cacheDir "php"

Write-Host "== 1/5: dotnet publish (self-contained win-x64) ==" -ForegroundColor Cyan
dotnet publish $PSScriptRoot -c Release -r win-x64 --self-contained true -o $OutDir
if ($LASTEXITCODE -ne 0) { throw "dotnet publish failed" }

Write-Host "== 2/5: portable PHP ==" -ForegroundColor Cyan
if (-not (Test-Path (Join-Path $phpCacheDir "php.exe"))) {
    if (-not $PhpZip) {
        throw "No cached portable PHP found under $phpCacheDir and -PhpZip was not given. " +
              "Download a *-nts-Win32-*-x64.zip from https://windows.php.net/downloads/releases/archives/ " +
              "and pass it via -PhpZip the first time."
    }
    New-Item -ItemType Directory -Force -Path $phpCacheDir | Out-Null
    Expand-Archive -Path $PhpZip -DestinationPath $phpCacheDir -Force

    # Enable exactly what Laravel needs; nothing else. See shell/README.md
    # for why config:cache must never be run against this build - env vars
    # (DB_DATABASE from MainWindow.xaml.cs) must stay live at runtime.
    #
    # php.ini-production ships with CRLF line endings. A "$" end-of-line
    # anchor in a (?m) multiline .NET regex does NOT match right before
    # "\r\n" (only right before the "\n", leaving a stray "\r" in between) -
    # so a pattern like "^;extension=curl$" silently matches nothing against
    # this file, and every "extension=" line stays commented out with no
    # error anywhere. Found by actually running the published build and
    # grepping the result, not by inspection: the regex looked correct and
    # PowerShell reported no error, but zero extensions ever got uncommented.
    # "\r?$" fixes the anchor; the explicit count check below exists so a
    # similar silent mismatch can never ship again undetected.
    $requiredExtensions = @('openssl', 'mbstring', 'pdo_sqlite', 'sqlite3', 'fileinfo', 'curl')
    $ini = Get-Content (Join-Path $phpCacheDir "php.ini-production") -Raw
    $ini = $ini -replace ';extension_dir = "ext"', 'extension_dir = "ext"'
    foreach ($ext in $requiredExtensions) {
        $ini = $ini -replace "(?m)^;extension=$ext\r?$", "extension=$ext"
    }
    Set-Content -Path (Join-Path $phpCacheDir "php.ini") -Value $ini -Encoding UTF8

    $stillCommented = $requiredExtensions | Where-Object { $ini -notmatch "(?m)^extension=$_\r?$" }
    if ($stillCommented) {
        throw "Failed to uncomment required PHP extension(s) in php.ini: $($stillCommented -join ', '). " +
              "php.ini-production's format may have changed - check the regex above against the actual file."
    }
    Write-Host "  Cached + configured portable PHP under $phpCacheDir (enabled: $($requiredExtensions -join ', '))"
} else {
    Write-Host "  Reusing cached portable PHP from $phpCacheDir"
}
$phpDestDir = Join-Path $OutDir "php"
if (Test-Path $phpDestDir) { Remove-Item -Recurse -Force $phpDestDir }
Copy-Item -Recurse -Path $phpCacheDir -Destination $phpDestDir

Write-Host "== 3/5: copy Laravel app (production files only) ==" -ForegroundColor Cyan
$appDestDir = Join-Path $OutDir "app_root"
if (Test-Path $appDestDir) { Remove-Item -Recurse -Force $appDestDir }
New-Item -ItemType Directory -Force -Path $appDestDir | Out-Null

$exclude = @('node_modules', 'tests', '.git', 'shell', 'storage\logs', 'storage\framework\cache\data',
             'storage\framework\sessions', 'storage\framework\views', 'database\database.sqlite')
robocopy $repoRoot $appDestDir /E /XD $($exclude | ForEach-Object { Join-Path $repoRoot $_ }) /XF "database.sqlite" "*.log" /NFL /NDL /NJH /NJS | Out-Null

# The four directories excluded above are excluded because they're runtime
# state (.gitignore'd, nothing to copy) - but Laravel needs the directories
# themselves to physically exist to write into, it doesn't create missing
# parents. Found by actually running the published build: the app got past
# every PHP/extension issue and then 500'd with "Please provide a valid
# cache path" the moment the first Blade view tried to compile, because
# storage\framework\views simply didn't exist in the copy.
foreach ($dir in @('storage\logs', 'storage\framework\cache\data', 'storage\framework\sessions',
                    'storage\framework\views', 'bootstrap\cache')) {
    New-Item -ItemType Directory -Force -Path (Join-Path $appDestDir $dir) | Out-Null
}

Write-Host "== 4/5: composer install --no-dev (production vendor/) ==" -ForegroundColor Cyan
Push-Location $appDestDir
try {
    composer install --no-dev --optimize-autoloader --no-interaction
    if ($LASTEXITCODE -ne 0) { throw "composer install failed" }
} finally {
    Pop-Location
}

Write-Host "== 5/5: sanity checks ==" -ForegroundColor Cyan
if (-not (Test-Path (Join-Path $appDestDir "artisan"))) { throw "app_root\artisan missing - copy step failed" }
if (-not (Test-Path (Join-Path $phpDestDir "php.exe"))) { throw "php\php.exe missing - PHP bundling failed" }
if (-not (Test-Path (Join-Path $OutDir "UnicAccountingShell.exe"))) { throw "UnicAccountingShell.exe missing - dotnet publish failed" }

Write-Host ""
Write-Host "Done. Self-contained build at: $OutDir" -ForegroundColor Green
Write-Host "It needs nothing preinstalled except the WebView2 Runtime (present on current Windows 10/11)."
