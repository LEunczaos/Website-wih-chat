<?php
include('connexion.php');

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['connecte'])) {
    header("Location:connexion.php");
    exit();
}

$user = $_SESSION['connecte'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $telephone = $_POST['telephone'];
    $nationalite = $_POST['nationalite'];

    // Gestion de la photo
    $photo = $user['photo'];
    if (!empty($_FILES['photo']['name'])) {
        $photo = uniqid() . "_" . basename($_FILES['photo']['name']);
        move_uploaded_file($_FILES['photo']['tmp_name'], "uploads/" . $photo);
    }

    // Mise à jour dans la base
    $stmt = $conn->prepare("UPDATE info SET nom=?, prenom=?, email=?, telephone=?, nationalite=?, photo=? WHERE email=?");
    $stmt->execute([$nom, $prenom, $email, $telephone, $nationalite, $photo, $user['email']]);

    // Mettre à jour la session
    $_SESSION['connecte'] = [
        'nom' => $nom,
        'prenom' => $prenom,
        'email' => $email,
        'telephone' => $telephone,
        'nationalite' => $nationalite,
        'photo' => $photo
    ];

    header("Location: profil.php?success=1");
    exit();
}

$user = $_SESSION['connecte'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Profil</title>
    <link rel="stylesheet" href="mrsocial.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>
<div class="container-profil">
    <h2 class="header-title">Mon profil</h2>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert-success">Profil mis à jour avec succès.</div>
    <?php endif; ?>

   <form method="POST" enctype="multipart/form-data" class="form-profil-card">
    <div class="form-group">
      <label>Modifier votre Photo de profil</label>
        <input type="file" name="photo">
        <?php if (!empty($user['photo'])): ?>
            <p>Photo actuelle :</p>
            <img src="uploads/<?= htmlspecialchars($user['photo']) ?>" alt="Photo de profil" style="max-width:100px; border-radius: 5px;">
        <?php endif; ?>
        <br><br>
        </div>

    <div class="form-group">
        <label>Nom</label>
        <input type="text" name="nom" class="form-input" value="<?= htmlspecialchars($user['nom']) ?>" required>
    </div>
<br><br>
    <div class="form-group">
        <label>Prénom</label>
        <input type="text" name="prenom" class="form-input" value="<?= htmlspecialchars($user['prenom']) ?>" required>
    </div>
<br><br>
    <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" class="form-input" value="<?= htmlspecialchars($user['email']) ?>" required>
    </div>
<br><br>
    <div class="form-group">
        <label>Téléphone</label>
        <input type="text" name="telephone" class="form-input" value="<?= htmlspecialchars($user['telephone']) ?>" required>
    </div>
<br><br>
    <div class="form-group">
        <label>Nationalité</label>
        <input type="text" name="nationalite" class="form-input" value="<?= htmlspecialchars($user['nationalite']) ?>" required>
    </div>
<br><br>
    <button type="submit" class="btn-save">Enregistrer les modifications</button>
</form>

</div>
</body>
</html>
