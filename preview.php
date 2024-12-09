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
