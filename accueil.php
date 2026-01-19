<?php
session_start();
// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['connecte'])) {
    // Redirection vers la page de connexion si pas connecté
    header('Location: connexion.php');
    exit();
}

// Récupération des données utilisateur depuis la session
$user = $_SESSION['connecte'];

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil - Netly</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        margin: 0;
        overflow-x: hidden;
        background: linear-gradient(135deg, #00c9a7, #4dabf7);
    }

    .background-gradient {
        display: none; /* Supprimé au profit du body background */
    }

    .sidebar {
        position: fixed;
        top: 0;
        left: 0;
        height: 100%;
        width: 240px;
        background-color: #1b2a41;
        padding-top: 60px;
        transition: width 0.3s ease;
        overflow-x: hidden;
        z-index: 1;
        box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
    }

    .sidebar.collapsed {
        width: 60px;
    }

    .sidebar a {
        padding: 15px 20px;
        text-decoration: none;
        font-size: 16px;
        color: #ecf0f1;
        display: flex;
        align-items: center;
        transition: background 0.3s ease;
    }

    .sidebar a:hover {
        background-color: #2e3d59;
    }

    .sidebar i {
        margin-right: 10px;
        font-size: 18px;
    }

    .sidebar.collapsed .menu-text {
        display: none;
    }

    .content {
        margin-left: 240px;
        padding: 30px;
        transition: margin-left 0.3s ease;
        position: relative;
        z-index: 0;
        color: #fff;
        min-height: 100vh;
    }

    .sidebar.collapsed ~ .content {
        margin-left: 60px;
    }

    .toggle-btn {
        position: absolute;
        top: 10px;
        left: 10px;
        background-color: #00c9a7;
        color: white;
        border: none;
        padding: 8px 12px;
        cursor: pointer;
        z-index: 2;
        border-radius: 4px;
        font-weight: bold;
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.15);
    }

    .toggle-btn:hover {
        background-color: #00b38f;
    }
</style>

</head>
<body>
    <div class="background-gradient"></div>

    <div class="sidebar" id="sidebar">
        <button class="toggle-btn" onclick="toggleSidebar()">☰</button>
        <a href="profil.php"><i class="fas fa-user"></i> <span class="menu-text">Profil</span></a>
        <a href="explorer.php"><i class="fas fa-compass"></i> <span class="menu-text">Explorer</span></a>
        <a href="amis.php"><i class="fas fa-user-friends"></i> <span class="menu-text">Amis</span></a>
        <a href="Chats.php"><i class="fas fa-comments"></i> <span class="menu-text">Chats</span></a>
        <a href="deconnexion.php"><i class="fas fa-sign-out-alt"></i> <span class="menu-text">deconnexion</span></a>
    </div>

    <div class="content" id="content">
        <!-- Contenu principal -->
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const content = document.getElementById('content');
            sidebar.classList.toggle('collapsed');
            content.classList.toggle('collapsed');
        }
    </script>
</body>
</html>
