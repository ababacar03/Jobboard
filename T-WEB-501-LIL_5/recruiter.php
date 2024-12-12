<?php
session_start();

// Connexion à la base de données
$servername = "localhost";
$username = "phpmyadmin"; 
$password = "Babs03$#Secure"; 
$dbname = "jobboard";

// Créer une connexion
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("La connexion a échoué: " . $conn->connect_error);
}

// Vérifier si l'utilisateur est authentifié et a le rôle de recruteur
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'recruiter') {
    header('Location: login.php'); // Rediriger vers la page de connexion si non authentifié
    exit;
}

// Ajouter une annonce
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_advertisement'])) {
    $title = $_POST['title'];
    $short_description = $_POST['short_description'];
    $learn_more = $_POST['learn_more'];
    $wage = $_POST['wage'];
    $place = $_POST['place'];
    $working_time = $_POST['working_time'];
    $company_id = $_POST['company_id'];
    $recruiter_id = $_SESSION['user_id'];

    $sql = "INSERT INTO advertisements (title, short_description, learn_more, wage, place, working_time, company_id, recruiter_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sssdssii', $title, $short_description, $learn_more, $wage, $place, $working_time, $company_id, $recruiter_id);
    $stmt->execute();
}

// Supprimer une annonce
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $sql = "DELETE FROM advertisements WHERE id = ? AND recruiter_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $delete_id, $_SESSION['user_id']);
    $stmt->execute();
}

// Modifier une annonce
if (isset($_POST['edit_advertisement'])) {
    $ad_id = $_POST['ad_id'];
    $title = $_POST['title'];
    $short_description = $_POST['short_description'];
    $learn_more = $_POST['learn_more'];
    $wage = $_POST['wage'];
    $place = $_POST['place'];
    $working_time = $_POST['working_time'];

    $sql = "UPDATE advertisements SET title = ?, short_description = ?, learn_more = ?, wage = ?, place = ?, working_time = ? WHERE id = ? AND recruiter_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sssdssii', $title, $short_description, $learn_more, $wage, $place, $working_time, $ad_id, $_SESSION['user_id']);
    $stmt->execute();
}

// Récupérer les annonces du recruteur
$sql = "SELECT * FROM advertisements WHERE recruiter_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $_SESSION['user_id']);
$stmt->execute();
$ads_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Page Recruteur</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <style>
        /* Styles globaux */
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa; /* Couleur de fond claire */
            margin: 0;
            padding: 0;
            min-height: 100vh; /* S'assurer que la hauteur minimale du body est de 100% de la hauteur de la fenêtre */
            display: flex;
            flex-direction: column;
        }

        /* Container principal */
        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            flex: 1; /* S'assurer que le container prend tout l'espace disponible */
        }

        /* Titre principal */
        h1, h2 {
            text-align: center;
            color: #007bff; /* Couleur principale */
        }

        /* Table des annonces */
        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        table th, table td {
            padding: 10px;
            border: 1px solid #ddd;
        }

        table th {
            background-color: #007bff;
            color: white;
        }

        table td {
            background-color: #f9f9f9;
        }

        /* Styles du formulaire */
        form {
            margin-top: 30px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            font-weight: bold;
            margin-bottom: 5px;
            display: block;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        /* Bouton d'ajout */
        button {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            border: none;
            color: white;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        }

        button:hover {
            background-color: #0056b3;
        }

        /* Liens d'action */
        a {
            color: #007bff;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        /* Footer */
        footer {
            background-color: #007bff;
            color: white;
            text-align: center;
            padding: 10px 0;
            width: 100%;
            position: relative;
            bottom: 0;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <a class="navbar-brand" href="index.html">MAemplois</a>
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

    <div class="container mt-5">
        <h1>Bienvenue, <?php echo $_SESSION['first_name']; ?></h1>

        <h2>Ajouter une Nouvelle Annonce</h2>
        <form id="add-advertisement-form" method="POST">
            <div class="form-group">
                <label for="title">Titre de l'annonce</label>
                <input type="text" id="title" name="title" required>
            </div>

            <div class="form-group">
                <label for="short_description">Description Courte</label>
                <textarea id="short_description" name="short_description" rows="3" required></textarea>
            </div>

            <div class="form-group">
                <label for="learn_more">Lien En Savoir Plus</label>
                <input type="url" id="learn_more" name="learn_more" required>
            </div>

            <div class="form-group">
                <label for="wage">Salaire (€)</label>
                <input type="number" id="wage" name="wage" required>
            </div>

            <div class="form-group">
                <label for="place">Lieu</label>
                <input type="text" id="place" name="place" required>
            </div>

            <div class="form-group">
                <label for="working_time">Temps de Travail</label>
                <input type="text" id="working_time" name="working_time" required>
            </div>

            <button type="submit" name="add_advertisement">Ajouter l'annonce</button>
        </form>

        <h2>Mes Annonces</h2>
        <table class="table table-bordered" id="advertisements-table">
            <tr>
                <th>Titre</th>
                <th>Description Courte</th>
                <th>Action</th>
            </tr>
            <?php while ($ad = $ads_result->fetch_assoc()): ?>
                <tr id="ad-row-<?php echo $ad['id']; ?>">
                    <td><?php echo $ad['title']; ?></td>
                    <td><?php echo $ad['short_description']; ?></td>
                    <td>
                        <a href="recruiter.php?delete_id=<?php echo $ad['id']; ?>">Supprimer</a>
                        <button type="button" onclick="editAdvertisement(<?php echo $ad['id']; ?>, '<?php echo addslashes($ad['title']); ?>', '<?php echo addslashes($ad['short_description']); ?>', '<?php echo addslashes($ad['learn_more']); ?>', <?php echo $ad['wage']; ?>, '<?php echo addslashes($ad['place']); ?>', '<?php echo addslashes($ad['working_time']); ?>')">Modifier</button>
                    </td>
                </tr>
                <tr>
                    <td colspan="3">
                        <strong>Candidatures reçues :</strong>
                        <ul>
                            <?php
                            // Requête pour récupérer les candidatures pour cette annonce
                            $sql = "SELECT p.first_name, p.last_name, p.email, ja.email_content FROM job_applications ja 
                                    JOIN people p ON ja.applicant_id = p.id 
                                    WHERE ja.advertisement_id = ?";
                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param('i', $ad['id']);
                            $stmt->execute();
                            $applications_result = $stmt->get_result();
                            if ($applications_result->num_rows > 0) {
                                while ($applicant = $applications_result->fetch_assoc()) {
                                    echo "<li><strong>" . $applicant['first_name'] . " " . $applicant['last_name'] . "</strong> (" . $applicant['email'] . ") - <em>" . $applicant['email_content'] . "</em></li>";
                                }
                            } else {
                                echo "<li>Aucune candidature pour cette annonce.</li>";
                            }
                            ?>
                        </ul>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>

    <!-- Footer -->
    <footer>
        <p>© 2024 MAemplois. Tous droits réservés.</p>
    </footer>

    <script src="recruiter.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conn->close(); 
?>
