@echo off
echo ============================================
echo  Configuration Email XAMPP - PeaceConnect
echo ============================================
echo.

echo Etape 1: Verification de l'installation XAMPP
if not exist "C:\xampp\sendmail\sendmail.exe" (
    echo [ERREUR] XAMPP sendmail non trouve!
    echo Verifiez que XAMPP est installe dans C:\xampp\
    pause
    exit
)
echo [OK] XAMPP sendmail detecte

echo.
echo Etape 2: Sauvegarde des fichiers de configuration
if exist "C:\xampp\sendmail\sendmail.ini" (
    copy "C:\xampp\sendmail\sendmail.ini" "C:\xampp\sendmail\sendmail.ini.backup" >nul 2>&1
    echo [OK] Sauvegarde de sendmail.ini creee
)

if exist "C:\xampp\php\php.ini" (
    copy "C:\xampp\php\php.ini" "C:\xampp\php\php.ini.backup" >nul 2>&1
    echo [OK] Sauvegarde de php.ini creee
)

echo.
echo ============================================
echo  CONFIGURATION REQUISE
echo ============================================
echo.
echo Pour configurer l'envoi d'emails:
echo.
echo 1. GMAIL - Mot de passe d'application
echo    - Allez sur: https://myaccount.google.com/security
echo    - Activez la validation en 2 etapes
echo    - Creez un mot de passe d'application
echo.
echo 2. EDITEZ: C:\xampp\sendmail\sendmail.ini
echo    Modifiez ces lignes:
echo    smtp_server=smtp.gmail.com
echo    smtp_port=587
echo    auth_username=VOTRE_EMAIL@gmail.com
echo    auth_password=VOTRE_MOT_DE_PASSE_APPLICATION
echo    force_sender=VOTRE_EMAIL@gmail.com
echo.
echo 3. EDITEZ: C:\xampp\php\php.ini
echo    Cherchez [mail function] et modifiez:
echo    SMTP=smtp.gmail.com
echo    smtp_port=587
echo    sendmail_from=noreply@peaceconnect.org
echo    sendmail_path="C:\xampp\sendmail\sendmail.exe -t"
echo.
echo 4. REDEMARREZ Apache dans XAMPP Control Panel
echo.
echo ============================================
echo.

echo Voulez-vous ouvrir les fichiers de configuration maintenant? (O/N)
set /p choice="> "

if /i "%choice%"=="O" (
    start notepad "C:\xampp\sendmail\sendmail.ini"
    timeout /t 2 >nul
    start notepad "C:\xampp\php\php.ini"
    echo.
    echo [OK] Fichiers ouverts dans Notepad
    echo.
    echo N'oubliez pas de:
    echo 1. Modifier les configurations
    echo 2. Sauvegarder les fichiers
    echo 3. Redemarrer Apache
)

echo.
echo ============================================
echo  TESTS
echo ============================================
echo.
echo Apres configuration, testez avec:
echo http://localhost/PeaceConnectr/PeaceConnect/view/BackOffice/test-email.php
echo.

echo Voulez-vous ouvrir le guide de configuration? (O/N)
set /p choice2="> "

if /i "%choice2%"=="O" (
    start "" "%~dp0QUICK_START_EMAIL.md"
)

echo.
echo Configuration terminee!
echo.
pause
