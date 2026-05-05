param(
    [switch]$UseMySql,
    [switch]$FreshDatabase
)

$ErrorActionPreference = "Stop"

function Write-Step {
    param([string]$Message)
    Write-Host ""
    Write-Host "==> $Message" -ForegroundColor Cyan
}

function Invoke-Step {
    param(
        [string]$Name,
        [scriptblock]$Command
    )

    Write-Step $Name
    & $Command
}

$root = Split-Path -Parent $PSScriptRoot
Set-Location $root

if ($UseMySql) {
    Write-Step "Checking MySQL availability on 127.0.0.1:3306"
    $mysqlPort = Test-NetConnection -ComputerName 127.0.0.1 -Port 3306 -WarningAction SilentlyContinue
    if (-not $mysqlPort.TcpTestSucceeded) {
        throw "MySQL is not reachable on 127.0.0.1:3306. Start MySQL or run: docker compose -f docker-compose.mysql.yml up -d"
    }
}

Invoke-Step "Validating Composer configuration" { composer validate }
Invoke-Step "Installing Composer dependencies" { composer install --no-interaction }
Invoke-Step "Installing NPM dependencies" { npm install }

if ($UseMySql) {
    Invoke-Step "Verifying Laravel can connect to the configured MySQL database" {
        php artisan tinker --execute="DB::connection()->getPdo(); echo 'Database connection OK';"
    }
}

if ($FreshDatabase) {
    Invoke-Step "Refreshing database and running seeders" { php artisan migrate:fresh --seed --force }
} else {
    Invoke-Step "Running pending migrations" { php artisan migrate --force }
}

Invoke-Step "Listing API routes" { php artisan route:list --path=api }
Invoke-Step "Running PHP tests" { php artisan test }
Invoke-Step "Building React dashboard assets" { npm run build }

Write-Host ""
Write-Host "African Leaders Connection backend verification completed successfully." -ForegroundColor Green
