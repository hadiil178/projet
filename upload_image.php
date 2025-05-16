<?php
// Database connection details (replace with your actual credentials)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "projet";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["image"]) && isset($_POST["perfume_id"])) {
    $perfume_id = $_POST["perfume_id"];
    $file = $_FILES["image"];

    // File upload handling
    $uploadDir = "images/uploaded/"; // Create this directory in your 'images' folder
    $fileName = basename($file["name"]);
    $targetFilePath = $uploadDir . $fileName;
    $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

    // Allowed image file types
    $allowTypes = array('jpg','jpeg','png','gif');

    if(in_array($fileType, $allowTypes)){
        // Check if file was uploaded without errors
        if($file["error"] == 0){
            // Verify file size (optional - adjust as needed)
            if ($file["size"] < 2000000) { // Example: 2MB limit
                // Create the upload directory if it doesn't exist
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                // Move the uploaded file to the server directory
                if(move_uploaded_file($file["tmp_name"], $targetFilePath)){
                    // Update the database with the new image path
                    $imagePath = $targetFilePath; // Or just the filename if you prefer

                    $sql = "UPDATE perfumes SET image_path = :image_path WHERE id = :perfume_id";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(':image_path', $imagePath);
                    $stmt->bindParam(':perfume_id', $perfume_id, PDO::PARAM_INT);

                    if ($stmt->execute()) {
                        echo "Image uploaded and database updated successfully for Perfume ID: " . $perfume_id;
                    } else {
                        echo "Error updating database for Perfume ID: " . $perfume_id;
                    }
                } else{
                    echo "Error moving uploaded file.";
                }
            } else {
                echo "Sorry, your file is too large.";
            }
        } else{
            echo "Error uploading file.";
        }
    } else{
        echo "Sorry, only JPG, JPEG, PNG, & GIF files are allowed.";
    }
} else {
    echo "Invalid request.";
}

// After processing the upload, you might want to redirect back to the index page
header("Location: index.html");
exit();
?>