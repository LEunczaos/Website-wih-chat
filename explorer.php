<?php

require_once 'connexion.php';

if (!isset($_SESSION['connecte'])) {
    header("Location: connexion.php");
    exit();
}

$id_utilisateur = $_SESSION['connecte']['id'];
$email_utilisateur = $_SESSION['connecte']['email'];

// Tous les utilisateurs sauf soi-même
$stmt = $conn->prepare("SELECT * FROM utilisateurs WHERE id != ?");
$stmt->execute([$id_utilisateur]);
$utilisateurs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Les amis
$amis = $conn->prepare("SELECT id_ami FROM amis WHERE id_utilisateur = ? AND statut = 'ami'");
$amis->execute([$id_utilisateur]);
$ids_amis = $amis->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Explorer</title>
    <link rel="stylesheet" href="mrsocial.css">
    <style>
        .users-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        .user-card {
            width: 200px;
            border: 1px solid #ddd;
            border-radius: 12px;
            overflow: hidden;
            position: relative;
            text-align: center;
            background: #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .user-card img.profile-pic {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        .user-card h3 {
            margin: 10px 0;
            font-size: 1.1em;
        }
        .user-card button {
            margin-bottom: 10px;
            padding: 8px 12px;
            border: none;
            background-color: #228b22;
            color: white;
            border-radius: 6px;
            cursor: pointer;
        }
        .ami-label {
            position: absolute;
            top: 8px;
            left: 8px;
            background: #28a745;
            color: white;
            padding: 4px 8px;
            font-size: 0.9em;
            border-radius: 4px;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.6);
        }

        .modal-content {
            background-color: #fff;
            margin: 5% auto;
            padding: 20px;
            border-radius: 12px;
            width: 400px;
            position: relative;
        }

        .close {
            color: #aaa;
            position: absolute;
            right: 10px;
            top: 10px;
            font-size: 24px;
            cursor: pointer;
        }

        .chat-btn {
            background-color: #007bff;
            padding: 8px 16px;
            color: white;
            border-radius: 6px;
            text-decoration: none;
            display: inline-block;
        }
    </style>
</head>
<body>
<?php include 'sidebar.php'; ?>

<div class="main-content">
    <div class="header-title">Explorez les utilisateurs</div>
    <div class="users-grid">
        <?php foreach ($utilisateurs as $u): ?>
            <div class="user-card">
                <?php if (in_array($u['id'], $ids_amis)): ?>
                    <div class="ami-label">Votre ami(e)</div>
                <?php endif; ?>
                <img src="uploads/<?= htmlspecialchars($u['photo']) ?>" alt="Photo" class="profile-pic">
                <h3><?= htmlspecialchars($u['nom']) ?></h3>
                <button onclick="openModal(<?= $u['id'] ?>)">Voir le profil</button>
            </div>

            <!-- Modal -->
            <div class="modal" id="modal-<?= $u['id'] ?>">
                <div class="modal-content">
                    <span onclick="closeModal(<?= $u['id'] ?>)" class="close">&times;</span>
                    <h2><?= htmlspecialchars($u['nom']) ?></h2>
                    <p><strong>Email:</strong> <?= htmlspecialchars($u['email']) ?></p>
                    <p><strong>Téléphone:</strong> <?= htmlspecialchars($u['telephone']) ?></p>
                    <p><strong>Sexe:</strong> <?= htmlspecialchars($u['sexe']) ?></p>
                    <p><strong>État civil:</strong> <?= htmlspecialchars($u['etatcivil']) ?></p>

                    <?php if (in_array($u['id'], $ids_amis)): ?>
                        <a href="chats.php?avec=<?= $u['id'] ?>" class="chat-btn">Discuter</a>
                    <?php else: ?>
                        <form method="POST" action="demandeami.php">
                            <input type="hidden" name="id_ami" value="<?= $u['id'] ?>">
                            <input type="hidden" name="email_ami" value="<?= $u['email'] ?>">
                            <input type="hidden" name="nom_ami" value="<?= $u['nom'] ?>">
                            <button type="submit">Devenir ami</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
    function openModal(id) {
        document.getElementById('modal-' + id).style.display = 'block';
    }

    function closeModal(id) {
        document.getElementById('modal-' + id).style.display = 'none';
    }
</script>
</body>
</html>
