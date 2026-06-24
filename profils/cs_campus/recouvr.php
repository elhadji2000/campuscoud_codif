<?php
session_start();
include ('../../traitement/fonction.php');

verif_type_mdp_2($_SESSION['username']);
$campus = $_SESSION['campus'];
$pavillons = getPavillonsByCampus2($connexion, $campus);

$pavillonDonne = isset($_GET['pavillon']) ? htmlspecialchars($_GET['pavillon']) : htmlspecialchars($pavillons[0]);
$result = getPaymentDetailsByPavillon1($pavillonDonne, $connexion);

// Regrouper les lits par chambre
$chambres = [];
foreach ($result as $row) {
    $chambres[$row['chambre']][] = $row;
}
// Exemple de destinataire dynamique (à ajuster selon ton contexte)
$destinataire = $pavillonDonne;

$pavillon_2 = $pavillonDonne;

$sql = "SELECT nom_user, prenom_user FROM codif_user WHERE pavillon = '$pavillon_2' AND var='RCR'";
$result = mysqli_query($connexion, $sql);

$listeChefs = [];

while ($row = mysqli_fetch_assoc($result)) {
    $listeChefs[] = $row['prenom_user'] . ' ' . $row['nom_user'];
}

// Convertir en une seule chaîne séparée par virgule
$nomsChefs = implode(', ', $listeChefs);

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COUD: CODIFICATION</title>

    <link rel="stylesheet" href="../../assets/css/main.css">
    <link rel="stylesheet" href="../../assets/css/styles.css">
    <link rel="stylesheet" href="../../assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../assets/css/base.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
    select.pavillon {
        width: 250px;
        height: 50px;
        font-size: 16px;
        padding: 5px;
        border-radius: 5px;
    }

    #tablePaiements td,
    #tablePaiements th {
        font-size: 12px;
    }

    #tablePaiements td a {
        text-decoration: underline !important;
        font-size: 12px !important;
    }

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
</head>
<?php include ('../../head.php'); ?>

<body>
    <div class="container-fluid" style="font-size:16px;">
        <center>
            <div class="container" style="width:50%;">
                <form method="get" action="recouvr">
                    <div class="row align-items-center justify-content-center">
                        <div class="col-5">
                            <select class="pavillon" name="pavillon" required>
                                <option value="">Sélectionnez un pavillon</option>
                                <?php foreach ($pavillons as $pavillon): ?>
                                <option value="<?= htmlspecialchars($pavillon) ?>"
                                    <?= $pavillon === $pavillonDonne ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($pavillon) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-5">
                            <button type="submit" class="btn btn-primary pavillon"><strong>Rechercher</strong></button>
                        </div>
                    </div>
                </form>
            </div>

            <br><br>
            <h1>GESTION DES RECOUVREMENTS</h1>
            <h2>PAVILLON : <?= htmlspecialchars($pavillonDonne) ?></h2>
            <h2><a href="pavillon_nonLoger?pavillon=<?= urlencode($pavillonDonne) ?>">Voir les étudiants du pavillon
                    <?= htmlspecialchars($pavillonDonne) ?> à surveiller</a></h2>
        </center>

        <br><br>

        <table id="tablePaiements" class="table table-bordered table-striped">
            <thead class="thead-dark">
                <tr>
                    <th>#</th>
                    <th>Chambre</th>
                    <th>Lit</th>
                    <th>Faculté</th>
                    <th>Num Étudiant</th>
                    <th>Nom</th>
                    <th>Montant Facturé</th>
                    <th>Montant Payé</th>
                    <th>Restant</th>
                    <th>% Réalisée</th>
                    <th>Rappel</th>
                </tr>
            </thead>
            <?php
            $totalFacture = 0;
            $totalPaye = 0;
            $totalReste = 0;
            ?>
            <tbody>
                <?php if (!empty($chambres)): ?>
                <?php $counter = 1;
                foreach ($chambres as $chambre => $lits): ?>
                <tr>
                    <th rowspan="<?= count($lits) ?>"><?= $counter++ ?></th>
                    <td rowspan="<?= count($lits) ?>"><?= htmlspecialchars($chambre) ?></td>

                    <?php foreach ($lits as $i => $litRow): ?>

                    <?php
                    $facture = ($litRow['montant_facture_total'] ?? 0);
                    $paye = $litRow['montant_paye_total'] ?? 0;

                    $totalFacture += $facture;
                    $totalPaye += $paye;
                    $totalReste += ($litRow['reste_a_payer_total'] ?? 0);

                    $pourcentage = 0;
                    if ($facture > 0) {
                        $pourcentage = round(($paye / $facture) * 100, 2);
                    }
                    ?>

                    <?php if ($i > 0): ?>
                <tr>
                    <?php endif; ?>

                    <td><?= htmlspecialchars($litRow['lit']) ?></td>
                    <td><?= htmlspecialchars($litRow['etablissement'] ?? '') ?></td>
                    <td><?= htmlspecialchars($litRow['num_etu'] ?? '') ?></td>

                    <td>
                        <?= htmlspecialchars(($litRow['etudiant_prenoms'] ?? 'NR') . ' ' . ($litRow['etudiant_nom'] ?? '')) ?>
                    </td>

                    <td><?= number_format($facture, 0, ',', ' ') ?> F</td>

                    <td>
                        <a
                            href="details.php?id_etu=<?= urlencode($litRow['etudiant_id'] ?? '') ?>&etu=<?= urlencode($litRow['num_etu'] ?? '') ?>">
                            <?= number_format($paye, 0, ',', ' ') ?> F
                            |
                            <?= $pourcentage ?>%
                        </a>
                    </td>

                    <td>
                        <?= number_format(($litRow['reste_a_payer_total'] ?? 0), 0, ',', ' ') ?> F
                    </td>

                    <td>
                        <span
                            class="badge badge-<?= $pourcentage >= 100 ? 'success' : ($pourcentage >= 50 ? 'warning' : 'danger') ?>">
                            <?= $pourcentage ?>%
                        </span>
                    </td>

                    <td>
                        <button class="btn btn-secondary" disabled>Rappel</button>
                    </td>
                </tr>

                <?php endforeach; ?>
                <?php endforeach; ?>

                <?php else: ?>
                <tr>
                    <td colspan="11">Aucun étudiant trouvé pour ce pavillon.</td>
                </tr>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <?php
                $pourcentageGlobal = $totalFacture > 0
                    ? round(($totalPaye / $totalFacture) * 100, 2)
                    : 0;
                ?>
                <tr style="font-weight:bold;background:#f8f9fa;">
                    <td colspan="6" class="text-end">
                        TOTAL GÉNÉRAL
                    </td>

                    <td>
                        <?= number_format($totalFacture, 0, ',', ' ') ?> F
                    </td>

                    <td>
                        <?= number_format($totalPaye, 0, ',', ' ') ?> F
                        |
                        <?= $pourcentageGlobal ?>%
                    </td>

                    <td>
                        <?= number_format($totalReste, 0, ',', ' ') ?> F
                    </td>

                    <td>
                        <span
                            class="badge badge-<?= $pourcentageGlobal >= 100 ? 'success' : ($pourcentageGlobal >= 50 ? 'warning' : 'danger') ?>">
                            <?= $pourcentageGlobal ?>%
                        </span>
                    </td>

                    <td></td>
                </tr>
            </tfoot>
        </table>

        <div class="text-center my-5">
            <button class="btn btn-success" onclick="window.history.back()">Retour</button>
        </div>
    </div>

    <!-- Bouton flottant -->
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
                    <h5 class="modal-title" id="infoModalLabel">
                        💬 Instructions au chef de résidence
                        <b>( <?= htmlspecialchars($nomsChefs) ?> )</b>
                    </h5>

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

    <!-- Scripts -->

    <script src="../../assets/js/script.js"></script>
    <script src="../../assets/js/jquery-3.2.1.min.js"></script>
    <script src="../../assets/js/plugins.js"></script>
    <script src="../../assets/js/main.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    $(function() {
        const destinataire = "<?= $destinataire ?>";
        const expediteur = "<?= $campus ?>";
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