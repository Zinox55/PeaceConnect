@echo off
echo ============================================
echo Installation PHPMailer pour PeaceConnect
echo ============================================
echo.

REM Vérifier si Composer est installé
where composer >nul 2>nul
if %ERRORLEVEL% EQU 0 (
    echo [OK] Composer trouve
    echo Installation de PHPMailer...
    composer require phpmailer/phpmailer
    echo.
    echo Installation terminee!
    pause
    exit /b 0
)

echo [!] Composer n'est pas installe
echo.
echo Option 1: Installer Composer
echo Telecharger depuis: https://getcomposer.org/download/
echo.
echo Option 2: Installation manuelle de PHPMailer
echo 1. Telecharger: https://github.com/PHPMailer/PHPMailer/releases
echo 2. Extraire dans: e:\xampp\htdocs\PeaceConnect\vendor\phpmailer\
echo.
echo Option 3: Installation via PowerShell
echo Executer: php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
echo Puis: php composer-setup.php
echo.

pause
