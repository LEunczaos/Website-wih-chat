<!-- sidebar.php -->
<style>
  /* Sidebar container */
  .sidebar {
    height: 100vh;
    width: 220px;
    background-color: #2c3e50;
    color: white;
    position: fixed;
    top: 0; left: 0;
    display: flex;
    flex-direction: column;
    padding-top: 20px;
    font-family: Arial, sans-serif;
  }

  .sidebar a {
    color: white;
    text-decoration: none;
    padding: 15px 20px;
    display: block;
    transition: background-color 0.3s;
  }

  .sidebar a:hover {
    background-color: #34495e;
  }

  .sidebar .logo {
    font-size: 1.5em;
    font-weight: bold;
    text-align: center;
    margin-bottom: 30px;
  }

  /* Content margin to avoid overlap */
  .content {
    margin-left: 220px;
    padding: 20px;
  }

  /* Responsive for small screens */
  @media (max-width: 600px) {
    .sidebar {
      width: 100%;
      height: auto;
      position: relative;
      flex-direction: row;
      padding: 0;
    }

    .sidebar a {
      flex: 1;
      text-align: center;
      padding: 10px 0;
      border-right: 1px solid #34495e;
    }

    .sidebar a:last-child {
      border-right: none;
    }

    .content {
      margin-left: 0;
      margin-top: 60px;
    }
  }
</style>

<div class="sidebar">
  <div class="logo">Mon Réseau</div>
  <a href="accueil.php">Accueil</a>
  <a href="profil.php">Profil</a>
  <a href="explorer.php">Explorer</a>
  <a href="amis.php">Amis</a>
  <a href="chats.php">Chats</a>
  <a href="deconnexion.php">Déconnexion</a>
</div>
