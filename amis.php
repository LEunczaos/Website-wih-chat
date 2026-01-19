<?php

require_once 'connexion.php';

if (!isset($_SESSION['connecte'])) {
    header('Location: connexion.php');
    exit();
}

$user_id = $_SESSION['connecte']['id'];

$stmt = $conn->prepare("SELECT u.id, u.nom, u.prenom, u.email, u.telephone, u.nationalite, u.photo FROM utilisateurs u
    JOIN amis a ON (u.id = a.id_utilisateur AND a.id_ami = ?) OR (u.id = a.id_ami AND a.id_utilisateur = ?)
    WHERE u.id != ?");
$stmt->execute([$user_id, $user_id, $user_id]);
$amis = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes amis</title>
    <link rel="stylesheet" href="mrsocial.css">
    <style>
        .amis-container {
            margin-left: 250px;
            padding: 20px;
        }
        .ami-ligne {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background-color: #fff;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .ami-ligne img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            margin-right: 20px;
        }
        .ami-nom {
            flex-grow: 1;
            font-weight: bold;
            font-size: 1.2em;
        }
        .voir-btn {
            padding: 10px 20px;
            background-color: #a6c1ee;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }
        .voir-btn:hover {
            background-color: #8aa1d9;
        }
        .modal {
            display: none;
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.6);
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            width: 400px;
            text-align: center;
        }
        .close-btn {
            float: right;
            font-size: 20px;
            cursor: pointer;
            color: #999;
        }
    </style>
</head>
<body>
<?php include 'sidebar.php'; ?>
<div class="amis-container">
    <h2>Mes amis</h2>
    <?php foreach ($amis as $ami): ?>
        <div class="ami-ligne">
            <img src="uploads/<?= htmlspecialchars($ami['photo']) ?>" alt="Photo">
            <div class="ami-nom"><?= htmlspecialchars($ami['nom'] . ' ' . $ami['prenom']) ?></div>
            <button class="voir-btn" onclick="openModal(<?= htmlspecialchars(json_encode($ami)) ?>)">Voir profil</button>
        </div>
    <?php endforeach; ?>
</div>

<div class="modal" id="profilModal">
    <div class="modal-content">
        <span class="close-btn" onclick="closeModal()">&times;</span>
        <h3 id="modalNom"></h3>
        <img id="modalPhoto" src="" alt="Photo" style="width: 100px; height: 100px; border-radius: 50%; margin: 10px auto;">
        <p>Email: <span id="modalEmail"></span></p>
        <p>Téléphone: <span id="modalTel"></span></p>
        <p>Nationalité: <span id="modalNat"></span></p>
    </div>
</div>

<script>
    function openModal(ami) {
        document.getElementById('modalNom').textContent = ami.nom + ' ' + ami.prenom;
        document.getElementById('modalPhoto').src = 'uploads/' + ami.photo;
        document.getElementById('modalEmail').textContent = ami.email;
        document.getElementById('modalTel').textContent = ami.telephone;
        document.getElementById('modalNat').textContent = ami.nationalite;
        document.getElementById('profilModal').style.display = 'flex';
    }
    function closeModal() {
        document.getElementById('profilModal').style.display = 'none';
    }
</script>
</body>
</html>
