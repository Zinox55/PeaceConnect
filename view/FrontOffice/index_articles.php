<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PeaceConnect - Accueil Articles</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            padding: 50px;
            max-width: 900px;
            width: 100%;
            text-align: center;
        }
        
        h1 {
            color: #333;
            font-size: 3rem;
            margin-bottom: 15px;
            font-weight: 700;
        }
        
        .subtitle {
            color: #666;
            font-size: 1.2rem;
            margin-bottom: 50px;
        }
        
        .options {
            display: flex;
            gap: 30px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .option-card {
            flex: 1;
            min-width: 300px;
            max-width: 400px;
            background: #f8f9fa;
            border-radius: 15px;
            padding: 40px 30px;
            text-decoration: none;
            color: inherit;
            transition: all 0.3s ease;
            border: 3px solid transparent;
            position: relative;
            overflow: hidden;
        }
        
        .option-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, transparent 0%, rgba(102, 126, 234, 0.1) 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .option-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            border-color: #667eea;
        }
        
        .option-card:hover::before {
            opacity: 1;
        }
        
        .frontend {
            background: linear-gradient(135deg, #667eea20 0%, #764ba220 100%);
        }
        
        .backend {
            background: linear-gradient(135deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%);
            background-size: 200% 200%;
            opacity: 0.1;
        }
        
        .option-card:hover .backend {
            opacity: 0.2;
        }
        
        .icon {
            font-size: 4rem;
            margin-bottom: 20px;
            position: relative;
            z-index: 1;
        }
        
        h2 {
            font-size: 2rem;
            margin-bottom: 15px;
            color: #333;
            position: relative;
            z-index: 1;
        }
        
        p {
            color: #666;
            font-size: 1.1rem;
            line-height: 1.6;
            position: relative;
            z-index: 1;
        }
        
        .btn {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 35px;
            background: #667eea;
            color: white;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s ease;
            position: relative;
            z-index: 1;
        }
        
        .option-card:hover .btn {
            background: #764ba2;
            transform: scale(1.05);
        }
        
        @media (max-width: 768px) {
            h1 {
                font-size: 2rem;
            }
            
            .options {
                flex-direction: column;
            }
            
            .option-card {
                min-width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🕊️ PeaceConnect - Articles</h1>
        <p class="subtitle">Choisissez votre destination</p>
        
        <div class="options">
            <a href="FrontOffice/index.php" class="option-card">
                <div class="frontend"></div>
                <div class="icon">🌐</div>
                <h2>Front-End</h2>
                <p>Interface publique pour consulter les articles et s'abonner à la newsletter</p>
                <span class="btn">Accéder au site</span>
            </a>
            
            <a href="BackOffice/dashboard_ichrak.php" class="option-card">
                <div class="backend"></div>
                <div class="icon">⚙️</div>
                <h2>Back-End</h2>
                <p>Tableau de bord administrateur pour gérer les articles et les abonnés</p>
                <span class="btn">Espace Admin</span>
            </a>
        </div>
    </div>
</body>
</html>