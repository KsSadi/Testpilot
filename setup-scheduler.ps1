# Laravel Scheduler Setup Script for Windows
# This script creates a Windows Task Scheduler task to run Laravel's scheduler every minute

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  Laravel Scheduler Setup - LaraKit" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Check if running as Administrator
$isAdmin = ([Security.Principal.WindowsPrincipal] [Security.Principal.WindowsIdentity]::GetCurrent()).IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)

if (-not $isAdmin) {
    Write-Host "❌ This script must be run as Administrator!" -ForegroundColor Red
    Write-Host "   Please right-click PowerShell and select 'Run as Administrator'" -ForegroundColor Yellow
    Write-Host ""
    Read-Host "Press Enter to exit"
    exit 1
}

# Project path
$projectPath = $PSScriptRoot
Write-Host "📁 Project Path: $projectPath" -ForegroundColor Green

# Find PHP executable
$phpPaths = @(
    "C:\xampp\php\php.exe",
    "C:\php\php.exe",
    "C:\wamp64\bin\php\php8.2\php.exe",
    "C:\laragon\bin\php\php-8.2\php.exe"
)

$phpPath = $null
foreach ($path in $phpPaths) {
    if (Test-Path $path) {
        $phpPath = $path
        break
    }
}

if (-not $phpPath) {
    Write-Host "❌ PHP executable not found!" -ForegroundColor Red
    Write-Host "   Please enter the full path to php.exe:" -ForegroundColor Yellow
    $phpPath = Read-Host "PHP Path"
    
    if (-not (Test-Path $phpPath)) {
        Write-Host "❌ Invalid PHP path!" -ForegroundColor Red
        Read-Host "Press Enter to exit"
        exit 1
    }
}

Write-Host "✅ PHP found: $phpPath" -ForegroundColor Green
Write-Host ""

# Task details
$taskName = "Laravel Scheduler - LaraKit"
$taskDescription = "Runs Laravel task scheduler every minute for automatic backups and scheduled tasks"

Write-Host "🔧 Creating Task Scheduler entry..." -ForegroundColor Cyan

# Remove existing task if it exists
$existingTask = Get-ScheduledTask -TaskName $taskName -ErrorAction SilentlyContinue
if ($existingTask) {
    Write-Host "   Removing existing task..." -ForegroundColor Yellow
    Unregister-ScheduledTask -TaskName $taskName -Confirm:$false
}

# Create the scheduled task
$action = New-ScheduledTaskAction -Execute $phpPath `
    -Argument "artisan schedule:run" `
    -WorkingDirectory $projectPath

$trigger = New-ScheduledTaskTrigger -Once -At (Get-Date) -RepetitionInterval (New-TimeSpan -Minutes 1) -RepetitionDuration ([TimeSpan]::MaxValue)

$settings = New-ScheduledTaskSettingsSet `
    -AllowStartIfOnBatteries `
    -DontStopIfGoingOnBatteries `
    -StartWhenAvailable `
    -RunOnlyIfNetworkAvailable:$false `
    -MultipleInstances IgnoreNew

$principal = New-ScheduledTaskPrincipal -UserId "$env:USERDOMAIN\$env:USERNAME" -LogonType S4U -RunLevel Highest

# Register the task
Register-ScheduledTask -TaskName $taskName `
    -Action $action `
    -Trigger $trigger `
    -Settings $settings `
    -Principal $principal `
    -Description $taskDescription | Out-Null

Write-Host ""
Write-Host "========================================" -ForegroundColor Green
Write-Host "  ✅ Setup Complete!" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Green
Write-Host ""
Write-Host "📋 Task Details:" -ForegroundColor Cyan
Write-Host "   Name: $taskName" -ForegroundColor White
Write-Host "   Schedule: Every 1 minute" -ForegroundColor White
Write-Host "   PHP: $phpPath" -ForegroundColor White
Write-Host "   Project: $projectPath" -ForegroundColor White
Write-Host ""
Write-Host "🎯 Next Steps:" -ForegroundColor Cyan
Write-Host "   1. Enable 'Automatic Backups' in Settings → Backup Configuration" -ForegroundColor White
Write-Host "   2. Choose backup frequency (Daily/Weekly/Monthly)" -ForegroundColor White
Write-Host "   3. Backups will run automatically at 2:00 AM" -ForegroundColor White
Write-Host ""
Write-Host "📊 Verify Setup:" -ForegroundColor Cyan
Write-Host "   php artisan schedule:list" -ForegroundColor Yellow
Write-Host ""
Write-Host "🧪 Test Manually:" -ForegroundColor Cyan
Write-Host "   php artisan backup:run-now" -ForegroundColor Yellow
Write-Host ""

# Offer to run test
$runTest = Read-Host "Would you like to test the scheduler now? (y/n)"
if ($runTest -eq 'y' -or $runTest -eq 'Y') {
    Write-Host ""
    Write-Host "🧪 Running test..." -ForegroundColor Cyan
    & $phpPath artisan schedule:list
    Write-Host ""
}

Write-Host "✨ Done! The task is now running in the background." -ForegroundColor Green
Write-Host ""
Read-Host "Press Enter to exit"
