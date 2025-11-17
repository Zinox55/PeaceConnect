@echo off
echo ========================================
echo Installation de PHP pour PeaceConnect
echo ========================================
echo.

REM Créer le dossier pour PHP
if not exist "C:\php" mkdir "C:\php"

echo 1. Telechargez PHP depuis: https://windows.php.net/download/
echo    - Version recommandee: PHP 8.2 VS16 x64 Thread Safe
echo    - Fichier: php-8.2.x-Win32-vs16-x64.zip
echo.
echo 2. Extraire le contenu dans: C:\php\
echo.
echo 3. Renommer php.ini-development en php.ini
echo.
echo 4. Executer ce script a nouveau pour configurer PHP
echo.

REM Vérifier si PHP existe
if exist "C:\php\php.exe" (
    echo [OK] PHP detecte dans C:\php\
    echo.
    
    REM Ajouter au PATH
    echo Configuration du PATH Windows...
    setx PATH "%PATH%;C:\php" /M
    
    REM Copier php.ini si nécessaire
    if not exist "C:\php\php.ini" (
        if exist "C:\php\php.ini-development" (
            copy "C:\php\php.ini-development" "C:\php\php.ini"
            echo [OK] php.ini cree
        )
    )
    
    REM Activer les extensions nécessaires
    echo.
    echo Configuration des extensions PHP...
    powershell -Command "(Get-Content C:\php\php.ini) -replace ';extension=mysqli', 'extension=mysqli' | Set-Content C:\php\php.ini"
    powershell -Command "(Get-Content C:\php\php.ini) -replace ';extension=pdo_mysql', 'extension=pdo_mysql' | Set-Content C:\php\php.ini"
    powershell -Command "(Get-Content C:\php\php.ini) -replace ';extension=mbstring', 'extension=mbstring' | Set-Content C:\php\php.ini"
    
    echo [OK] Extensions activees (mysqli, pdo_mysql, mbstring)
    echo.
    echo ========================================
    echo Installation terminee !
    echo ========================================
    echo.
    echo Fermez et reouvrez votre terminal, puis tapez:
    echo   cd "%~dp0"
    echo   php -S localhost:8000
    echo.
    echo Puis ouvrez: http://localhost:8000/view/front/produits.html
    echo.
) else (
    echo [ERREUR] PHP non trouve dans C:\php\
    echo.
    echo Veuillez suivre les etapes 1-3 ci-dessus.
    echo.
)

pause
