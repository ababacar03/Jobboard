<?php
session_start();
$servername = "localhost";
$username = "phpmyadmin";
$password = "Babs03$#Secure";
$dbname = "jobboard";

// Connexion à la base de données
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérification de la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    // Rediriger vers la page de connexion si l'utilisateur n'est pas connecté
    header('Location: login.html');
    exit;
}

// Vérifier si l'utilisateur a le rôle d'**applicant**
if ($_SESSION['role'] !== 'applicant') {
    // Si ce n'est pas un applicant, rediriger vers une autre page ou afficher un message d'erreur
    header('Location: unauthorized.php'); 
    exit;
}

// Récupérer l'ID de l'annonce depuis l'URL
$job_id = isset($_GET['job_id']) ? intval($_GET['job_id']) : 0;

if ($job_id > 0) {
    // Requête pour récupérer les informations de l'annonce
    $sql = "SELECT title, short_description FROM advertisements WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $job_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $job = $result->fetch_assoc();

    // Vérifiez si l'annonce existe
    if (!$job) {
        die("Annonce introuvable.");
    }
} else {
    die("ID de l'annonce invalide.");
}

// Traitement du formulaire de candidature
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $applicant_id = $_SESSION['user_id']; // Récupérer l'ID de l'utilisateur connecté depuis la session
    $email_content = $_POST['cover_letter'];

    // Insertion de la candidature dans la base de données
    $sql = "INSERT INTO job_applications (applicant_id, advertisement_id, email_content, status) 
            VALUES (?, ?, ?, 'pending')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iis", $applicant_id, $job_id, $email_content);

    if ($stmt->execute()) {
        echo "<p>Candidature soumise avec succès !</p>";
    } else {
        echo "<p>Erreur lors de la soumission de la candidature : " . $stmt->error . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Postuler à l'offre : <?php echo htmlspecialchars($job['title']); ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <style>
        /* Centrer le contenu sur la page */
        body {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            max-width: 400px;
            margin-top: 50px;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary"> <!-- Navbar avec fond bleu -->
        <a class="navbar-brand" href="index.html">
            <img src="logo.png" width="30" height="30" alt="MAemplois"> 
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Déconnexion</a> <!-- Lien vers la déconnexion -->
                </li>
            </ul>
        </div>
    </nav>

    <!-- Formulaire de Postulation -->
    <div class="container">
        <h1 class="text-center mb-4">Postuler à l'offre : <?php echo htmlspecialchars($job['title']); ?></h1>
        <p class="text-center"><strong>Description :</strong> <?php echo htmlspecialchars($job['short_description']); ?></p>
        <form method="POST">
            <div class="form-group">
                <label for="cover_letter">Lettre de motivation :</label>
                <textarea name="cover_letter" id="cover_letter" class="form-control" rows="6" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Envoyer la candidature</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
