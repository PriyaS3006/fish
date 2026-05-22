# Ensure you are running this from the project directory
$projectPath = "c:\Users\senth\OneDrive\Desktop\web-priya\fish-shop"
Set-Location $projectPath

# Check if php is in PATH, otherwise fallback to XAMPP's PHP
$phpCommand = "php"
if (!(Get-Command php -ErrorAction SilentlyContinue)) {
    if (Test-Path "C:\xampp\php\php.exe") {
        $phpCommand = "C:\xampp\php\php.exe"
        Write-Host "PHP not found in PATH, but found in C:\xampp. Using XAMPP's PHP." -ForegroundColor Yellow
    } else {
        Write-Host "PHP is not installed or not in your PATH. Please install XAMPP or PHP." -ForegroundColor Red
        exit
    }
}

# Check if mysql is in PATH, otherwise fallback to XAMPP's MySQL
$mysqlCommand = "mysql"
if (!(Get-Command mysql -ErrorAction SilentlyContinue)) {
    if (Test-Path "C:\xampp\mysql\bin\mysql.exe") {
        $mysqlCommand = "C:\xampp\mysql\bin\mysql.exe"
        Write-Host "MySQL not found in PATH, but found in C:\xampp. Using XAMPP's MySQL." -ForegroundColor Yellow
    }
}

Write-Host "Setting up the database..." -ForegroundColor Green
& cmd.exe /c "`"$mysqlCommand`" -u root -h 127.0.0.1 -P 3307 < `"database.sql`""

if ($LASTEXITCODE -ne 0) {
    Write-Host "Database setup failed. Make sure MySQL is running in your XAMPP Control Panel!" -ForegroundColor Red
} else {
    Write-Host "Database set up successfully." -ForegroundColor Green
}

Write-Host "Starting PHP server on http://localhost:8000..." -ForegroundColor Cyan
Write-Host "Press Ctrl+C to stop the server." -ForegroundColor Yellow
Write-Host "Opening your browser..." -ForegroundColor Cyan
Start-Process "http://localhost:8000"
& $phpCommand -S localhost:8000
