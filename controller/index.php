<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PeaceConnect - Plateforme E-Commerce</title>
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="view/BackOffice/assets/vendor/bootstrap/css/bootstrap.min.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="view/BackOffice/assets/vendor/fontawesome-free/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Nunito', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow-x: hidden;
        }
        
        /* Animation de fond */
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="%23ffffff" fill-opacity="0.1" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,122.7C672,117,768,139,864,138.7C960,139,1056,117,1152,96C1248,75,1344,53,1392,42.7L1440,32L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>');
            background-size: cover;
            background-position: bottom;
            animation: wave 20s ease-in-out infinite alternate;
        }
        
        @keyframes wave {
            0% { transform: translateY(0); }
            100% { transform: translateY(-20px); }
        }
        
        .container {
            position: relative;
            z-index: 1;
            max-width: 1200px;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 60px;
            animation: fadeInDown 1s ease;
        }
        
        .logo {
            width: 80px;
            height: 80px;
            background: white;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            animation: pulse 2s ease-in-out infinite;
        }
        
        .logo i {
            font-size: 40px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        
        h1 {
            color: white;
            font-size: 48px;
            font-weight: 800;
            margin-bottom: 10px;
            text-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }
        
        .subtitle {
            color: rgba(255,255,255,0.9);
            font-size: 20px;
            font-weight: 300;
        }
        
        .cards-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }
        
        .access-card {
            background: white;
            border-radius: 20px;
            padding: 40px 30px;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            cursor: pointer;
            position: relative;
            overflow: hidden;
            animation: fadeInUp 1s ease;
        }
        
        .access-card:nth-child(2) {
            animation-delay: 0.2s;
        }
        
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .access-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, #667eea, #764ba2);
            transform: scaleX(0);
            transition: transform 0.4s ease;
        }
        
        .access-card:hover::before {
            transform: scaleX(1);
        }
        
        .access-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 30px 80px rgba(0,0,0,0.2);
        }
        
        .card-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 25px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            transition: all 0.4s ease;
        }
        
        .admin-card .card-icon {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .client-card .card-icon {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
        }
        
        .access-card:hover .card-icon {
            transform: scale(1.1) rotate(5deg);
        }
        
        .card-title {
            font-size: 28px;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 15px;
        }
        
        .card-description {
            color: #718096;
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 25px;
        }
        
        .card-features {
            list-style: none;
            padding: 0;
            margin: 25px 0;
            text-align: left;
        }
        
        .card-features li {
            padding: 8px 0;
            color: #4a5568;
            font-size: 14px;
            display: flex;
            align-items: center;
        }
        
        .card-features li i {
            margin-right: 10px;
            color: #667eea;
            font-size: 12px;
        }
        
        .access-btn {
            display: inline-block;
            padding: 15px 40px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            border: 2px solid;
            position: relative;
            overflow: hidden;
        }
        
        .admin-card .access-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-color: transparent;
        }
        
        .admin-card .access-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }
        
        .client-card .access-btn {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            border-color: transparent;
        }
        
        .client-card .access-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 10px 25px rgba(245, 87, 108, 0.4);
        }
        
        .footer {
            text-align: center;
            margin-top: 60px;
            color: rgba(255,255,255,0.8);
            font-size: 14px;
            animation: fadeInUp 1s ease 0.4s both;
        }
        
        .footer a {
            color: white;
            text-decoration: none;
            font-weight: 600;
            margin: 0 10px;
            transition: all 0.3s ease;
        }
        
        .footer a:hover {
            color: #fff;
            text-shadow: 0 0 10px rgba(255,255,255,0.5);
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            h1 {
                font-size: 36px;
            }
            
            .subtitle {
                font-size: 16px;
            }
            
            .cards-container {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .access-card {
                padding: 30px 20px;
            }
        }
        
        /* Badge de version */
        .version-badge {
            position: fixed;
            top: 20px;
            right: 20px;
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(10px);
            padding: 8px 16px;
            border-radius: 20px;
            color: white;
            font-size: 12px;
            font-weight: 600;
            z-index: 10;
        }
    </style>
</head>
<body>
    <div class="version-badge">
        v1.0
    </div>

    <div class="container">
        <div class="header">
            <div class="logo">
                <i class="fas fa-handshake"></i>
            </div>
            <h1>PeaceConnect</h1>
            <p class="subtitle">Plateforme de Commerce Électronique</p>
        </div>

        <div class="cards-container">
            <!-- Carte Admin -->
            <div class="access-card admin-card" onclick="window.location.href='view/BackOffice/dashboard.html'">
                <div class="card-icon">
                    <i class="fas fa-user-shield"></i>
                </div>
                <h2 class="card-title">Tableau de Bord</h2>
                <p class="card-description">
                    Accédez à l'interface d'administration pour gérer votre boutique
                </p>
                <ul class="card-features">
                    <li>
                        <i class="fas fa-circle"></i>
                        Gestion des produits et stock
                    </li>
                    <li>
                        <i class="fas fa-circle"></i>
                        Suivi des commandes
                    </li>
                    <li>
                        <i class="fas fa-circle"></i>
                        Notifications de stock
                    </li>
                    <li>
                        <i class="fas fa-circle"></i>
                        Statistiques et rapports
                    </li>
                </ul>
                <a href="view/BackOffice/dashboard.html" class="access-btn">
                    <i class="fas fa-sign-in-alt"></i> Accéder au Dashboard
                </a>
            </div>

            <!-- Carte Client -->
            <div class="access-card client-card" onclick="window.location.href='view/FrontOffice/produits.html'">
                <div class="card-icon">
                    <i class="fas fa-shopping-bag"></i>
                </div>
                <h2 class="card-title">Boutique en Ligne</h2>
                <p class="card-description">
                    Découvrez notre catalogue et passez vos commandes en ligne
                </p>
                <ul class="card-features">
                    <li>
                        <i class="fas fa-circle"></i>
                        Catalogue de produits
                    </li>
                    <li>
                        <i class="fas fa-circle"></i>
                        Panier d'achat
                    </li>
                    <li>
                        <i class="fas fa-circle"></i>
                        Commande en ligne
                    </li>
                    <li>
                        <i class="fas fa-circle"></i>
                        Suivi de commande
                    </li>
                </ul>
                <a href="view/FrontOffice/produits.html" class="access-btn">
                    <i class="fas fa-store"></i> Visiter la Boutique
                </a>
            </div>
        </div>

        <div class="footer">
            <p>
                <a href="INSTALLATION.md" target="_blank">
                    <i class="fas fa-book"></i> Documentation
                </a>
                |
                <a href="MAILING_README.md" target="_blank">
                    <i class="fas fa-envelope"></i> Configuration Email
                </a>
                |
                <a href="STRUCTURE.md" target="_blank">
                    <i class="fas fa-sitemap"></i> Structure
                </a>
            </p>
            <p style="margin-top: 15px; opacity: 0.7;">
                © 2025 PeaceConnect - Tous droits réservés
            </p>
        </div>
    </div>

    <!-- Scripts -->
    <script src="view/BackOffice/assets/vendor/jquery/jquery.min.js"></script>
    <script src="view/BackOffice/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Animation au survol des cartes
        document.querySelectorAll('.access-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-10px) scale(1.02)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });

        // Effet de particules (optionnel)
        function createParticle() {
            const particle = document.createElement('div');
            particle.style.position = 'fixed';
            particle.style.width = '4px';
            particle.style.height = '4px';
            particle.style.background = 'rgba(255,255,255,0.5)';
            particle.style.borderRadius = '50%';
            particle.style.pointerEvents = 'none';
            particle.style.left = Math.random() * window.innerWidth + 'px';
            particle.style.top = '-10px';
            particle.style.animation = 'fall ' + (Math.random() * 3 + 2) + 's linear';
            document.body.appendChild(particle);
            
            setTimeout(() => particle.remove(), 5000);
        }
        
        // Créer des particules périodiquement
        setInterval(createParticle, 300);
        
        // Animation de chute
        const style = document.createElement('style');
        style.textContent = `
            @keyframes fall {
                to {
                    transform: translateY(100vh);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>
