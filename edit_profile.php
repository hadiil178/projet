<?php
// Start session (if not already started)
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

require 'config.php'; // Database connection

$name = "";
$email = "";
$error = "";
$success = "";

try {
    // Fetch current admin data
    $stmt = $pdo->prepare("SELECT name, email FROM admin WHERE admin_id = :admin_id");
    $stmt->bindParam(':admin_id', $_SESSION['id']);
    $stmt->execute();
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin) {
        $name = $admin['name'];
        $email = $admin['email'];
    } else {
        $error = "Admin data not found.";
    }

    // Handle form submission
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $newName = $_POST['name'];
        $newEmail = $_POST['email'];
        $newPassword = $_POST['new_password'];
        $confirmPassword = $_POST['confirm_password'];

        // Basic validation
        if (empty($newName) || empty($newEmail)) {
            $error = "Veuillez remplir les champs Nom et E-mail.";
        } elseif (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
            $error = "Format d'email invalide.";
        } elseif (!empty($newPassword)) {
            if (strlen($newPassword) < 6) {
                $error = "Le mot de passe doit contenir au moins 6 caractères.";
            } elseif ($newPassword !== $confirmPassword) {
                $error = "Les mots de passe ne correspondent pas.";
            }
        }

        if (empty($error)) {
            // Update admin data
            $updateSql = "UPDATE admin SET name = :nom, email = :email";
            $params = [
                ':nom' => $newName,
                ':email' => $newEmail,
                ':admin_id' => $_SESSION['id']
            ];

            if (!empty($newPassword)) {
                $updateSql .= ", password = :password";
                $params[':password'] = $newPassword; // In a real application, you should hash the password!
            }

            $updateSql .= " WHERE admin_id = :admin_id";
            $updateStmt = $pdo->prepare($updateSql);
            $updateStmt->execute($params);

            if ($updateStmt->rowCount() > 0) {
                $success = "Profil mis à jour avec succès!";
                $_SESSION['nom'] = $newName;
            } else {
                $success = "Profil mis à jour avec succès!"; // Even if no changes were made
            }
        }
    }

} catch(PDOException $e) {
    $error = "Erreur de base de données: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier le Profil</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        body {
            font-family: 'Roboto', sans-serif; /* Consistent font */
            background-color: #FAF6F4; /* Main background color */
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            color: #333; /* Default text color */
        }

        .profile-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            width: 80%;
            max-width: 500px;
        }

        h2 {
            font-family: 'Playfair Display', serif;
            color: #8C5356; /* Logo text color */
            text-align: center;
            margin-bottom: 25px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #555; /* Label color */
            font-weight: 500;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd; /* Input border color */
            border-radius: 8px;
            box-sizing: border-box;
            font-size: 1em;
            font-family: 'Roboto', sans-serif; /* Consistent font */
        }

        button[type="submit"] {
            background-color: #8C5356; /* Button background color */
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            font-size: 1em;
            transition: background-color 0.3s ease; /* Standard transition duration */
            font-family: 'Roboto', sans-serif; /* Consistent font */
            outline: none; /* Remove default focus outline */
        }

        button[type="submit"]:hover {
            background-color: #734043; /* Darker accent for hover */
        }

        .error-message {
            color: red;
            margin-top: 10px;
            text-align: center;
        }

        .success-message {
            color: green;
            margin-top: 10px;
            text-align: center;
        }

        .back-link {
            display: block;
            margin-top: 20px;
            text-align: center;
            color: #777;
            text-decoration: none;
            transition: color 0.3s ease;
            font-family: 'Roboto', sans-serif; /* Consistent font */
        }

        .back-link:hover {
            color: #8C5356; /* Accent hover color */
        }
    </style>
</head>
<body>
    <div class="profile-container">
        <h2>Modifier Votre Profil</h2>
        <?php if ($error): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="success-message"><?php echo $success; ?></div>
        <?php endif; ?>
        <form method="post">
            <div class="form-group">
                <label for="name">Nom:</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>">
            </div>
            <div class="form-group">
                <label for="email">E-mail:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>">
            </div>
            <div class="form-group">
                <label for="new_password">Nouveau Mot de Passe:</label>
                <input type="password" id="new_password" name="new_password">
                <small>Laissez ce champ vide si vous ne souhaitez pas changer le mot de passe.</small>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirmer le Nouveau Mot de Passe:</label>
                <input type="password" id="confirm_password" name="confirm_password">
            </div>
            <button type="submit">Mettre à Jour le Profil</button>
        </form>
        <a href="index.php" class="back-link">Retour au Tableau de Bord</a>
    </div>
</body>
</html>