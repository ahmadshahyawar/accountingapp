<#
.SYNOPSIS
  Builds a single self-extracting UnicAccounting-Setup.exe: runs publish.ps1
  to produce dist/ (shell exe, uninstaller exe, bundled PHP, production app
  copy), zips that as a payload, publishes the UnicAccounting.Setup wizard as
  a self-contained single-file exe, then appends the payload zip + a footer
  (8-byte original-exe length + 16-byte magic string) onto the wizard exe.
  MainWindow.xaml.cs (in UnicAccounting.Setup) reads that same footer at
  runtime to find and extract its own embedded payload - no third-party
  installer tooling involved. Mirrors HSG-desktop's build-installer.ps1.

.PARAMETER PhpZip
  Forwarded to publish.ps1 - only needed the first time a portable PHP isn't
  already cached under shell/.publish-cache/.

.NOTES
  Run this after any change to the shell, the Laravel app, or the setup
  wizard itself - it always rebuilds dist/ from scratch via publish.ps1
  first, so the payload can't go stale.
#>

param(
    [string]$PhpZip,
    [string]$OutFile = "$PSScriptRoot\dist-installer\UnicAccounting-Setup.exe"
)

$ErrorActionPreference = "Stop"
$FooterMagic = "UNICPAYLOADEND!!"  # must match UnicAccounting.Setup/MainWindow.xaml.cs FooterMagic exactly (16 bytes)

Write-Host "=== 1/4: Publishing app payload (dist/) ===" -ForegroundColor Cyan
if ($PhpZip) {
    & "$PSScriptRoot\publish.ps1" -PhpZip $PhpZip
} else {
    & "$PSScriptRoot\publish.ps1"
}
if ($LASTEXITCODE -ne 0 -and $null -ne $LASTEXITCODE) { throw "publish.ps1 failed" }

$payloadDir = "$PSScriptRoot\dist"
$payloadZip = "$PSScriptRoot\dist-installer\payload.zip"
New-Item -ItemType Directory -Force -Path (Split-Path $payloadZip) | Out-Null
if (Test-Path $payloadZip) { Remove-Item $payloadZip -Force }

Write-Host "=== 2/4: Zipping payload ===" -ForegroundColor Cyan
Compress-Archive -Path "$payloadDir\*" -DestinationPath $payloadZip -CompressionLevel Optimal
Write-Host "  Payload zip: $payloadZip ($([math]::Round((Get-Item $payloadZip).Length / 1MB, 1)) MB)"

Write-Host "=== 3/4: Publishing setup wizard (self-contained single-file win-x64) ===" -ForegroundColor Cyan
$setupPublishDir = "$PSScriptRoot\dist-installer\setup-publish"
if (Test-Path $setupPublishDir) { Remove-Item $setupPublishDir -Recurse -Force }
dotnet publish "$PSScriptRoot\UnicAccounting.Setup\UnicAccounting.Setup.csproj" `
    -c Release -r win-x64 --self-contained true `
    -p:PublishSingleFile=true -p:IncludeNativeLibrariesForSelfExtract=true `
    -o $setupPublishDir
if ($LASTEXITCODE -ne 0) { throw "dotnet publish (UnicAccounting.Setup) failed" }

$setupExe = Join-Path $setupPublishDir "UnicAccounting-Setup.exe"
if (-not (Test-Path $setupExe)) { throw "Expected setup exe not found at $setupExe" }

Write-Host "=== 4/4: Appending payload + footer onto setup exe ===" -ForegroundColor Cyan
if (Test-Path $OutFile) { Remove-Item $OutFile -Force }
New-Item -ItemType Directory -Force -Path (Split-Path $OutFile) | Out-Null

$originalExeSize = (Get-Item $setupExe).Length

Copy-Item $setupExe $OutFile

$out = [System.IO.File]::Open($OutFile, [System.IO.FileMode]::Append)
try {
    $zipBytes = [System.IO.File]::ReadAllBytes($payloadZip)
    $out.Write($zipBytes, 0, $zipBytes.Length)

    $lenBytes = [System.BitConverter]::GetBytes([int64]$originalExeSize)
    $out.Write($lenBytes, 0, $lenBytes.Length)

    $magicBytes = [System.Text.Encoding]::ASCII.GetBytes($FooterMagic)
    $out.Write($magicBytes, 0, $magicBytes.Length)
} finally {
    $out.Close()
}

Remove-Item $payloadZip -Force
Remove-Item $setupPublishDir -Recurse -Force

Write-Host ""
Write-Host "Done: $OutFile ($([math]::Round((Get-Item $OutFile).Length / 1MB, 1)) MB)" -ForegroundColor Green
Write-Host "This single file is the whole installer - copy it anywhere and run it."
