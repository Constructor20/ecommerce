<?php
session_start();

if (!isset($_SESSION["admin"])) {
    header("Location: admin.php");
}

include 'bdd.php';

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $dbusername, $dbpassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $conn->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_NUM);
} catch (PDOException $e) {
    echo "Erreur: ". $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Tableau de Bord Admin</title>
    <style>
        * {
            box-sizing: border-box;
        }
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f2f5;
            color: #333;
            padding: 40px 20px;
            min-height: 100vh;
        }
        h1 {
            color: #007acc;
            font-size: 2.8rem;
            text-align: center;
            margin-bottom: 10px;
        }
        h2 {
            color: #005a99;
            font-size: 1.8rem;
            text-align: center;
            margin-bottom: 40px;
        }
        h3 {
            color: #004477;
            font-size: 1.3rem;
            margin-top: 50px;
            margin-bottom: 20px;
            text-align: center;
        }

        .records-container {
            display: flex;
            flex-direction: column;
            gap: 25px;
            max-width: 900px;
            margin: 0 auto 30px;
        }
        .record {
            background: #fff;
            padding: 20px 25px;
            border-radius: 8px;
            box-shadow: 0 1px 5px rgb(0 0 0 / 0.1);
            border-left: 5px solid #007acc;
        }
        .record div {
            margin-bottom: 12px;
            display: flex;
            justify-content: space-between;
        }
        .record label {
            font-weight: 700;
            color: #007acc;
            flex: 1 0 40%;
        }
        .record span {
            flex: 1 0 55%;
            color: #444;
            word-break: break-word;
        }
        .actions {
            margin-top: 10px;
        }
        .actions a {
            display: inline-block;
            padding: 6px 12px;
            margin-right: 10px;
            background-color: #007acc;
            color: white;
            text-decoration: none;
            font-size: 0.9rem;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .actions a:hover {
            background-color: #005a99;
        }
        p > a {
            color: #007acc;
            font-weight: 600;
            text-decoration: underline;
        }
        p > a:hover {
            color: #005a99;
            text-decoration: none;
        }
        p {
            font-size: 1.1rem;
            text-align: center;
            color: #666;
            margin-top: 30px;
        }

        .success-banner {
            background-color: #d4edda;
            color: #155724;
            padding: 15px 25px;
            border-radius: 6px;
            border-left: 6px solid #28a745;
            font-size: 1rem;
            font-weight: bold;
            margin: 20px auto 30px;
            max-width: 900px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            text-align: center;
        }

        .buttons {
            max-width: 900px; /* ou la largeur de ton container */
            margin: 20px auto 30px;
            text-align: center;
        }
        .back-button {
            display: inline-block;
            background-color: #007acc;
            color: white;
            border: none;
            padding: 10px 16px;
            font-size: 1rem;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            text-decoration: none;
        }
        .back-button:hover {
            background-color: #005a99;
        }


    </style>
</head>
<body>
    <h1>Bienvenue Maître</h1>
    <h2>Liste des tables dans la base de données "<?php echo htmlspecialchars($dbname); ?>"</h2>

<?php
if ($tables) {
    foreach ($tables as $table) {
        $tableName = $table[0];
        echo "<h3>Table : " . htmlspecialchars($tableName) . "</h3>";

        $stmt = $conn->query("SELECT * FROM $tableName");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($rows) {
            echo '<div class="records-container">';
            foreach ($rows as $row) {
                $idColumn = array_keys($rows[0])[0];
                echo '<div class="record">';
                foreach ($row as $key => $value) {
                    echo '<div><label>' . htmlspecialchars($key) . ' :</label><span>' . htmlspecialchars($value) . '</span></div>';
                }
                echo '<div class="actions">';
                echo "<a href='edit.php?table=" . urlencode($tableName) . "&id=" . urlencode($row[$idColumn]) . "'>Modifier</a>";
                echo "<a href='delete.php?table=" . urlencode($tableName) . "&id=" . urlencode($row[$idColumn]) . "' onclick='return confirm(\"Êtes-vous sûr de vouloir supprimer cet enregistrement ?\")'>Supprimer</a>";
                echo "</div>";
                echo '</div>';
            }
            echo '</div>';
        } else {
            echo "<p>Aucune donnée disponible dans la table " . htmlspecialchars($tableName) . ".</p>";
        }
        echo "<p><a href='create.php?table=" . urlencode($tableName) . "'>Ajouter un nouvel enregistrement</a></p>";
    }
} else {
    echo "<p>Aucune table trouvée dans la base de données.</p>";
}

$conn = null;
?>

</body>
<?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
    <div class="success-banner">L'enregistrement a été ajouté avec succès ✅</div>
<?php endif; ?>

<div class="buttons">
    <a href="index.html" class="back-button">Retour à la page principale</a>
</div>


</html>
