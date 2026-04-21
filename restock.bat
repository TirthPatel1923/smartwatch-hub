@echo off
REM Automatic restocking script for Smartwatches e-commerce
REM This script should be scheduled to run every 1-2 hours

echo Starting automatic restocking process...

REM Change to the PHP directory (adjust path as needed)
cd /d "C:\xampp\php"

REM Run the restock script
php "C:\xampp\htdocs\SMARTWATCHES\restock.php"

echo Restocking process completed.
pause