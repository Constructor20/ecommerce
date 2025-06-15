<?php
session_start();

if (!isset($_SESSION["admin"])) {
    header("Location: admin.php");
    exit();
}

include "bdd.php";

$table = $_GET['table'] ?? null;

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $dbusername, $dbpassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER["REQUEST_METHOD"] === "POST" && $table) {
        $columns = array_keys($_POST);
        $values = array_values($_POST);

        // Vérifie les champs vides
        foreach ($values as $val) {
            if (trim($val) === '') {
                header("Location: create.php?table=$table&error=empty");
                exit();
            }
        }

        // Vérifie si l'ID existe déjà
        if (isset($_POST['id'])) {
            $stmt = $conn->prepare("SELECT COUNT(*) FROM $table WHERE id = :id");
            $stmt->bindParam(":id", $_POST['id']);
            $stmt->execute();
            if ($stmt->fetchColumn() > 0) {
                header("Location: create.php?table=$table&error=duplicate");
                exit();
            }
        }

        // Insérer dans la table
        $columnsList = implode(",", $columns);
        $placeholders = ":" . implode(", :", $columns);
        $stmt = $conn->prepare("INSERT INTO $table ($columnsList) VALUES ($placeholders)");

        foreach ($columns as $col) {
            $stmt->bindValue(":$col", $_POST[$col]);
        }

        $stmt->execute();

        header("Location: admin_dashboard.php?table=$table&success=1");
        exit();
    }

    // Génération du formulaire automatiquement depuis les colonnes de la table
    if ($table) {
        $stmt = $conn->prepare("DESCRIBE $table");
        $stmt->execute();
        $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);

        ?>
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <title>Ajouter un enregistrement</title>
                <style>
        body {
            font-family: 'Segoe UI', Tahoma, sans-serif;
            margin: 40px;
            background-color: #f5f7fa;
        }
        h1 {
            color: #007acc;
        }
        form {
            display: flex;
            flex-direction: column;
            max-width: 500px;
        }
        label {
            margin-top: 15px;
            font-weight: bold;
        }
        input[type="text"], input[type="number"], textarea {
            padding: 10px;
            font-size: 1rem;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        button {
            margin-top: 25px;
            padding: 10px;
            background-color: #007acc;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            cursor: pointer;
        }
        button:hover {
            background-color: #005fa3;
        }

        .error-banner {
            background-color: #f8d7da;
            color: #721c24;
            padding: 15px 25px;
            border-radius: 6px;
            border-left: 6px solid #dc3545;
            font-size: 1rem;
            font-weight: bold;
            margin: 20px auto 30px;
            max-width: 900px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            text-align: center;
        }

    </style>
        </head>
        <body>
        <h1>Ajouter un enregistrement dans "<?php echo htmlspecialchars($table); ?>"</h1>

        <?php if (isset($_GET['error'])) {
            if ($_GET['error'] === 'empty') {
                echo "<p style='color:red'>Tous les champs sont obligatoires.</p>";
            } elseif ($_GET['error'] === 'duplicate') {
                echo "<p style='color:red'>Un enregistrement avec cet ID existe déjà.</p>";
            }
        } ?>

        <form method="POST">
            <?php foreach ($columns as $col): ?>
                <label for="<?php echo $col; ?>"><?php echo htmlspecialchars($col); ?>:</label>
                <input type="text" name="<?php echo $col; ?>" id="<?php echo $col; ?>" required><br><br>
            <?php endforeach; ?>
            <button type="submit">Enregistrer</button>
        </form>

        </body>
        </html>
        <?php
    } else {
        echo "Table non spécifiée.";
    }
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
