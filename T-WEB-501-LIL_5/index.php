<?php
session_start(); // Démarre la session

// Vérifie si l'utilisateur est déjà connecté
if (!isset($_SESSION['user_id'])) {
    // Redirige vers la page de connexion si l'utilisateur n'est pas connecté
    header('Location: login.html');
    exit; // Termine le script après la redirection
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"> <!-- Définit l'encodage des caractères en UTF-8 -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Rend la page responsive sur les appareils mobiles -->
    <title>Offres d'Emploi</title> <!-- Titre de la page affiché dans l'onglet du navigateur -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"> <!-- Lien vers la feuille de style Bootstrap pour le style -->
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="container mt-5">
        <h1 class="text-center mb-5">Offres d'Emploi</h1>

        <div id="job-list" class="row"> <!-- Container for job cards -->
            <p class="text-center">Chargement des offres d'emploi...</p> <!-- Loading message -->
        </div>
    </div>

    <script src="index.js"></script>
</body>
</html>
