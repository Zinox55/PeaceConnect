# Configuration automatique Email pour PeaceConnect
# Ce script configure XAMPP pour utiliser Mailtrap (email de test)

Write-Host "============================================" -ForegroundColor Cyan
Write-Host " Configuration Email - PeaceConnect" -ForegroundColor Cyan
Write-Host "============================================" -ForegroundColor Cyan
Write-Host ""

# Vérifier les droits administrateur
$currentPrincipal = New-Object Security.Principal.WindowsPrincipal([Security.Principal.WindowsIdentity]::GetCurrent())
$isAdmin = $currentPrincipal.IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)

if (-not $isAdmin) {
    Write-Host "[ATTENTION] Ce script doit etre execute en tant qu'administrateur" -ForegroundColor Yellow
    Write-Host "Clic droit sur PowerShell > Executer en tant qu'administrateur" -ForegroundColor Yellow
    Write-Host ""
}

Write-Host "Choisissez votre methode de configuration:" -ForegroundColor Green
Write-Host ""
Write-Host "1. Mailtrap (Recommande pour tests - FACILE)" -ForegroundColor Yellow
Write-Host "   - Tous les emails sont captures (pas envoyes reellement)" -ForegroundColor Gray
Write-Host "   - Gratuit, aucune configuration Gmail necessaire" -ForegroundColor Gray
Write-Host "   - Voir les emails sur mailtrap.io" -ForegroundColor Gray
Write-Host ""
Write-Host "2. Gmail SMTP (Production - emails reels)" -ForegroundColor Yellow
Write-Host "   - Envoie reellement les emails" -ForegroundColor Gray
Write-Host "   - Necessite un mot de passe d'application Gmail" -ForegroundColor Gray
Write-Host ""
Write-Host "3. Configuration manuelle" -ForegroundColor Yellow
Write-Host ""

$choice = Read-Host "Votre choix (1, 2 ou 3)"

switch ($choice) {
    "1" {
        Write-Host ""
        Write-Host "=== Configuration Mailtrap ===" -ForegroundColor Cyan
        Write-Host ""
        Write-Host "Etapes a suivre:" -ForegroundColor Green
        Write-Host "1. Allez sur https://mailtrap.io et creez un compte gratuit"
        Write-Host "2. Creez un inbox (boite de reception)"
        Write-Host "3. Cliquez sur votre inbox et allez dans 'SMTP Settings'"
        Write-Host "4. Copiez les informations suivantes:"
        Write-Host ""
        
        $username = Read-Host "   Username Mailtrap"
        $password = Read-Host "   Password Mailtrap"
        
        if ($username -and $password) {
            # Configuration sendmail.ini
            $sendmailConfig = @"
[sendmail]

smtp_server=smtp.mailtrap.io
smtp_port=2525
error_logfile=error.log
debug_logfile=debug.log
auth_username=$username
auth_password=$password
force_sender=noreply@peaceconnect.org
"@
            
            try {
                # Backup
                if (Test-Path "C:\xampp\sendmail\sendmail.ini") {
                    Copy-Item "C:\xampp\sendmail\sendmail.ini" "C:\xampp\sendmail\sendmail.ini.backup" -Force
                    Write-Host ""
                    Write-Host "[OK] Sauvegarde de sendmail.ini creee" -ForegroundColor Green
                }
                
                # Écrire la nouvelle configuration
                $sendmailConfig | Out-File "C:\xampp\sendmail\sendmail.ini" -Encoding ASCII -Force
                Write-Host "[OK] sendmail.ini configure pour Mailtrap" -ForegroundColor Green
                
                # Configuration php.ini
                Write-Host "[INFO] Configuration de php.ini..." -ForegroundColor Yellow
                
                $phpIniPath = "C:\xampp\php\php.ini"
                if (Test-Path $phpIniPath) {
                    Copy-Item $phpIniPath "$phpIniPath.backup" -Force
                    
                    $phpIni = Get-Content $phpIniPath
                    
                    # Modifier les lignes SMTP
                    $phpIni = $phpIni -replace '^SMTP\s*=.*', 'SMTP=smtp.mailtrap.io'
                    $phpIni = $phpIni -replace '^smtp_port\s*=.*', 'smtp_port=2525'
                    $phpIni = $phpIni -replace '^;?sendmail_path\s*=.*', 'sendmail_path="C:\xampp\sendmail\sendmail.exe -t"'
                    
                    $phpIni | Out-File $phpIniPath -Encoding ASCII -Force
                    
                    Write-Host "[OK] php.ini configure" -ForegroundColor Green
                }
                
                Write-Host ""
                Write-Host "============================================" -ForegroundColor Green
                Write-Host " CONFIGURATION TERMINEE !" -ForegroundColor Green
                Write-Host "============================================" -ForegroundColor Green
                Write-Host ""
                Write-Host "Prochaines etapes:" -ForegroundColor Yellow
                Write-Host "1. Redemarrez Apache dans XAMPP Control Panel"
                Write-Host "2. Testez avec: http://localhost/PeaceConnectr/PeaceConnect/view/BackOffice/test-email.php"
                Write-Host "3. Verifiez les emails sur https://mailtrap.io"
                Write-Host ""
                
            } catch {
                Write-Host "[ERREUR] $($_.Exception.Message)" -ForegroundColor Red
            }
        } else {
            Write-Host "[ERREUR] Username ou password manquant!" -ForegroundColor Red
        }
    }
    
    "2" {
        Write-Host ""
        Write-Host "=== Configuration Gmail ===" -ForegroundColor Cyan
        Write-Host ""
        Write-Host "IMPORTANT: Vous devez creer un mot de passe d'application Gmail" -ForegroundColor Yellow
        Write-Host ""
        Write-Host "Etapes:" -ForegroundColor Green
        Write-Host "1. Allez sur https://myaccount.google.com/security"
        Write-Host "2. Activez la 'Validation en 2 etapes'"
        Write-Host "3. Allez dans 'Mots de passe d'application'"
        Write-Host "4. Creez un mot de passe pour 'Mail' ou 'Autre'"
        Write-Host "5. Copiez le mot de passe genere (16 caracteres)"
        Write-Host ""
        
        $gmailUser = Read-Host "   Votre email Gmail"
        $gmailAppPassword = Read-Host "   Mot de passe d'application (16 caracteres)"
        
        if ($gmailUser -and $gmailAppPassword) {
            # Enlever les espaces du mot de passe
            $gmailAppPassword = $gmailAppPassword -replace '\s', ''
            
            # Configuration sendmail.ini
            $sendmailConfig = @"
[sendmail]

smtp_server=smtp.gmail.com
smtp_port=587
error_logfile=error.log
debug_logfile=debug.log
auth_username=$gmailUser
auth_password=$gmailAppPassword
force_sender=$gmailUser
"@
            
            try {
                # Backup
                if (Test-Path "C:\xampp\sendmail\sendmail.ini") {
                    Copy-Item "C:\xampp\sendmail\sendmail.ini" "C:\xampp\sendmail\sendmail.ini.backup" -Force
                    Write-Host ""
                    Write-Host "[OK] Sauvegarde de sendmail.ini creee" -ForegroundColor Green
                }
                
                # Écrire la nouvelle configuration
                $sendmailConfig | Out-File "C:\xampp\sendmail\sendmail.ini" -Encoding ASCII -Force
                Write-Host "[OK] sendmail.ini configure pour Gmail" -ForegroundColor Green
                
                # Configuration php.ini
                Write-Host "[INFO] Configuration de php.ini..." -ForegroundColor Yellow
                
                $phpIniPath = "C:\xampp\php\php.ini"
                if (Test-Path $phpIniPath) {
                    Copy-Item $phpIniPath "$phpIniPath.backup" -Force
                    
                    $phpIni = Get-Content $phpIniPath
                    
                    # Modifier les lignes SMTP
                    $phpIni = $phpIni -replace '^SMTP\s*=.*', 'SMTP=smtp.gmail.com'
                    $phpIni = $phpIni -replace '^smtp_port\s*=.*', 'smtp_port=587'
                    $phpIni = $phpIni -replace '^;?sendmail_path\s*=.*', 'sendmail_path="C:\xampp\sendmail\sendmail.exe -t"'
                    
                    $phpIni | Out-File $phpIniPath -Encoding ASCII -Force
                    
                    Write-Host "[OK] php.ini configure" -ForegroundColor Green
                }
                
                Write-Host ""
                Write-Host "============================================" -ForegroundColor Green
                Write-Host " CONFIGURATION TERMINEE !" -ForegroundColor Green
                Write-Host "============================================" -ForegroundColor Green
                Write-Host ""
                Write-Host "Prochaines etapes:" -ForegroundColor Yellow
                Write-Host "1. Redemarrez Apache dans XAMPP Control Panel"
                Write-Host "2. Testez avec: http://localhost/PeaceConnectr/PeaceConnect/view/BackOffice/test-email.php"
                Write-Host "3. Verifiez l'email dans ghribiranim6@gmail.com"
                Write-Host ""
                
            } catch {
                Write-Host "[ERREUR] $($_.Exception.Message)" -ForegroundColor Red
            }
        } else {
            Write-Host "[ERREUR] Email ou mot de passe manquant!" -ForegroundColor Red
        }
    }
    
    "3" {
        Write-Host ""
        Write-Host "Pour configuration manuelle:" -ForegroundColor Yellow
        Write-Host ""
        Write-Host "Editez ces fichiers:" -ForegroundColor Green
        Write-Host "1. C:\xampp\sendmail\sendmail.ini"
        Write-Host "2. C:\xampp\php\php.ini"
        Write-Host ""
        Write-Host "Consultez CONFIGURATION_COMPLETE.txt pour les details"
        Write-Host ""
        
        $openFiles = Read-Host "Ouvrir les fichiers maintenant? (O/N)"
        if ($openFiles -eq "O" -or $openFiles -eq "o") {
            Start-Process notepad "C:\xampp\sendmail\sendmail.ini"
            Start-Sleep -Seconds 1
            Start-Process notepad "C:\xampp\php\php.ini"
        }
    }
    
    default {
        Write-Host "Choix invalide!" -ForegroundColor Red
    }
}

Write-Host ""
Write-Host "Appuyez sur une touche pour quitter..."
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")
