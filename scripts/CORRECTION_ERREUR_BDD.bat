@echo off
echo ========================================
echo Correction de l'erreur base de donnees
echo ========================================
echo.
echo Cette commande va ajouter les colonnes de paiement
echo a votre base de donnees existante.
echo.
pause

cd /d C:\xampp\mysql\bin
mysql.exe -u root -p peaceconnect < "%~dp0sql\add_payment_fields.sql"

if %ERRORLEVEL% EQU 0 (
    echo.
    echo ========================================
    echo SUCCES ! Base de donnees mise a jour
    echo ========================================
    echo.
    echo Vous pouvez maintenant tester le paiement :
    echo http://localhost/peaceconnect/tests/test_paiement.php
) else (
    echo.
    echo ========================================
    echo ERREUR lors de la mise a jour
    echo ========================================
    echo.
    echo Essayez l'option web :
    echo http://localhost/peaceconnect/update_database.php
)

echo.
pause
