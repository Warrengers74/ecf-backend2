<?php
// On démarre une session
session_start();

// Est-ce que l'id existe et n'est pas vide dans l'URL
if (isset($_GET['id']) && !empty($_GET['id'])) {
    require_once 'connect.php';

    // On nettoie l'id envoyé
    $id = strip_tags($_GET['id']);

    $sql = 'SELECT et.id_etudiant, et.nom, et.prenom, ex.id_examen, ex.matiere, ex.note FROM `etudiants` et INNER JOIN `examens` ex ON et.id_etudiant = ex.id_etudiant WHERE et.id_etudiant = :id';

    // On prépare la requête
    $query = $db->prepare($sql);

    // On "accroche" les paramètres (id)
    $query->bindValue(':id', $id, PDO::PARAM_INT);

    // On exécute la requête
    $query->execute();

    // On récupère le procduit
    $result = $query->fetchAll(PDO::FETCH_ASSOC);

    $sql2 = 'SELECT ROUND(AVG(ex.note), 2) AS moyenne FROM `etudiants` et INNER JOIN `examens` ex ON et.id_etudiant = ex.id_etudiant WHERE et.id_etudiant = :id';

    // On prépare la requête
    $query = $db->prepare($sql2);

    // On "accroche" les paramètres (id)
    $query->bindValue(':id', $id, PDO::PARAM_INT);

    // On exécute la requête
    $query->execute();

    // On récupère la moyenne
    $moyenne = $query->fetch();

    // On vérifie si le resulat existe
    if (!$result) {
        $_SESSION['erreur'] = "Cet id n'existe pas";
        header('Location: index.php');
    }
} else {
    $_SESSION['erreur'] = "URL invalide";
    header('Location: index.php');
}
?>
<!doctype html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Note de l'étudiant</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">
</head>

<body>
    <main class="container">
        <div class="row">
            <section class="col-12">
                <h1>Notes de l'étudiant <?= $result[0]['nom'] . ' ' . $result[0]['prenom'] ?> : </h1>
                <table class="table">
                    <thead>
                        <th>Matière</th>
                        <th>Note</th>
                        <th>Actions</th>
                    </thead>
                    <tbody>
                        <?php
                        // On boucle sur la variable result
                        foreach ($result as $exam) {
                            if ($exam['id_examen'] == 87) {

                        ?>
                                <tr>
                                    <td><?= $exam['matiere'] ?></td>
                                    <td><?= $exam['note'] ?></td>
                                    <td><a href="deleteM.php?id=<?= $exam['id_etudiant'] ?>" class="btn btn-danger">Supprimer note</a></td>
                                </tr>
                            <?php
                            } else if ($exam['id_examen'] == 45) {
                            ?>
                                <tr>
                                    <td><?= $exam['matiere'] ?></td>
                                    <td><?= $exam['note'] ?></td>
                                    <td><a href="deleteHG.php?id=<?= $exam['id_etudiant'] ?>" class="btn btn-danger">Supprimer note</a></td>
                                </tr>
                            <?php
                            }
                        }
                        ?>
                    </tbody>
                </table>
                <table class="table">
                    <thead>
                        <th>Moyenne</th>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?= $moyenne['moyenne'] ?></td>
                        </tr>
                    </tbody>
                </table>
                <p><a href="index.php" class="btn btn-primary">Retour</a> <a href="edit.php?id=<?= $result[0]['id_etudiant'] ?>" class="btn btn-warning ms-3 me-3">Modifier</a></p>
            </section>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-A3rJD856KowSb7dwlZdYEkO39Gagi7vIsF0jrRAoQmDKKtQBHUuLZ9AsSv4jD4Xa" crossorigin="anonymous"></script>
</body>

</html>