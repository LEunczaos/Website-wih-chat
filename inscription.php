<?php
ob_start();
session_start();
include('fonctions.php');

// PHPMailer sans composer (vendor)
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';
require 'PHPMailer-master/src/Exception.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $sexe = $_POST['sexe'];
    $datenaiss = $_POST['datenaiss'];
    $lieunaiss = $_POST['lieunaiss'];
    $nationalite = $_POST['nationalite'];
    $etatcivil = $_POST['etatcivil'];
    $telephone = $_POST['telephone'];
    $email = $_POST['email'];
    $mdp = password_hash($_POST['mdp'], PASSWORD_DEFAULT); // hash du mot de passe

    // Vérifier si l'email existe déjà
    $check = $conn->prepare("SELECT * FROM utilisateurs WHERE email = :email");
    $check->execute([':email' => $email]);

    if ($check->rowCount() > 0) {
        echo "<script>alert('Email déjà utilisé');</script>";
        exit;
    }

    // Gérer l'upload de la photo
    $photo_nom = '';
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === 0) {
        $extension = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        $photo_nom = date('dmYHis') . '.' . $extension;
        move_uploaded_file($_FILES['photo']['tmp_name'], "uploads/" . $photo_nom);
    }

    // Générer un code de confirmation
    $code = rand(100000, 999999);

    // Stocker les infos dans la session
    $_SESSION['inscription_temp'] = [
        'nom' => $nom,
        'prenom' => $prenom,
        'sexe'=> $sexe,
        'datenaiss' => $datenaiss,
        'lieunaiss' => $lieunaiss,
        'nationalite' => $nationalite,
        'etatcivil' => $etatcivil,
        'telephone' => $telephone,
        'email' => $email,
        'mdp' => $mdp,
        'photo' => $photo_nom,
        'code' => $code
    ];

    // Envoi du mail avec PHPMailer
    $mail = new PHPMailer(true);

    try {
       $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = '71ele17@gmail.com';
        $mail->Password = 'oveglxclwyjbyoem';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('71ele17@gmail.com', 'LE Chat');
        $mail->addAddress($email, $prenom . ' ' . $nom);

        $mail->isHTML(true);
        $mail->Subject = "Votre code de confirmation";

        $mail->Body = '
    <div style="background:#e6f2f1;padding:30px;font-family:\'Segoe UI\',sans-serif;">
        <div style="max-width:550px;margin:auto;background:white;border-radius:12px;box-shadow:0 4px 12px rgba(0,0,0,0.08);padding:30px;">
            <div style="text-align:center;">
                <h2 style="color:#007bff;margin-bottom:10px;">Bienvenue sur <span style="color:#00b894;">LE CHAT</span></h2>
                <p style="color:#555;font-size:16px;">Bonjour <b>' . htmlspecialchars($prenom) . '</b>,</p>
                <p style="color:#444;font-size:15px;">Merci pour votre inscription !<br>Voici votre code de confirmation :</p>
                <div style="font-size:32px;color:#00b894;text-align:center;margin:30px 0;font-weight:bold;letter-spacing:5px;">' . $code . '</div>
                <p style="color:#555;font-size:14px;">Veuillez saisir ce code pour valider votre inscription.</p>
            </div>
            <hr style="margin:30px 0;border:0;border-top:1px solid #ccc;">
            <p style="font-size:13px;color:#999;text-align:center;">
                Cet email a été généré automatiquement, merci de ne pas y répondre.<br>
                &copy; ' . date('Y') . ' <span style="color:#007bff;">LE CHAT</span>
            </p>
        </div>
    </div>';

        $mail->AltBody = "Bonjour $prenom,\n\nVotre code de confirmation est : $code\n\nMerci.";

        $mail->send();
        echo "OK";
    exit;
    } catch (Exception $e) {
        echo "<script>alert('Erreur lors de l\'envoi du mail : {$mail->ErrorInfo}');</script>";
        exit;
    }

  
}
?>
