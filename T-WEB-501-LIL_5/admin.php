<?php
$servername = "localhost";
$username = "phpmyadmin";
$password = "Babs03$#Secure";
$dbname = "jobboard";

// Connexion à la base de données
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Récupérer l'action à effectuer
$action = $_GET['action'] ?? null;

if ($action === 'read') {
    // Vérifie si un 'id' est passé dans l'URL pour obtenir une annonce spécifique
    $id = $_GET['id'] ?? null;

    if ($id) {
        // Récupérer les détails d'une annonce spécifique
        $sql = "SELECT id, title, short_description, learn_more, wage, place, working_time FROM advertisements WHERE id = $id";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $job = $result->fetch_assoc();
            header('Content-Type: application/json');
            echo json_encode($job);
        } else {
            echo json_encode(["message" => "Job offer not found"]);
        }
    } else {
        // Récupérer toutes les annonces d'emploi
        $sql = "SELECT id, title, short_description, learn_more, wage, place, working_time FROM advertisements";
        $result = $conn->query($sql);

        $jobs = [];
        while ($row = $result->fetch_assoc()) {
            $jobs[] = $row;
        }

        header('Content-Type: application/json');
        echo json_encode($jobs);
    }
}

if ($action === 'create') {
    // Créer une nouvelle annonce
    $data = json_decode(file_get_contents('php://input'), true);
    $title = $data['title'];
    $short_description = $data['short_description'];
    $learn_more = $data['learn_more'];
    $wage = $data['wage'];
    $place = $data['place'];
    $working_time = $data['working_time'];

    $sql = "INSERT INTO advertisements (title, short_description, learn_more, wage, place, working_time) 
            VALUES ('$title', '$short_description', '$learn_more', '$wage', '$place', '$working_time')";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(["message" => "Job offer created successfully"]);
    } else {
        echo json_encode(["message" => "Error: " . $conn->error]);
    }
}

if ($action === 'delete') {
    // Supprimer une annonce
    $id = $_GET['id'];
    $sql = "DELETE FROM advertisements WHERE id=$id";
    
    if ($conn->query($sql) === TRUE) {
        echo json_encode(["message" => "Job offer deleted successfully"]);
    } else {
        echo json_encode(["message" => "Error: " . $conn->error]);
    }
}

if ($action === 'update') {
    // Mettre à jour une annonce
    $data = json_decode(file_get_contents('php://input'), true);
    $id = $_GET['id'];
    $title = $data['title'];
    $short_description = $data['short_description'];
    $learn_more = $data['learn_more'];
    $wage = $data['wage'];
    $place = $data['place'];
    $working_time = $data['working_time'];

    $sql = "UPDATE advertisements SET title='$title', short_description='$short_description', learn_more='$learn_more', wage='$wage', place='$place', working_time='$working_time' WHERE id=$id";
    
    if ($conn->query($sql) === TRUE) {
        echo json_encode(["message" => "Job offer updated successfully"]);
    } else {
        echo json_encode(["message" => "Error: " . $conn->error]);
    }
}

$conn->close();
?>
