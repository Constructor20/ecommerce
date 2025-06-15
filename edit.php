<?php
session_start();

if (!isset($_SESSION["admin"])) {
    header("Location: admin.php");
    exit();
}

include 'bdd.php';

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $dbusername, $dbpassword);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (!isset($_GET['table']) || !isset($_GET['id'])) {
        die("Table ou ID manquant.");
    }

    $table = $_GET['table'];
    $id = $_GET['id'];

    // Récupération de l'enregistrement
    $stmt = $conn->prepare("SELECT * FROM $table WHERE id = :id");
    $stmt->bindParam(":id", $id);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        die("Aucun enregistrement trouvé avec cet ID.");
    }

    $message = '';
    if ($_SERVER['REQUEST_METHOD'] === "POST") {
        if (isset($_POST['columns']) && is_array($_POST['columns'])) {
            $columns = $_POST['columns'];

            // Vérifier si des champs sont vides
            foreach ($columns as $col => $val) {
                if (trim($val) === '') {
                    $message = "<p style='color:red;'>Tous les champs doivent être remplis.</p>";
                    break;
                }
            }

            if ($message === '') {
                // Préparation de la requête UPDATE
                $setClause = [];
                foreach ($columns as $col => $val) {
                    if ($col !== 'password') { // ne pas modifier password ici
                        $setClause[] = "$col = :$col";
                    }
                }
                $setClause = implode(", ", $setClause);

                $stmtUpdate = $conn->prepare("UPDATE $table SET $setClause WHERE id = :id");

                // Liaison des paramètres
                foreach ($columns as $col => $val) {
                    if ($col !== 'password') {
                        $stmtUpdate->bindValue(":$col", $val);
                    }
                }
                $stmtUpdate->bindValue(":id", $id);

                if ($stmtUpdate->execute()) {
                    $message = "<p style='color:green;'>Modification réussie !</p>";

                    // Recharger les données modifiées
                    $stmt = $conn->prepare("SELECT * FROM $table WHERE id = :id");
                    $stmt->bindParam(":id", $id);
                    $stmt->execute();
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                } else {
                    $message = "<p style='color:red;'>Erreur lors de la mise à jour.</p>";
                }
            }
        } else {
            $message = "<p style='color:red;'>Formulaire incorrect.</p>";
        }
    }
} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Modifier l'enregistrement</title>
<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #f0f2f5;
        padding: 40px 20px;
        color: #333;
    }
    h2 {
        color: #007acc;
        text-align: center;
        margin-bottom: 30px;
    }
    form {
        max-width: 600px;
        background: #fff;
        margin: 0 auto;
        padding: 25px 30px;
        border-radius: 8px;
        box-shadow: 0 1px 5px rgb(0 0 0 / 0.1);
        border-left: 5px solid #007acc;
    }
    label {
        display: block;
        margin-bottom: 6px;
        font-weight: 700;
        color: #005a99;
    }
    input[type="text"] {
        width: 100%;
        padding: 8px 10px;
        margin-bottom: 20px;
        border: 1px solid #ccc;
        border-radius: 4px;
        font-size: 1rem;
        color: #333;
        box-sizing: border-box;
        transition: border-color 0.3s ease;
    }
    input[type="text"]:focus {
        border-color: #007acc;
        outline: none;
    }
    input[type="submit"], .back-button {
        background-color: #007acc;
        color: white;
        border: none;
        padding: 10px 16px;
        font-size: 1rem;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s ease;
        margin-right: 10px;
    }
    input[type="submit"]:hover, .back-button:hover {
        background-color: #005a99;
    }
    .buttons {
        max-width: 600px;
        margin: 20px auto 0; /* marge en haut pour séparer du formulaire */
        text-align: center;
    }
    .back-button {
        display: inline-block;  /* bien en ligne */
        background-color: #007acc;
        color: white;
        border: none;
        padding: 10px 16px;
        font-size: 1rem;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s ease;
        text-decoration: none; /* pour un lien propre */
        margin-right: 0; /* supprime la marge droite si tu veux */
    }
    .back-button:hover {
        background-color: #005a99;
    }
    .message {
        max-width: 600px;
        margin: 15px auto;
        font-size: 1.1rem;
        text-align: center;
    }
</style>

</head>
<body>
    <h2>Modifier l'enregistrement dans la table "<?php echo htmlspecialchars($table); ?>"</h2>

    <?php if ($message): ?>
        <div class="message"><?php echo $message; ?></div>
    <?php endif; ?>

    <form method="POST">
        <?php foreach ($row as $column => $value): ?>
            <?php if ($column !== "password"): ?>
                <label for="<?php echo htmlspecialchars($column); ?>"><?php echo htmlspecialchars($column); ?> :</label>
                <input
                    type="text"
                    name="columns[<?php echo htmlspecialchars($column); ?>]"
                    id="<?php echo htmlspecialchars($column); ?>"
                    value="<?php echo htmlspecialchars($value); ?>"
                    required
                >
            <?php endif; ?>
        <?php endforeach; ?>
        <input type="submit" value="Enregistrer les modifications">
    </form>

    <div class="buttons">
        <a class="back-button" href="admin_dashboard.php?table=<?php echo urlencode($table); ?>">Retour au tableau de bord</a>
    </div>
</body>
</html>
