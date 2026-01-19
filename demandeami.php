<?php
// demandeami.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';
require 'PHPMailer-master/src/Exception.php';

include'fonctions.php';


session_start();
;

if (isset($_POST['id_ami'], $_POST['email_ami'], $_POST['nom_ami']) && isset($_SESSION['connecte'])) {
    $id_utilisateur = $_SESSION['connecte']['id'];
    $nom_envoyeur = $_SESSION['connecte']['nom'];
    $id_ami = $_POST['id_ami'];
    $email_ami = $_POST['email_ami'];
    $nom_ami = $_POST['nom_ami'];

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
        $mail->addAddress($email_ami, $nom_ami);
        $mail->Subject = 'Nouvel ami sur LE Chat';
        $mail->Body = "Hey $nom_ami ! Vous êtes devenu ami avec $nom_envoyeur sur LE CHAT !";

        $mail->send();

        $stmt = $conn->prepare("INSERT INTO amis (id_utilisateur, id_ami) VALUES (?, ?)");
        $stmt->execute([$id_utilisateur, $id_ami]);

        header("Location: explorer.php");
    } catch (Exception $e) {
        echo "Erreur lors de l'envoi de l'email : ", $mail->ErrorInfo;
    }
} else {
    header("Location: explorer.php");
    exit();
}
?>
