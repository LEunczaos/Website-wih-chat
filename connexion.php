<?php
session_start();
require_once('fonctions.php'); // $conn est la connexion PDO

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ne traiter que si les champs email et mdp existent dans $_POST
    if (isset($_POST['email']) && isset($_POST['mdp'])) {
        $email = $_POST['email'];
        $mdp = $_POST['mdp'];

        verifier_champs_obligatoires([$email, $mdp]);

        $stmt = $conn->prepare("SELECT * FROM utilisateurs WHERE email = :email");
        $stmt->execute([':email' => $email]);

        if ($stmt->rowCount() === 1) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Debug : écrire dans un fichier
            file_put_contents('debug_connexion.log', "Tape: [$mdp]\nHash: [{$user['mdp']}]\n", FILE_APPEND);

            // Vérifier le mot de passe
            if (password_verify($mdp, $user['mdp'])) {
                $_SESSION['connecte'] = $user; // Stocke l'id de l'utilisateur dans la session
                echo "OK";
            } else {
                echo "motdepasse_incorrect";
            }
        } else {
            echo "email_introuvable";
        }
        exit; // Arrêter le script après réponse à connexion
    }
}
?>
