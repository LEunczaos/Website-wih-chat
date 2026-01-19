<?php session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code_saisi = trim($_POST['code'] ?? '');


    if (isset($_SESSION['inscription_temp'])) {
        $data = $_SESSION['inscription_temp'];
        
        if (trim($data['code']) === trim($code_saisi)) {
            include('fonctions.php');  // assure-toi que $conn est accessible ici

            insertion(
                $conn,
                $data['nom'],
                $data['prenom'],
                $data['sexe'],
                $data['datenaiss'],
                $data['lieunaiss'],
                $data['nationalite'],
                $data['etatcivil'],
                $data['telephone'],
                $data['email'],
                $data['mdp'],
                $data['photo']
            );

            // Stocker toutes les données dans la session 'connecte' (sauf le code)
$data_sans_code = $data;
unset($data_sans_code['code']);

// ✅ Ajouter l'ID généré automatiquement
$data_sans_code['id'] = $conn->lastInsertId();


$_SESSION['connecte'] = $data_sans_code;

unset($_SESSION['inscription_temp']);
header('Location: accueil.php');
exit;
        } else {
            echo "<script>alert('Code incorrect. Veuillez réessayer.'); window.history.back();</script>";
            exit;
        }
    } else {
        header('Location: formulaire.html');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Confirmation</title>
    <style>
       body {
    font-family: 'Segoe UI', sans-serif;
    background: linear-gradient(135deg, #007bff, #00b894);
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    margin: 0;
}

.container {
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
    width: 400px;
    max-width: 90%;
}

.form-container {
    padding: 30px;
}

.form h2 {
    margin-bottom: 25px;
    color: #007bff;
    text-align: center;
    font-weight: 600;
}

.code-input {
    display: flex;
    justify-content: space-between;
    margin-bottom: 25px;
}

.code-input input {
    width: 48px;
    height: 54px;
    text-align: center;
    font-size: 22px;
    border: 2px solid #00b894;
    border-radius: 8px;
    transition: border-color 0.3s ease;
}

.code-input input:focus {
    border-color: #007bff;
    outline: none;
}

.form button {
    width: 100%;
    padding: 12px;
    background: linear-gradient(to right, #00b894, #007bff);
    border: none;
    border-radius: 8px;
    color: white;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
    transition: background 0.3s ease, transform 0.2s ease;
}

.form button:hover {
    background: linear-gradient(to right, #009e7d, #006ae0);
    transform: scale(1.02);
}

.resend {
    margin-top: 18px;
    text-align: center;
}

.resend a {
    color: #007bff;
    font-size: 14px;
    text-decoration: underline;
    cursor: pointer;
}

.resend a:hover {
    color: #0056b3;
}

    </style>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <form method="POST" action="" class="form">
                <h2>Entrer le code de confirmation</h2>
                <div class="code-input">
                    <input type="text" maxlength="1" name="digit1" required>
                    <input type="text" maxlength="1" name="digit2" required>
                    <input type="text" maxlength="1" name="digit3" required>
                    <input type="text" maxlength="1" name="digit4" required>
                    <input type="text" maxlength="1" name="digit5" required>
                    <input type="text" maxlength="1" name="digit6" required>
                </div>
                <input type="hidden" name="code" id="fullCode">
                <button type="submit">Valider</button>

                <div class="resend">
                    <p>Pas reçu le code ? <a href="renvoyer_code.php">Renvoyer le code</a></p>
                </div>
            </form>
        </div>
    </div>

    <script>
        const inputs = document.querySelectorAll('.code-input input');
const hiddenInput = document.getElementById('fullCode');

inputs.forEach((input, index) => {
    input.addEventListener('input', () => {
        if (input.value.length === 1 && index < inputs.length - 1) {
            inputs[index + 1].focus();
        }
        updateHiddenInput();
    });

    input.addEventListener('keydown', (e) => {
        if (e.key === 'Backspace' && !input.value && index > 0) {
            inputs[index - 1].focus();
        }
    });
});

function updateHiddenInput() {
    hiddenInput.value = Array.from(inputs).map(i => i.value).join('');
}

// IMPORTANT : Mettre à jour le champ caché AVANT la soumission
document.querySelector('.form').addEventListener('submit', (e) => {
    updateHiddenInput();

    // Optionnel : empêcher la soumission si code incomplet
    if (hiddenInput.value.length < 6) {
        alert('Veuillez saisir les 6 chiffres du code.');
        e.preventDefault();
    }
});

    </script>
</body>
</html>

