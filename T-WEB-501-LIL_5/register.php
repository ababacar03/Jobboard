<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $firstName = $_POST['first_name'];
    $lastName = $_POST['last_name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Hachage du mot de passe
    $role = $_POST['role']; // 'recruiter' ou 'applicant'

    // Générer un token unique
    $token = bin2hex(random_bytes(32)); // Générer un token aléatoire de 32 octets

    // Connexion à la base de données
    $conn = new mysqli('localhost', 'phpmyadmin', 'Babs03$#Secure', 'jobboard');

    // Vérifier si l'email est déjà utilisé
    $sql = "SELECT * FROM people WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "Cet email est déjà utilisé.";
    } else {
        // Insérer le nouvel utilisateur avec le token
        $sql = "INSERT INTO people (first_name, last_name, email, mot_de_passe, token, role) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssssss', $firstName, $lastName, $email, $password, $token, $role);

        if ($stmt->execute()) {
            echo "Inscription réussie ! Vous pouvez maintenant vous connecter.";
        } else {
            echo "Erreur lors de l'inscription.";
        }
    }

    // Fermer le statement et la connexion
    $stmt->close();
    $conn->close();
}
?>
