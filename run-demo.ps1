param(
    [switch]$Seed
)

$ErrorActionPreference = 'Stop'

# Demo launcher đã được test thực tế:
# - clear cache
# - reset database demo khi dùng -Seed
# - mở Boost MCP
# - mở Laravel server trên port 8002
# - mở Vite ở terminal riêng

$RepoRoot = Split-Path -Parent $MyInvocation.MyCommand.Path
$Php84 = 'C:\Users\Voduybinhv\AppData\Local\Microsoft\WinGet\Packages\PHP.PHP.8.4_Microsoft.Winget.Source_8wekyb3d8bbwe\php.exe'
$LaravelPort = 8002
$LaravelUrl = "http://127.0.0.1:$LaravelPort"
$ProxyCleanup = "Remove-Item Env:ALL_PROXY,Env:GIT_HTTP_PROXY,Env:GIT_HTTPS_PROXY,Env:HTTP_PROXY,Env:HTTPS_PROXY -ErrorAction SilentlyContinue;"

if (-not (Test-Path $Php84)) {
    throw "Khong tim thay PHP 8.4 tai: $Php84"
}

Push-Location $RepoRoot
try {
    Write-Host '==> Kiem tra PHP 8.4' -ForegroundColor Cyan
    & $Php84 -v | Out-Host

    Write-Host '==> Clear cache' -ForegroundColor Cyan
    & $Php84 artisan optimize:clear

    if ($Seed) {
        Write-Host '==> Reset database demo' -ForegroundColor Cyan
        & $Php84 artisan migrate:fresh --seed
    }

    Write-Host '==> Mo Boost MCP, Vite va Laravel trong 3 terminal rieng' -ForegroundColor Cyan
    $phpCmd = "`"$Php84`""

    Start-Process powershell -ArgumentList @(
        '-NoExit','-Command',
        "$ProxyCleanup Set-Location '$RepoRoot'; & $phpCmd artisan boost:mcp"
    )

    Start-Process powershell -ArgumentList @(
        '-NoExit','-Command',
        "$ProxyCleanup Set-Location '$RepoRoot'; npm run dev"
    )

    Start-Process powershell -ArgumentList @(
        '-NoExit','-Command',
        "$ProxyCleanup Set-Location '$RepoRoot'; & $phpCmd artisan serve --host=127.0.0.1 --port=$LaravelPort"
    )

    Write-Host "==> Da san sang. Mo $LaravelUrl/login" -ForegroundColor Green
    Write-Host '==> Neu cap nhat database demo, chay: .\run-demo.ps1 -Seed' -ForegroundColor Green
}
finally {
    Pop-Location
}
