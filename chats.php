<?php

require_once 'connexion.php';

if (!isset($_SESSION['connecte'])) {
    header("Location: connexion.php");
    exit();
}

$mon_id = $_SESSION['connecte']['id'];

if (!isset($_GET['avec'])) {
    echo "Aucun utilisateur spécifié.";
    exit();
}

$ami_id = (int)$_GET['avec'];

// Vérifier qu'ils sont amis
$check = $conn->prepare("SELECT * FROM amis WHERE id_utilisateur = ? AND id_ami = ? AND statut = 'ami'");
$check->execute([$mon_id, $ami_id]);
if ($check->rowCount() == 0) {
    echo "Vous n'êtes pas amis.";
    exit();
}

// Récupérer les infos de l'ami
$ami = $conn->prepare("SELECT nom, photo FROM utilisateurs WHERE id = ?");
$ami->execute([$ami_id]);
$ami_data = $ami->fetch(PDO::FETCH_ASSOC);
$ami_nom = $ami_data['nom'];
$ami_photo = $ami_data['photo'] ?: 'default.png';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Discussion avec <?= htmlspecialchars($ami_nom) ?></title>
    <link rel="stylesheet" href="mrsocial.css" />
    <style>
        body {
    background: #eef5f3;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.chat-box {
    max-width: 700px;
    margin: 30px auto;
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 6px 20px rgba(0, 128, 128, 0.15);
    display: flex;
    flex-direction: column;
    height: 80vh;
}

.chat-header {
    display: flex;
    align-items: center;
    padding: 15px 20px;
    border-bottom: 1px solid #cde8e4;
    background: linear-gradient(90deg, #1abc9c, #3498db);
    color: white;
    border-top-left-radius: 12px;
    border-top-right-radius: 12px;
}

.chat-header img {
    width: 50px;
    height: 50px;
    object-fit: cover;
    border-radius: 50%;
    margin-right: 15px;
    border: 2px solid white;
}

.messages-container {
    flex-grow: 1;
    padding: 15px 20px;
    overflow-y: auto;
    background-color: #f4fbfa;
}

.message {
    max-width: 65%;
    margin-bottom: 14px;
    padding: 12px 16px;
    border-radius: 20px;
    position: relative;
    font-size: 15px;
    line-height: 1.5;
    word-wrap: break-word;
    white-space: pre-wrap;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.sent {
    background-color: #2ecc71;
    color: white;
    margin-left: auto;
    border-bottom-right-radius: 0;
}

.received {
    background-color: #d5f5e3;
    color: #2c3e50;
    margin-right: auto;
    border-bottom-left-radius: 0;
}

.timestamp {
    font-size: 0.75em;
    color: #7f8c8d;
    margin-top: 5px;
    text-align: right;
}

form#form-message {
    display: flex;
    padding: 15px 20px;
    border-top: 1px solid #cde8e4;
    background-color: #ffffff;
    border-bottom-left-radius: 12px;
    border-bottom-right-radius: 12px;
}

form#form-message textarea {
    flex-grow: 1;
    resize: none;
    border-radius: 25px;
    border: 1px solid #b2dfdb;
    padding: 10px 15px;
    font-size: 15px;
    outline: none;
    height: 50px;
    font-family: inherit;
    background-color: #f8ffff;
    transition: border-color 0.3s ease;
}

form#form-message textarea:focus {
    border-color: #1abc9c;
}

form#form-message button {
    margin-left: 10px;
    background: linear-gradient(to right, #1abc9c, #16a085);
    border: none;
    color: white;
    border-radius: 25px;
    padding: 0 22px;
    font-size: 16px;
    cursor: pointer;
    transition: background 0.3s ease;
}

form#form-message button:hover {
    background: linear-gradient(to right, #16a085, #138d75);
}

    </style>
</head>
<body>
<?php include 'sidebar.php'; ?>

<div class="chat-box">
    <div class="chat-header">
        <img src="uploads/<?= htmlspecialchars($ami_photo) ?>" alt="Photo de profil" />
        <h3>Discussion avec <?= htmlspecialchars($ami_nom) ?></h3>
    </div>

    <div class="messages-container" id="messages-container"></div>

    <form id="form-message" method="POST" autocomplete="off">
        <input type="hidden" name="destinataire_id" value="<?= $ami_id ?>" />
        <textarea name="message" placeholder="Écrivez votre message..." required></textarea>
        <button type="submit">Envoyer</button>
    </form>
</div>

<script>
const mon_id = <?= json_encode($mon_id) ?>;
const ami_id = <?= json_encode($ami_id) ?>;

const messagesContainer = document.getElementById('messages-container');

function formatTimestamp(dateStr) {
    const d = new Date(dateStr);
    return d.toLocaleString('fr-FR', {
        hour: '2-digit',
        minute: '2-digit',
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
}

function chargerMessages() {
    fetch(`recuperer_message.php?avec=${ami_id}`)
    .then(res => res.json())
    .then(data => {
        messagesContainer.innerHTML = '';
        data.forEach(msg => {
            const div = document.createElement('div');
            div.classList.add('message');
            if (msg.expediteur_id == mon_id) {
                div.classList.add('sent');
            } else {
                div.classList.add('received');
            }
            // sécuriser le message en échappant HTML pour éviter injection, puis remplacer les sauts de ligne par <br>
            const safeMessage = msg.message.replace(/&/g, "&amp;")
                                           .replace(/</g, "&lt;")
                                           .replace(/>/g, "&gt;")
                                           .replace(/"/g, "&quot;")
                                           .replace(/'/g, "&#039;")
                                           .replace(/\n/g, '<br>');
            div.innerHTML = `
                <div>${safeMessage}</div>
                <div class="timestamp">${formatTimestamp(msg.date_envoi)}</div>
            `;
            messagesContainer.appendChild(div);
        });
        // Scroll automatique en bas
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    })
    .catch(console.error);
}

document.getElementById('form-message').addEventListener('submit', e => {
    e.preventDefault();
    const form = e.target;
    const message = form.message.value.trim();
    if (!message) return;

    const formData = new FormData();
    formData.append('destinataire_id', ami_id);
    formData.append('message', message);

    fetch('envoyer_message.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            form.message.value = '';
            chargerMessages();
        } else {
            alert(data.error || 'Erreur lors de l\'envoi du message');
        }
    })
    .catch(console.error);
});

// Chargement initial + refresh toutes les 3 secondes
chargerMessages();
setInterval(chargerMessages, 3000);
</script>
</body>
</html>
