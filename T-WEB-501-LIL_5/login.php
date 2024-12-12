<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $is_admin_login = isset($_POST['admin_login']); // Vérifier si l'utilisateur tente de se connecter en tant qu'admin

    // Connexion à la base de données
    $conn = new mysqli('localhost', 'phpmyadmin', 'Babs03$#Secure', 'jobboard');

    // Si connexion échoue
    if ($conn->connect_error) {
        die("La connexion a échoué: " . $conn->connect_error);
    }

    // Si l'utilisateur tente de se connecter en tant qu'admin
    if ($is_admin_login) {
        // Vérifier si l'utilisateur est un administrateur
        $sql = "SELECT * FROM people WHERE email = ? AND role = 'admin'";
    } else {
        // Vérifier pour un utilisateur régulier ou recruteur
        $sql = "SELECT * FROM people WHERE email = ?";
    }

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Vérifier si l'utilisateur existe et si le mot de passe correspond
    if ($user && password_verify($password, $user['mot_de_passe'])) {
        // Authentification réussie, stocker les informations dans la session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['first_name'] = $user['first_name'];
        $_SESSION['role'] = $user['role'];

        // Vérifier si le token est présent, sinon générer un token
        if (empty($user['token'])) {
            $token = bin2hex(random_bytes(32)); // Générer un token unique
            // Mettre à jour le token dans la base de données
            $updateSql = "UPDATE people SET token = ? WHERE id = ?";
            $updateStmt = $conn->prepare($updateSql);
            $updateStmt->bind_param('si', $token, $user['id']);
            $updateStmt->execute();
        } else {
            $token = $user['token']; // Utiliser le token existant
        }

        // Stocker le token dans la session
        $_SESSION['token'] = $token;

        // Redirection en fonction du rôle
        if ($is_admin_login && $user['role'] == 'admin') {
            header('Location: admin.html'); // Redirige vers la page admin pour les administrateurs
        } elseif ($user['role'] == 'recruiter') {
            header('Location: recruiter.php'); // Redirige vers la page admin pour recruteurs
        } else {
            header('Location: userpage.html'); // Redirige vers la page d'accueil pour les candidats
        }
        exit;
    } else {
        // Si l'authentification échoue
        echo "Email ou mot de passe incorrect.";
    }

    // Fermer le statement et la connexion
    $stmt->close();
    $conn->close();
}
?> 