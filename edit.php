<?php
// On démarre une session
session_start();

if ($_POST) {
    if (
        isset($_POST['id']) && !empty($_POST['id'])
        && isset($_POST['nom']) && !empty($_POST['nom'])
        && isset($_POST['prenom']) && !empty($_POST['prenom'])
        // && isset($_POST['noteHG']) && !empty($_POST['noteHG'])
        // && isset($_POST['noteM']) && !empty($_POST['noteM'])
    ) {
        // On inclut la connexion à la bdd
        require_once 'connect.php';

        // On nettoie les données envoyées
        $id = strip_tags($_POST['id']);
        $nom = strip_tags($_POST['nom']);
        $prenom = strip_tags($_POST['prenom']);
        $noteHG = strip_tags($_POST['noteHG']);
        $noteM = strip_tags($_POST['noteM']);

        $sql = 'UPDATE `etudiants` SET nom = :nom, prenom = :prenom WHERE id_etudiant = :id;';

        $query = $db->prepare($sql);

        $query->bindValue(':id', $id, PDO::PARAM_INT);
        $query->bindValue(':nom', $nom, PDO::PARAM_STR);
        $query->bindValue(':prenom', $prenom, PDO::PARAM_STR);

        $query->execute();

        $sql2 = 'UPDATE `examens` SET note = :noteHG WHERE id_examen = 45 AND id_etudiant = :id;';
        $query = $db->prepare($sql2);
        $query->bindValue(':id', $id, PDO::PARAM_INT);
        $query->bindValue(':noteHG', $noteHG, PDO::PARAM_STR);
        $query->execute();

        $sql3 = 'UPDATE `examens` SET note = :noteM WHERE id_examen = 87 AND id_etudiant = :id;';
        $query = $db->prepare($sql3);
        $query->bindValue(':id', $id, PDO::PARAM_INT);
        $query->bindValue(':noteM', $noteM, PDO::PARAM_STR);
        $query->execute();

        $_SESSION['message'] = "Etudiant modifié";

        require_once 'close.php';

        header('Location: index.php');
    } else {
        $_SESSION['erreur'] = "Le formulaire est incomplet";
    }
}

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

    // On récupère le résultat
    $result = $query->fetchAll(PDO::FETCH_ASSOC);

    // On vérifie si le produit existe
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
    <title>Modifier un étudiant</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">
</head>

<body>
    <main class="container">
        <div class="row">
            <section class="col-12">
                <?php
                if (!empty($_SESSION['erreur'])) {
                    echo '<div class="alert alert-danger" role="alert">' . $_SESSION['erreur'] . '</div>';
                    $_SESSION['erreur'] = "";
                }
                ?>
                <h1>Modifier un étudiant</h1>
                <form method="post">
                    <div class="form-group">
                        <label for="nom">Nom</label>
                        <input type="text" id="nom" name="nom" class="form-control" value="<?= $result[0]['nom'] ?>">
                    </div>
                    <br>
                    <div class="form-group">
                        <label for="prenom">Prénom</label>
                        <input type="text" id="prenom" name="prenom" class="form-control" value="<?= $result[0]['prenom'] ?>">
                    </div>
                    <br>
                    
                    <?php
                    foreach ($result as $exam) {
                        if ($exam['id_examen'] == 87) {

                    ?>
                            <div class="form-group">
                                <label for="noteM">Note en Mathématiques</label>
                                <input type="number" min="0" max="20" step="0.5" id="noteM" name="noteM" class="form-control" value="<?= $exam['note'] ?>">
                            </div>
                            <br>
                        <?php
                        } else if ($exam['id_examen'] == 45) {
                        ?>
                            <div class="form-group">
                                <label for="noteHG">Note en Histoire-Géographie</label>
                                <input type="number" min="0" max="20" step="0.5" id="noteHG" name="noteHG" class="form-control" value="<?= $exam['note'] ?>">
                            </div>
                            <br>
                    <?php
                        }
                    }
                    ?>
                    <input type="hidden" name="id" value="<?= $result[0]['id_etudiant'] ?>">
                    <button class="btn btn-primary">Envoyer</button>
                </form>

            </section>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-A3rJD856KowSb7dwlZdYEkO39Gagi7vIsF0jrRAoQmDKKtQBHUuLZ9AsSv4jD4Xa" crossorigin="anonymous"></script>
</body>

</html>