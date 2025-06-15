<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pied de page</title>
    <style>
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background-color: black;
            color: white;
            text-align: center;
            padding: 15px 10px;
            font-family: Arial, sans-serif;
            font-size: 14px;
            z-index: 1000;
        }

        .footer a {
            color: white;
            text-decoration: none;
            margin: 0 10px;
            transition: color 0.3s ease;
        }

        .footer a:hover {
            text-decoration: underline;
            color: #aaa;
        }

        body {
            margin: 0;
            padding-bottom: 60px; /* pour ne pas masquer le contenu */
        }
    </style>
</head>
<body>

    <!-- Contenu de la page ici -->

    <div class="footer">
        <p>&copy; <?php echo date('Y'); ?> Charles CPX. | Tous droits réservés</p>
        <p>
            <a href="privacy.php">Politique de confidentialité</a> |
            <a href="terms.php">Conditions d'utilisation</a>
        </p>
    </div>

</body>
</html>