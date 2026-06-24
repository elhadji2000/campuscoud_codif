<?php
include('../../traitement/fonction.php');

$niveau = $_POST['niveau'] ?? '';
$sexe = $_POST['sexe'] ?? '';
$fac = $_POST['fac'] ?? '';

$data = getEtudiantsModal($fac, $niveau, $sexe);
?>

<table id="tableModal" class="table table-bordered table-sm table-striped text-center">
    <thead class="table-primary">
        <tr>
            <th>#</th>
            <th>Matricule</th>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Statut</th>
            <th>Rang</th>
            <th>Choix</th>
            <th>Valider</th>
            <th>Payer</th>
            <th>Loger</th>
        </tr>
    </thead>
    <tbody>
        <?php $i = 1; foreach ($data as $row): ?>
        <tr>
            <td><?= $i++ ?></td>
            <td><?= htmlspecialchars($row['num_etu']) ?></td>
            <td><?= htmlspecialchars($row['nom']) ?></td>
            <td><?= htmlspecialchars($row['prenoms']) ?></td>
            <td>
                <?php
                    switch($row['statut']){
                        case 'Forclos(e)':
                            echo '<span class="text-danger fw-bold">Forclos</span>';
                            break;
                        case 'Attributaire':
                            echo '<span class="text-success fw-bold">Attributaire</span>';
                            break;
                        case 'Suppleant(e)':
                            echo '<span class="text-warning fw-bold">Suppléant</span>';
                            break;
                        default:
                            echo '<span class="text-secondary fw-bold">'.htmlspecialchars($row['statut']).'</span>';
                    }
                ?>
            </td>
            <td><?= $row['rang'] ?? '-' ?></td>
            <td><?= !empty($row['id_aff']) ? '<span class="text-success fw-bold">Oui</span>' : '<span class="text-danger fw-bold">Non</span>' ?>
            </td>
            <td><?= !empty($row['id_val']) ? '<span class="text-success fw-bold">Oui</span>' : '<span class="text-danger fw-bold">Non</span>' ?>
            </td>
            <td><?= !empty($row['id_paie']) ? '<span class="text-success fw-bold">Oui</span>' : '<span class="text-danger fw-bold">Non</span>' ?>
            </td>
            <td><?= !empty($row['id_log']) ? '<span class="text-success fw-bold">Oui</span>' : '<span class="text-danger fw-bold">Non</span>' ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<script>
$(document).ready(function() {
    $('#tableModal').DataTable({
        "pageLength": 10,
        "lengthMenu": [10, 25, 50, 100],
        "ordering": true,
        "searching": true,
        "lengthChange": true,
        "language": {
            "search": "Rechercher :",
            "lengthMenu": "Afficher _MENU_ lignes",
            "zeroRecords": "Aucune donnée trouvée",
            "info": "Affichage de _START_ à _END_ sur _TOTAL_ entrées",
            "infoEmpty": "Aucune donnée disponible",
            "paginate": {
                "next": "Suivant",
                "previous": "Précédent"
            }
        },
        "order": [
            [5, "asc"]
        ] // tri uniquement par rang
    });
});
</script>