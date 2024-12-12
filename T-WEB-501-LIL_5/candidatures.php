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

// Récupérer l'ID de l'utilisateur connecté
$applicant_id = $_SESSION['user_id'];

// Récupérer les candidatures de l'utilisateur connecté
$sql = "SELECT ja.id, a.title, a.place, ja.application_date, ja.status 
        FROM job_applications ja 
        JOIN advertisements a ON ja.advertisement_id = a.id 
        WHERE ja.applicant_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $applicant_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Candidatures</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css"> <!-- Lien vers le fichier CSS -->
    <style>
        /* Sticky footer */
        html, body {
            height: 100%;
        }
        body {
            display: flex;
            flex-direction: column;
        }
        .container {
            flex: 1;
        }
        footer {
            background-color: #007bff; /* Footer bleu */
            color: white; /* Texte blanc */
            padding: 10px;
            position: relative;
            bottom: 0;
            width: 100%;
            text-align: center;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary"> <!-- Navbar avec fond bleu -->
        <a class="navbar-brand" href="userpage.html">
            <img src="logo.png" width="30" height="30" alt="MAemplois">
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Déconnexion</a> <!-- Lien vers la déconnexion -->
                    </li>
                <?php else: ?>
                    <!-- Afficher "Se connecter" si l'utilisateur n'est pas connecté -->
                    <li class="nav-item">
                        <a class="nav-link" href="login.html">Se connecter</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <!-- Contenu principal -->
    <div class="container mt-5">
        <h1>Mes Candidatures</h1>
        
        <?php if ($result->num_rows > 0): ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Titre de l'annonce</th>
                        <th>Lieu</th>
                        <th>Date de candidature</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($application = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($application['title']); ?></td>
                            <td><?php echo htmlspecialchars($application['place']); ?></td>
                            <td><?php echo htmlspecialchars($application['application_date']); ?></td>
                            <td>
                                <?php
                                    if ($application['status'] == 'pending') {
                                        echo "En attente";
                                    } elseif ($application['status'] == 'accepted') {
                                        echo "Acceptée";
                                    } else {
                                        echo "Rejetée";
                                    }
                                ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Vous n'avez pas encore soumis de candidature.</p>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer class="bg-primary text-white text-center text-lg-start mt-5"> <!-- Sticky footer bleu avec texte blanc -->
        <div class="container p-4">
            <p class="text-center">© 2024 MAemplois. Tous droits réservés.</p>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// Fermer la connexion à la base de données
$conn->close();
?>
