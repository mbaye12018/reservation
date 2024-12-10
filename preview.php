<?php
session_start();
if (isset($_SESSION['qr_code_path'])):
    $qrCodePath = $_SESSION['qr_code_path'];
    // Récupérer les données soumises depuis la session ou les stocker à partir du formulaire
    $firstname = $_SESSION['firstname'] ?? 'Non renseigné';
    $lastname = $_SESSION['lastname'] ?? 'Non renseigné';
    $phone = $_SESSION['phone'] ?? 'Non renseigné';
    $email = $_SESSION['email'] ?? 'Non renseigné';
    $role = $_SESSION['role'] ?? 'Non renseigné';
    unset($_SESSION['qr_code_path']); // Effacer après affichage pour éviter la répétition
    unset($_SESSION['firstname'], $_SESSION['lastname'], $_SESSION['phone'], $_SESSION['email'], $_SESSION['role']);
?>
    <div class="modal fade" id="modalQrCodePreview" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content" style="min-height: 500px;">
                <div class="modal-header" style="text-align: center;">
                    <h4>Gov'awards 2024</h4>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>

                <div class="modal-body" style="background: linear-gradient(to bottom, #4caf50, #81c784); padding: 20px; text-align: center;">
                    <!-- Logo centré -->
                    <img src="images/govawards-logo.png" alt="Logo Gov'awards" style="width: 70px; height: auto; display: inline-block; margin-bottom: 10px;">
                    <h3 style="color: white; font-weight: bold;">Gov'awards 2024</h3>

                    <!-- Informations du badge avec alignement à gauche des labels et valeurs -->
                    <div style="text-align: left; padding-top: 20px; color: white;">
                        <h5><span style="font-weight: bold;">Prénom et Nom :</span> <?php echo $firstname . ' ' . $lastname; ?></h5>
                        <p><span style="font-weight: bold;">Téléphone :</span> <?php echo $phone; ?></p>
                        <p><span style="font-weight: bold;">Email :</span> <?php echo $email; ?></p>
                        <p><span style="font-weight: bold;">Fonction :</span> <?php echo $role; ?></p>
                    </div>

                    <!-- QR Code et bouton de téléchargement -->
                    <h4 style="color: white; margin-top: 20px;">Badge QR Code généré :</h4>
                    <img src="<?php echo $qrCodePath; ?>" alt="QR Code" style="width: 100px; height: 100px;"><br>
                    <a href="<?php echo $qrCodePath; ?>" download="badge_qrcode.png" class="btn btn-primary" style="margin-top: 10px;">Télécharger votre QR Code</a>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>























<?php
session_start();

// Configuration de la base de données
$host = "91.234.195.179";
$username = "c2275612c_gov_athon";
$password = "Passer@2024";
$dbname = "c2275612c_govathon2024";

$dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4"; // Utilisation de utf8mb4

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("SET NAMES 'utf8mb4'"); // Encodage UTF-8

    // Récupération des projets finalistes
    $stmt = $pdo->prepare("SELECT * FROM Projets_Finalistes ORDER BY secteur");
    $stmt->execute();

} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vote du Public - Projets Finalistes</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #111;
            color: #fff;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .navbar {
            background-color: #222;
        }
        .navbar-brand img {
            max-height: 80px;
        }
        .nav-link {
            color: #fff !important;
        }
        .header {
            text-align: center;
            margin-top: 50px;
            margin-bottom: 30px;
            padding: 20px;
            color: white;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            background-color: #222;
        }
        .header h2 {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 10px;
            font-family: 'Roboto', sans-serif;
        }
        .intro-text {
            font-size: 1.2rem;
            font-style: italic;
            margin-bottom: 15px;
            font-family: 'Arial', sans-serif;
        }
        .edition {
            font-size: 2.1rem;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 8px 20px;
            border-radius: 25px;
            display: inline-block;
            margin-top: 20px;
            color: white;
        }
        .table-container {
            background-color: #222;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 40px;
        }
        .table {
            background-color: #333;
            color: #fff;
        }
        .table th, .table td {
            text-align: center;
            padding: 12px;
        }
        .table th {
            background-color: black;
            color:#fff;
        }
         .table tr {
            background-color: black;
            color:#fff;
        }
        .table tbody tr:nth-child(even) {
            background-color: #555;
        }
        .table tbody tr:nth-child(odd) {
            background-color: #444;
        }
        .vote-button {
            background-color: #dedede;
            color: black;
            border: none;
            padding: 8px;
            border-radius: 5px;
            font-size: 1.2rem;
            cursor: pointer;
        }
        
        .vote-button:hover {
            background-color: #438327;
            color : white;
        }
        .video-button {
            background-color: #555;
            color: white;
            border: none;
            padding: 8px;
            border-radius: 5px;
            font-size: 1.2rem;
            cursor: pointer;
        }
        .video-button:hover {
            background-color: #dbc237;
        }
        
        .video-button {
            background-color: #db5037 !important;
        }
        .success-message {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%)
            background-color: #28a745;
            color: white;
            border-radius: 10px;
            padding: 10px 20px;
            font-size: 1rem;
            text-align: center;
            z-index: 9999;
            display: none;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }
        .success-message .icon {
            font-size: 1.5rem;
        }
        /* Responsive design */
        @media (max-width: 768px) {
            .header h2 {
                font-size: 2rem;
            }
            .intro-text {
                font-size: 1rem;
            }
            .edition {
                font-size: 1rem;
            }
        }
        .main-content {
            flex-grow: 1; /* Pousse le footer en bas */
        }
        .entete{
            color : white !important;
        }
        footer {
            text-align: center;
            padding: 20px 0;
            background-color: #222;
            color: #fff;
            width: 100%;
        }
        .th
        {
           background-color:;
            width: 100%;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand sticky-top px-4 py-0">
        <a href="index.html" class="navbar-brand mx-auto d-flex justify-content-center align-items-center">
            <img src="../img/PHOTO-2024-07-17-23-26-00-removebg-preview (1).png" alt="Logo">
        </a>
    </nav>

    <!-- Header -->
    <div class="header">
        <h1 class="edition">Finale Gov'athon - Edition 2024</h1>
        <!-- <h5>Vote du Public</h5>-->
        <p class="intro-text">Exprimez votre opinion ! Découvrez les projets et votez pour votre projet favoris.</p>
        <a href="resultats_vote.php">
            <button class="btn btn-succes" style="font-size: 16px; padding: -4px 10px;background-color:##155a0a;">
                Tendance des votes
            </button>
        </a>
    </div>

    <!-- Table des Projets -->
    <div class="container-fluid pt-4 px-4 main-content">
        <div class="table-container">
            <div class="table-responsive">
                <table class="table text-start align-middle table-bordered table-hover mb-0">
                    <thead>
                        <tr class="entete">
                            <th scope="col">Nom Projet</th>
                            <th scope="col">Thème</th>
                            <th scope="col">Établissement</th>
                           
                            <th scope="col">Vidéo pitch</th>
                            <th scope="col">Mon choix</th>
                        </tr>
                    </thead>
                    <tbody id="projects-table">
                        <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['nom_projet'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($row['theme'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($row['etablissement'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td>
                                    <?php if (!empty($row['pitch'])): ?>
                                        <a href="<?= htmlspecialchars($row['pitch'], ENT_QUOTES, 'UTF-8') ?>" target="_blank">
                                            <i class="bi bi-play-circle video-button" title="Voir la vidéo"></i>
                                        </a>
                                    <?php else: ?>
                                        <span>Pas de vidéo</span>
                                    <?php endif; ?>
                                </td>
                               
                                <td>
                                    <button class="vote-button btn btn-link" 
                                            onclick="handleVote(<?= $row['id'] ?>, '<?= htmlspecialchars($row['nom_projet'], ENT_QUOTES, 'UTF-8') ?>')">
                                        <i class="bi bi-hand-thumbs-up" title="Je vote" style="font-size: 20px;"></i> 
                                    </button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal pour le vote avec vérification OTP -->
    <div class="modal fade" id="voteModal" tabindex="-1" aria-labelledby="voteModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content bg-dark text-white">
          <div class="modal-header">
            <h5 class="modal-title" id="voteModalLabel">Vérification de Vote</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
          </div>
          <div class="modal-body">
            <form id="vote-form">
              <div class="mb-3">
                <label for="phone-number" class="form-label">Numéro de téléphone</label>
                <input type="tel" class="form-control" id="phone-number" placeholder="Entrez votre numéro de téléphone" required>
                <div class="form-text">Format sénégalais recommandé (ex: +221612345678, 00221612345678, 0612345678). Autres formats acceptés.</div>
              </div>
              <div class="mb-3 d-none" id="otp-section">
                <label for="otp" class="form-label">Code OTP</label>
                <input type="text" class="form-control" id="otp" pattern="^[0-9]{6}$" placeholder="Entrez le code OTP" required>
                <div class="form-text">Entrez le code que vous avez reçu par SMS.</div>
              </div>
              <input type="hidden" id="project-id">
              <input type="hidden" id="project-name">
            </form>
            <div id="vote-error" class="text-danger mt-2"></div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            <button type="button" class="btn btn-primary" id="send-otp-button">Envoyer OTP</button>
            <button type="button" class="btn btn-success d-none" id="verify-otp-button">Vérifier OTP</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Message de succès -->
    <div id="success-message" class="success-message">
        <span class="icon"><i class="bi bi-check-circle"></i></span>
        Bravo, vous avez choisi le projet <span id="selected-project"></span> !
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Initialiser le modal Bootstrap
        const voteModal = new bootstrap.Modal(document.getElementById('voteModal'));

        let selectedProjectId = null;
        let selectedProjectName = null;

        function handleVote(projectId, projectName) {
            selectedProjectId = projectId;
            selectedProjectName = projectName;
            document.getElementById('vote-error').innerText = '';
            document.getElementById('phone-number').value = '';
            document.getElementById('otp').value = '';
            document.getElementById('otp-section').classList.add('d-none');
            document.getElementById('send-otp-button').classList.remove('d-none');
            document.getElementById('verify-otp-button').classList.add('d-none');
            document.getElementById('project-id').value = projectId;
            document.getElementById('project-name').value = projectName;
            voteModal.show();
        }

        document.getElementById('send-otp-button').addEventListener('click', () => {
            const phoneNumber = document.getElementById('phone-number').value.trim();
            const senegalPatterns = [
                /^\+221[6-9]\d{8}$/,        // +221612345678
                /^002216[6-9]\d{8}$/,        // 00221612345678
                /^0[6-9]\d{8}$/               // 0612345678
            ];

            let isSenegalNumber = false;
            let isValidSenegalNumber = false;

            // Vérifier si le numéro correspond à un format sénégalais
            for (let pattern of senegalPatterns) {
                if (pattern.test(phoneNumber)) {
                    isSenegalNumber = true;
                    isValidSenegalNumber = true;
                    break;
                }
            }

            if (isSenegalNumber && !isValidSenegalNumber) {
                document.getElementById('vote-error').innerText = 'Veuillez entrer un numéro de téléphone sénégalais valide.';
                return;
            }

            if (!isSenegalNumber) {
                // Pour les autres pays, on accepte tout format, mais on peut ajouter une validation basique
                // Exemple : vérifier qu'il contient au moins 7 chiffres
                const generalPattern = /^\+?[0-9\s\-]{7,}$/;
                if (!generalPattern.test(phoneNumber)) {
                    document.getElementById('vote-error').innerText = 'Veuillez entrer un numéro de téléphone valide.';
                    return;
                }
            }

            // Envoyer le numéro de téléphone au serveur pour envoyer l'OTP
            fetch('send_otp.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ phone_number: phoneNumber, project_id: selectedProjectId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('vote-error').innerText = 'OTP envoyé avec succès.';
                    document.getElementById('otp-section').classList.remove('d-none');
                    document.getElementById('send-otp-button').classList.add('d-none');
                    document.getElementById('verify-otp-button').classList.remove('d-none');
                } else {
                    document.getElementById('vote-error').innerText = data.message || 'Erreur lors de l\'envoi de l\'OTP.';
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                document.getElementById('vote-error').innerText = 'Une erreur s\'est produite. Veuillez réessayer.';
            });
        });

        document.getElementById('verify-otp-button').addEventListener('click', () => {
            const otp = document.getElementById('otp').value.trim();
            if (!/^[0-9]{6}$/.test(otp)) {
                document.getElementById('vote-error').innerText = 'Veuillez entrer un OTP valide à 6 chiffres.';
                return;
            }

            // Vérifier l'OTP auprès du serveur
            fetch('verify_otp.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ otp: otp, project_id: selectedProjectId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    voteModal.hide();
                    showSuccessMessage(selectedProjectName);
                    disableVoting();
                } else {
                    document.getElementById('vote-error').innerText = data.message || 'OTP invalide ou expiré.';
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                document.getElementById('vote-error').innerText = 'Une erreur s\'est produite. Veuillez réessayer.';
            });
        });

        function showSuccessMessage(projectName) {
            const message = document.getElementById('success-message');
            document.getElementById('selected-project').innerText = projectName;
            message.style.display = 'flex';
            setTimeout(() => {
                message.style.display = 'none';
            }, 3000);
        }

        function disableVoting() {
            // Désactiver tous les boutons de vote après un vote réussi
            document.querySelectorAll('.vote-button').forEach(btn => {
                btn.disabled = true;
                btn.style.color = 'gray';
                btn.title = "Vous avez déjà voté.";
            });
        }

        // Optionnel : Vous pouvez vérifier côté serveur si l'utilisateur a déjà voté et désactiver les boutons en conséquence
    </script>

    <!-- Footer -->
    <footer>
        &copy; <?= date("Y") ?> Finale Gov'athon. Tous droits réservés.
    </footer>
</body>
</html>








<a href="resultats_vote.php">
            <button class="btn btn-succes" style="font-size: 16px; padding: 10px 20px;background-color:##155a0a;">
                Tendance des votes
            </button>
        </a>