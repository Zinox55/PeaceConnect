# Configuration Manuelle SMTP - PeaceConnect
# Si vous ne pouvez pas exécuter le script PowerShell

Write-Host "============================================" -ForegroundColor Cyan
Write-Host " Configuration Manuelle Email" -ForegroundColor Cyan
Write-Host "============================================" -ForegroundColor Cyan
Write-Host ""

Write-Host "OPTION 1: Configuration Mailtrap (Recommandé)" -ForegroundColor Green
Write-Host "---------------------------------------------" -ForegroundColor Gray
Write-Host ""
Write-Host "1. Créez un compte sur https://mailtrap.io" -ForegroundColor Yellow
Write-Host "2. Allez dans Email Testing > Inboxes" -ForegroundColor Yellow
Write-Host "3. Cliquez sur SMTP Settings" -ForegroundColor Yellow
Write-Host "4. Notez vos credentials:" -ForegroundColor Yellow
Write-Host ""

$usernameMailtrap = Read-Host "   Entrez votre Username Mailtrap (ou appuyez sur Entrée pour passer)"

if ($usernameMailtrap) {
    $passwordMailtrap = Read-Host "   Entrez votre Password Mailtrap"
    
    if ($passwordMailtrap) {
        Write-Host ""
        Write-Host "Configuration de sendmail.ini..." -ForegroundColor Yellow
        
        # Créer le contenu pour sendmail.ini
        $sendmailContent = @"
[sendmail]

smtp_server=smtp.mailtrap.io
smtp_port=2525
error_logfile=error.log
debug_logfile=debug.log
auth_username=$usernameMailtrap
auth_password=$passwordMailtrap
force_sender=noreply@peaceconnect.org
"@
        
        try {
            # Backup
            if (Test-Path "C:\xampp\sendmail\sendmail.ini") {
                Copy-Item "C:\xampp\sendmail\sendmail.ini" "C:\xampp\sendmail\sendmail.ini.backup_$(Get-Date -Format 'yyyyMMdd_HHmmss')" -ErrorAction SilentlyContinue
                Write-Host "[OK] Backup créé" -ForegroundColor Green
            }
            
            # Écrire la configuration
            $sendmailContent | Out-File "C:\xampp\sendmail\sendmail.ini" -Encoding ASCII -Force
            Write-Host "[OK] sendmail.ini configuré pour Mailtrap" -ForegroundColor Green
            
            Write-Host ""
            Write-Host "Configuration de php.ini..." -ForegroundColor Yellow
            
            # Configuration php.ini
            $phpIniPath = "C:\xampp\php\php.ini"
            
            if (Test-Path $phpIniPath) {
                # Backup
                Copy-Item $phpIniPath "$phpIniPath.backup_$(Get-Date -Format 'yyyyMMdd_HHmmss')" -ErrorAction SilentlyContinue
                
                # Lire le contenu
                $phpIniContent = Get-Content $phpIniPath -Raw
                
                # Remplacer les lignes SMTP
                $phpIniContent = $phpIniContent -replace '(?m)^SMTP\s*=.*', 'SMTP=smtp.mailtrap.io'
                $phpIniContent = $phpIniContent -replace '(?m)^smtp_port\s*=.*', 'smtp_port=2525'
                $phpIniContent = $phpIniContent -replace '(?m)^;?sendmail_path\s*=.*', 'sendmail_path="C:\xampp\sendmail\sendmail.exe -t"'
                
                # Sauvegarder
                $phpIniContent | Out-File $phpIniPath -Encoding ASCII -Force
                
                Write-Host "[OK] php.ini configuré" -ForegroundColor Green
            } else {
                Write-Host "[ATTENTION] php.ini non trouvé dans C:\xampp\php\" -ForegroundColor Yellow
            }
            
            Write-Host ""
            Write-Host "============================================" -ForegroundColor Green
            Write-Host " CONFIGURATION TERMINÉE !" -ForegroundColor Green
            Write-Host "============================================" -ForegroundColor Green
            Write-Host ""
            Write-Host "PROCHAINES ÉTAPES:" -ForegroundColor Cyan
            Write-Host "1. Ouvrez XAMPP Control Panel" -ForegroundColor White
            Write-Host "2. Cliquez sur 'Stop' pour Apache" -ForegroundColor White
            Write-Host "3. Attendez 2 secondes" -ForegroundColor White
            Write-Host "4. Cliquez sur 'Start' pour Apache" -ForegroundColor White
            Write-Host ""
            Write-Host "5. Testez: http://localhost/PeaceConnectr/PeaceConnect/view/BackOffice/test-email.php" -ForegroundColor Yellow
            Write-Host ""
            Write-Host "Les emails seront visibles sur: https://mailtrap.io" -ForegroundColor Cyan
            Write-Host ""
            
        } catch {
            Write-Host "[ERREUR] $($_.Exception.Message)" -ForegroundColor Red
            Write-Host ""
            Write-Host "Configuration manuelle requise. Consultez CONFIGURATION_COMPLETE.txt" -ForegroundColor Yellow
        }
    }
} else {
    Write-Host ""
    Write-Host "============================================" -ForegroundColor Yellow
    Write-Host " Configuration Manuelle Requise" -ForegroundColor Yellow
    Write-Host "============================================" -ForegroundColor Yellow
    Write-Host ""
    Write-Host "ÉTAPE 1: Créez un compte Mailtrap" -ForegroundColor Cyan
    Write-Host "  URL: https://mailtrap.io" -ForegroundColor White
    Write-Host "  - Inscription gratuite" -ForegroundColor Gray
    Write-Host "  - Aucune carte bancaire requise" -ForegroundColor Gray
    Write-Host ""
    Write-Host "ÉTAPE 2: Obtenez vos credentials" -ForegroundColor Cyan
    Write-Host "  1. Connectez-vous à Mailtrap" -ForegroundColor White
    Write-Host "  2. Email Testing > Inboxes" -ForegroundColor White
    Write-Host "  3. Cliquez sur votre inbox" -ForegroundColor White
    Write-Host "  4. SMTP Settings" -ForegroundColor White
    Write-Host "  5. Copiez Username et Password" -ForegroundColor White
    Write-Host ""
    Write-Host "ÉTAPE 3: Éditez les fichiers de configuration" -ForegroundColor Cyan
    Write-Host ""
    Write-Host "Fichier 1: C:\xampp\sendmail\sendmail.ini" -ForegroundColor Yellow
    Write-Host "---------------------------------------------" -ForegroundColor Gray
    Write-Host "[sendmail]"
    Write-Host "smtp_server=smtp.mailtrap.io"
    Write-Host "smtp_port=2525"
    Write-Host "auth_username=VOTRE_USERNAME_MAILTRAP"
    Write-Host "auth_password=VOTRE_PASSWORD_MAILTRAP"
    Write-Host "force_sender=noreply@peaceconnect.org"
    Write-Host ""
    Write-Host "Fichier 2: C:\xampp\php\php.ini" -ForegroundColor Yellow
    Write-Host "---------------------------------------------" -ForegroundColor Gray
    Write-Host "Cherchez [mail function] et modifiez:"
    Write-Host "SMTP=smtp.mailtrap.io"
    Write-Host "smtp_port=2525"
    Write-Host 'sendmail_path="C:\xampp\sendmail\sendmail.exe -t"'
    Write-Host ""
    
    $openFiles = Read-Host "Voulez-vous ouvrir ces fichiers maintenant pour les éditer? (O/N)"
    
    if ($openFiles -eq "O" -or $openFiles -eq "o") {
        Write-Host ""
        Write-Host "Ouverture des fichiers..." -ForegroundColor Yellow
        
        if (Test-Path "C:\xampp\sendmail\sendmail.ini") {
            Start-Process notepad "C:\xampp\sendmail\sendmail.ini"
            Write-Host "[OK] sendmail.ini ouvert dans Notepad" -ForegroundColor Green
        }
        
        Start-Sleep -Seconds 1
        
        if (Test-Path "C:\xampp\php\php.ini") {
            Start-Process notepad "C:\xampp\php\php.ini"
            Write-Host "[OK] php.ini ouvert dans Notepad" -ForegroundColor Green
        }
        
        Write-Host ""
        Write-Host "Modifiez les fichiers selon les instructions ci-dessus," -ForegroundColor Cyan
        Write-Host "puis redémarrez Apache dans XAMPP Control Panel." -ForegroundColor Cyan
    }
    
    Write-Host ""
    Write-Host "ALTERNATIVE: Gmail SMTP" -ForegroundColor Yellow
    Write-Host "---------------------------------------------" -ForegroundColor Gray
    Write-Host ""
    Write-Host "Si vous préférez utiliser Gmail:" -ForegroundColor White
    Write-Host "1. Créez un mot de passe d'application Gmail" -ForegroundColor Gray
    Write-Host "   https://myaccount.google.com/security" -ForegroundColor Gray
    Write-Host "2. Utilisez ces valeurs dans sendmail.ini:" -ForegroundColor Gray
    Write-Host "   smtp_server=smtp.gmail.com" -ForegroundColor Gray
    Write-Host "   smtp_port=587" -ForegroundColor Gray
    Write-Host "   auth_username=votre-email@gmail.com" -ForegroundColor Gray
    Write-Host "   auth_password=mot_de_passe_application" -ForegroundColor Gray
    Write-Host ""
}

Write-Host ""
Write-Host "Documentation complète disponible dans:" -ForegroundColor Cyan
Write-Host "  - SOLUTION_RAPIDE_EMAIL.md" -ForegroundColor White
Write-Host "  - CONFIGURATION_COMPLETE.txt" -ForegroundColor White
Write-Host "  - QUICK_START_EMAIL.md" -ForegroundColor White
Write-Host ""
Write-Host "Guide visuel: http://localhost/PeaceConnectr/PeaceConnect/view/BackOffice/setup-email-guide.html" -ForegroundColor Cyan
Write-Host ""

Write-Host "Appuyez sur une touche pour quitter..."
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")
