<?php
// Database connection details (replace with your actual credentials)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "projet";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nom = htmlspecialchars($_POST["nom"]);
        $description = htmlspecialchars($_POST["description"]);
        $prix = filter_var($_POST["prix"], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

        // Handle image upload
        $target_dir = "images/"; // Directory to save uploaded images
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

        // Basic image validation (you should add more robust checks)
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if($check === false) {
            echo "Le fichier n'est pas une image.";
            $uploadOk = 0;
        }
        if ($_FILES["image"]["size"] > 500000) {
            echo "La taille de l'image est trop grande.";
            $uploadOk = 0;
        }
        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
            echo "Seuls les formats JPG, JPEG, PNG & GIF sont autorisés.";
            $uploadOk = 0;
        }
        if ($uploadOk == 0) {
            echo "L'upload du fichier a échoué.";
        } else {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $image_path = $target_file;
                $date_ajout = date("Y-m-d H:i:s");

                // Prepare and execute the SQL query
                $stmt = $conn->prepare("INSERT INTO produits (nom, description, prix, image_path, date_ajout) VALUES (:nom, :description, :prix, :image_path, :date_ajout)");
                $stmt->bindParam(':nom', $nom);
                $stmt->bindParam(':description', $description);
                $stmt->bindParam(':prix', $prix);
                $stmt->bindParam(':image_path', $image_path);
                $stmt->bindParam(':date_ajout', $date_ajout);
                $stmt->execute();

                echo "Produit ajouté avec succès!";
                header("Location: index.php"); // Redirect to the main page
                exit();
            } else {
                echo "Erreur lors de l'upload de l'image.";
            }
        }
    }
} catch(PDOException $e) {
    echo "Erreur de connexion à la base de données: " . $e->getMessage();
}
$conn = null;
?>