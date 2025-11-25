<?php session_start();

include('../../traitement/fonction.php');

verif_type_mdp_2($_SESSION['username']);

$pavillonDonne = $_SESSION['pavillon'];
$result = getPaymentDetailsByPavillon1($pavillonDonne, $connexion);
$expediteur = $pavillonDonne; 
$destinataire = getCampusByPavillon($connexion, $pavillonDonne);

if (isset($_GET['alert']) && $_GET['alert'] == 'success') {
    echo "<script type='text/javascript'>alert('Rappel envoyé avec succès!');</script>";
}
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['etudiant_id'])) {
    $etudiant_id = intval($_GET['etudiant_id']); // Sanitize the input
    
    // Appeler la fonction de rappel
    sms_recouvrement($etudiant_id, $pavillonDonne);	
	
    rappel("Rappel envoyé avec succès pour l'étudiant ID: $etudiant_id", $etudiant_id, $connexion);
    
    // Ajouter un message de confirmation (utilisation de session ou redirection)
    $message = "Rappel envoyé avec succès pour l'étudiant ID: $etudiant_id";

    // Rediriger vers la même page sans les paramètres GET
    header("Location: " . strtok($_SERVER["REQUEST_URI"], '?') . "?success=true");
    exit();
}




 //include('../../head.php'); ?>


<script>
function confirmRappel(form) {
    const confirmation = confirm(
        "Le SMS de rappel ne peut etre envoyé qu'une seule fois par mois.  Etes-vous sûr de vouloir envoyer un SMS de rappel à cet étudiant ?"
    );
    if (confirmation) {
        return true; // Permet de soumettre le formulaire
    } else {
        return false; // Empêche la soumission du formulaire
    }
}
</script>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COUD: CODIFICATION</title>
    <!-- CSS================================================== -->
    <link rel="stylesheet" href="../../assets/css/main.css">
    <!-- script================================================== -->
    <script src="../../assets/js/modernizr.js"></script>
    <script src="../../assets/js/pace.min.js"></script>
    <link rel="stylesheet" href="../../assets/css/styles.css">
    <link rel="stylesheet" href="../../assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../assets/bootstrap/js/bootstrap.min.js">
    <link rel="stylesheet" href="../../assets/bootstrap/js/bootstrap.bundle.min.js">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
    #floatingBtn {
        position: fixed;
        bottom: 25px;
        right: 25px;
        background-color: #0d6efd;
        color: white;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 26px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
        cursor: pointer;
        z-index: 1050;
        transition: transform 0.3s ease, background-color 0.3s ease;
    }

    #floatingBtn:hover {
        background-color: #0056b3;
        transform: scale(1.1);
    }

    .chat-box {
        height: 400px;
        overflow-y: auto;
        background: #f9f9f9;
        display: flex;
        flex-direction: column;
        gap: 10px;
        padding: 15px;
    }

    .message {
        max-width: 75%;
        padding: 10px 15px;
        border-radius: 15px;
        position: relative;
        display: inline-block;
    }

    .message.received {
        background-color: #e4e6eb;
        align-self: flex-start;
        border-bottom-left-radius: 0;
    }

    .message.sent {
        background-color: #0d6efd;
        color: white;
        align-self: flex-end;
        border-bottom-right-radius: 0;
    }

    .message-time {
        display: block;
        font-size: 0.8em;
        opacity: 0.7;
        margin-top: 3px;
        text-align: right;
    }

    #messageInput {
        border-radius: 20px;
        padding: 10px 15px;
    }

    #sendBtn {
        border-radius: 50%;
        width: 45px;
        height: 45px;
    }

    .chat-box {
        max-height: 400px;
        overflow-y: auto;
    }

    .modal-footer {
        position: sticky;
        bottom: 0;
        background: white;
    }
    </style>

    <?php include('../../head.php'); ?>
</head>

<body>
    <!--header class="s-header">
        <div class="header-logo">
            <a class="site-logo" href="#"><img src="/campuscoud.com/assets/images/logo.png" alt="Homepage" /></a>
            CAMPUSCOUD
        </div>
		
		<?php //if (($_SESSION['profil'] == 'chef_residence')) { ?>
		<nav class="header-nav-wrap">
      <ul class="header-nav">
          <li class="nav-item active">
            <a class="nav-link" href="recouvr" title="Suivi recouvrement">Recouvrement</a>
          </li>
		  <li class="nav-item active">
            <a class="nav-link" href="pavillon" title="Voir occupants">Pavillon</a>
          </li>
		  <li class="nav-item active">
            <a class="nav-link" href="loger" title="Loger etudiant">Loger_un_etudiant</a>
          </li>
		  <li class="nav-item">
          <a class="nav-link" href="/campuscoud.com/" title="Déconnexion"><i class="fa fa-sign-out" aria-hidden="true"></i> Déconnexion</a>
        </li>
		        </ul>
    </nav>
        <?php //} ?>
		
    </header-->
    <!--section id="homedesigne" class="s-homedesigne">
        <p class="lead">Bienvenue dans l'espace de connexion !</p>
    </section-->
    <div class="container-fluid">
        <center>
            <br> <u>
                <h2>SUIVI DES RECOUVREMENTS DU PAVILLON <?= htmlspecialchars($pavillonDonne) ?></h2>
            </u><br>
        </center>
        <center>
            <table class="table">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Chambre</th>
                        <th scope="col">Lit</th>
                        <th scope="col">Carte Etudiant</th>
                        <th scope="col">Prenom et Nom</th>
                        <th scope="col">Total Facturé</th>
                        <th scope="col">Total Payé</th>
                        <th scope="col">Montant Dû</th>
                        <th scope="col">SMS de Rappel</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $counter = 1;
                    $currentChambre = null;
                    $litCount = 0;
                    $chambreRows = [];

                    foreach ($result as $row):
                        if ($currentChambre !== $row['chambre']):
                            if ($currentChambre !== null):
                                ?>
                    <tr>
                        <th scope="row" rowspan="<?= $litCount ?>"><?= $counter ?></th>
                        <td rowspan="<?= $litCount ?>"><?= htmlspecialchars($currentChambre) ?></td>
                        <?php foreach ($chambreRows as $i => $litRow): ?>
                        <?php 
                                        // Vérification du statut du rappel pour chaque étudiant dans la ligne
                                        $resteAPayer = (int)$litRow['reste_a_payer_total'];
                                        $canRemind = false;

                                        // Vérification du montant restant à payer et de la date du dernier rappel
                                        if ($resteAPayer >= 6000) {
                                            if (!empty($litRow['rappel_envoye'])) {
                                                $lastReminderDate = new DateTime($litRow['rappel_envoye']);
                                                $currentDate = new DateTime();
                                                $interval = $lastReminderDate->diff($currentDate);

                                                // Si le dernier rappel a plus de 1 mois, autoriser le rappel
                                                if ($interval->m >= 1) {
                                                    $canRemind = true;
                                                }
                                            } else {
                                                $canRemind = true;  // Si aucun rappel n'a été envoyé
                                            }
                                        }
                                        ?>
                        <?php if ($i > 0): ?>
                    <tr>
                        <?php endif; ?>
                        <td><?= htmlspecialchars($litRow['lit']) ?></td>
                        <td><?= htmlspecialchars($litRow['num_etu']) ?></td>
                        <td><?= htmlspecialchars($litRow['etudiant_prenoms'] . " " . $litRow['etudiant_nom']) ?></td>
                        <td><?= number_format($litRow['montant_facture_total'], 0, ',', ' ') ?> F CFA</td>

                        <td>
                            <a
                                href="details.php?id_etu=<?= urlencode($litRow['etudiant_id']) ?>&etu=<?= urlencode($litRow['num_etu']) ?>">
                                <?= number_format($litRow['montant_paye_total'], 0, ',', ' ') ?> F CFA
                            </a>
                        </td>

                        <td><?= number_format($litRow['reste_a_payer_total'], 0, ',', ' ') ?> F CFA</td>
                        <td>
                            <form method="GET" action="" onsubmit="return confirmRappel(this);">
                                <input type="hidden" name="etudiant_id" value="<?= $litRow['etudiant_id'] ?>">
                                <button type="submit" class="btn <?= $canRemind ? 'btn-success' : 'btn-secondary' ?>"
                                    <?= $canRemind ? '' : 'disabled' ?>>ENVOYER</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php
                                $counter++;
                            endif;

                            $currentChambre = $row['chambre'];
                            $litCount = 1;
                            $chambreRows = [$row];
                        else:
                            $litCount++;
                            $chambreRows[] = $row;
                        endif;
                    endforeach;

                    if ($currentChambre !== null):
                        ?>
                    <tr>
                        <th scope="row" rowspan="<?= $litCount ?>"><?= $counter ?></th>
                        <td rowspan="<?= $litCount ?>"><?= htmlspecialchars($currentChambre) ?></td>
                        <?php foreach ($chambreRows as $i => $litRow): ?>
                        <?php 
                                // Vérification du statut du rappel pour chaque étudiant dans la ligne
                                $resteAPayer = (int)$litRow['reste_a_payer_total'];
                                $canRemind = false;

                                if ($resteAPayer >= 6000) {
                                    if (!empty($litRow['rappel_envoye'])) {
                                        $lastReminderDate = new DateTime($litRow['rappel_envoye']);
                                        $currentDate = new DateTime();
                                        $interval = $lastReminderDate->diff($currentDate);

                                        if ($interval->m >= 1) {
                                            $canRemind = true;
                                        }
                                    } else {
                                        $canRemind = true; // Si aucun rappel n'a été envoyé
                                    }
                                }
                                ?>
                        <?php if ($i > 0): ?>
                    <tr>
                        <?php endif; ?>
                        <td><?= htmlspecialchars($litRow['lit']) ?></td>
                        <td><?= htmlspecialchars($litRow['num_etu']) ?></td>
                        <td><?= htmlspecialchars($litRow['etudiant_prenoms'] . " " . $litRow['etudiant_nom']) ?></td>
                        <td><?= number_format($litRow['montant_facture_total'], 0, ',', ' ') ?> F CFA</td>
                        <td>
                            <a
                                href="details.php?id_etu=<?= urlencode($litRow['etudiant_id']) ?>&etu=<?= urlencode($litRow['num_etu']) ?>">
                                <?= number_format($litRow['montant_paye_total'], 0, ',', ' ') ?> F CFA
                            </a>
                        </td>
                        <td><?= number_format($litRow['reste_a_payer_total'], 0, ',', ' ') ?> F CFA</td>
                        <td>
                            <form method="GET" action="" onsubmit="return confirmRappel(this);">
                                <input type="hidden" name="etudiant_id" value="<?= $litRow['etudiant_id'] ?>">
                                <button type="submit" class="btn <?= $canRemind ? 'btn-success' : 'btn-secondary' ?>"
                                    <?= $canRemind ? '' : 'disabled' ?>>ENVOYER</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </center>
    </div>

    <!-- Bouton flottant -->
    <div id="floatingBtn" data-bs-toggle="modal" data-bs-target="#infoModal">
        <i class="fas fa-comments">

        </i>
        <span id="unreadCount" style="position: absolute; top: -5px; right: -5px; 
             background: white; color: black; border-radius: 50%; 
             padding: 3px 6px; font-size: 12px;">
            0
        </span>

    </div>

    <!-- Modal Messagerie -->
    <div class="modal fade" id="infoModal" tabindex="-1" aria-labelledby="infoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="infoModalLabel">💬 Instructions du chef de service</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Fermer"></button>
                </div>

                <div class="modal-body p-0">
                    <div id="chatBox" class="chat-box"></div>
                </div>

                <div class="modal-footer d-flex align-items-center">
                    <input type="text" id="messageInput" class="form-control me-2" placeholder="Écrivez un message...">
                    <button id="sendBtn" class="btn btn-primary"><i class="fas fa-paper-plane"></i></button>
                </div>
            </div>
        </div>
    </div>
    <?php //include('footer.php'); ?>
    <script src="../../assets/js/script.js"></script>
    <script src="../../assets/js/jquery-3.2.1.min.js"></script>
    <script src="../../assets/js/plugins.js"></script>
    <script src="../../assets/js/main.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    $(function() {
        const destinataire = "<?= $destinataire ?>";
        const expediteur = "<?= $expediteur ?>";
        let lastMessageCount = 0;
        let refreshInterval = null;
        const POLL_MS = 3000; // fréquence de rafraîchissement

        // 🔹 Fonction pour charger les messages + badge
        function loadMessages(scrollToBottom = false, fetchMessages = true) {
            $.get('getMessages.php', {
                destinataire
            }, function(data) {
                const result = JSON.parse(data || "{}");
                const messages = result.messages || [];
                const unreadCount = result.unreadCount ?? 0;

                // ✅ Met à jour le badge (toujours visible)
                const badge = $('#unreadCount');
                badge.text(unreadCount);
                badge.show(); // s'assure que le badge reste affiché

                // 🟢 Si on veut juste le badge, on s'arrête ici
                if (!fetchMessages) return;

                // 🔹 Afficher les messages si besoin
                if (messages.length !== lastMessageCount) {
                    $('#chatBox').empty();

                    messages.forEach(msg => {
                        const time = new Date(msg.date_envoi).toLocaleTimeString([], {
                            hour: '2-digit',
                            minute: '2-digit'
                        });
                        const classe = msg.expediteur === expediteur ? 'sent' : 'received';

                        $('#chatBox').append(`
                        <div class="message ${classe}">
                            <div class="message-content">${msg.message}</div>
                            <small class="message-time">${time}</small>
                        </div>
                    `);

                        // 🔹 Marquer comme lu les messages reçus
                        if (msg.destinataire === expediteur && msg.lu == 0) {
                            $.post('markAsRead.php', {
                                id: msg.id
                            });
                        }
                    });

                    if (scrollToBottom || messages.length > lastMessageCount) {
                        $('#chatBox').scrollTop($('#chatBox')[0].scrollHeight);
                    }

                    lastMessageCount = messages.length;
                }
            });
        }

        // 🔹 Envoi du message
        function sendMessage() {
            const message = $('#messageInput').val().trim();
            if (message === '') return;

            $('#sendBtn').prop('disabled', true);

            $.post('sendMessage.php', {
                message,
                destinataire
            }, function(resp) {
                if (resp.trim() === 'success') {
                    $('#messageInput').val('');

                    // petit délai pour MySQL
                    setTimeout(() => {
                        loadMessages(true);
                        $('#sendBtn').prop('disabled', false);
                    }, 300);
                } else {
                    alert("❌ Erreur lors de l'envoi du message.");
                    $('#sendBtn').prop('disabled', false);
                }
            }).fail(() => {
                alert("⚠️ Une erreur réseau est survenue.");
                $('#sendBtn').prop('disabled', false);
            });
        }

        // 🔹 Événements
        $('#sendBtn').click(sendMessage);
        $('#messageInput').keypress(e => {
            if (e.which === 13) {
                e.preventDefault();
                sendMessage();
            }
        });

        // 🔹 Polling permanent pour le badge
        function startBackgroundPolling() {
            if (refreshInterval) return;
            loadMessages(false, false); // juste le badge
            refreshInterval = setInterval(() => loadMessages(false, false), POLL_MS);
        }

        function stopBackgroundPolling() {
            if (!refreshInterval) return;
            clearInterval(refreshInterval);
            refreshInterval = null;
        }

        // 🔹 Gestion de la modale
        $('#infoModal').on('shown.bs.modal', function() {
            stopBackgroundPolling(); // évite conflit
            loadMessages(true); // afficher le contenu
            refreshInterval = setInterval(() => loadMessages(), POLL_MS);
        });

        $('#infoModal').on('hidden.bs.modal', function() {
            clearInterval(refreshInterval);
            startBackgroundPolling(); // reprend le badge seulement
        });

        // ✅ Démarrer la mise à jour du badge dès le chargement
        startBackgroundPolling();
    });
    </script>
</body>

</html>