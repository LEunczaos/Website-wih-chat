<?php

function connexion($host, $name, $pass, $user) {
        $pdo = new PDO("mysql:host=$host;dbname=$name;charset=utf8", $user, $pass);
        return $pdo;
}

$conn = connexion('localhost', 'macl0202_devoirlucas', 'phpmyadmin1234', 'macl0202_admin');

function insertion($pdo, $nom, $prenom, $sexe, $datenaiss, $lieunaiss, $nationalite, $etatcivil, $telephone, $email, $mdp, $photo) {
    $insert = $pdo->prepare("
        INSERT INTO utilisateurs 
        (nom, prenom, sexe, datenaiss, lieunaiss, nationalite, etatcivil, telephone, email, mdp, photo) 
        VALUES 
        (:nom, :prenom, :sexe, :datenaiss, :lieunaiss, :nationalite, :etatcivil,:telephone, :email, :mdp, :photo)
    ");

    $insert->execute([
        ':nom' => $nom,
        ':prenom' => $prenom,
        ':sexe'=> $sexe,
        ':datenaiss' => $datenaiss,
        ':lieunaiss' => $lieunaiss,
        ':nationalite' => $nationalite,
        ':etatcivil'=> $etatcivil,
        ':telephone' => $telephone,
        ':email' => $email,
        ':mdp' => $mdp,  // ici on ne touche plus, c'est déjà un hash
        ':photo' => $photo
    ]);
}

function verifier_champs_obligatoires(array $champs): void {
    foreach ($champs as $champ) {
        if (empty(trim($champ))) {
            // Tu peux ici déclencher une exception, un return, ou un arrêt
            http_response_code(400); // Code HTTP correct pour erreur de requête
            die(json_encode([
                'success' => false,
                'error' => 'Veuillez remplir tous les champs'
            ]));
        }
    }
}



?>
