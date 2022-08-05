<?php
// On démarre une session
session_start();

// On inclut la connexion à la bdd
require_once 'connect.php';

// On détermine sur quelle page on se trouve
if (isset($_GET['page']) && !empty($_GET['page'])) {
    $currentPage = (int) strip_tags($_GET['page']);
} else {
    $currentPage = 1;
}

// On détermine le nombre total d'étudiants
$sql = 'SELECT COUNT(*) AS nb_etudiants FROM `etudiants`;';

// On prépare la requête
$query = $db->prepare($sql);

// On exécute
$query->execute();

// On récupère le nombre d'étudiants
$result = $query->fetch();

$nbStudents = (int) $result['nb_etudiants'];

// On détermine le nombre d'articles par page
$parPage = 6;

// On calcule le nombre de pages total
$pages = ceil($nbStudents / $parPage);

// Calcul du 1er article de la page
$premier = ($currentPage * $parPage) - $parPage;

$sql = 'SELECT * FROM `etudiants` LIMIT :premier, :parpage;';


// On prépare la requête
$query = $db->prepare($sql);

$query->bindValue(':premier', $premier, PDO::PARAM_INT);
$query->bindValue(':parpage', $parPage, PDO::PARAM_INT);

// On exécute la requête
$query->execute();

// On stocke le résultat dans un tableau associatif
$result = $query->fetchAll(PDO::FETCH_ASSOC);

$q = "";
if (isset($_GET['q']) and !empty($_GET['q'])) {
    $q = htmlspecialchars($_GET['q']);

    // On détermine sur quelle page on se trouve
    if (isset($_GET['page']) && !empty($_GET['page'])) {
        $currentPage = (int) strip_tags($_GET['page']);
    } else {
        $currentPage = 1;
    }

    // On détermine le nombre total d'étudiants
    $sql = 'SELECT COUNT(*) AS nb_etudiants FROM `etudiants` WHERE nom LIKE "%' . $q . '%" OR prenom LIKE "%' . $q . '%";';

    // On prépare la requête
    $query = $db->prepare($sql);

    // On exécute
    $query->execute();

    // On récupère le nombre d'étudiants
    $result = $query->fetch();

    $nbStudents = (int) $result['nb_etudiants'];

    // On détermine le nombre d'articles par page
    $parPage = 6;

    // On calcule le nombre de pages total
    $pages = ceil($nbStudents / $parPage);

    // Calcul du 1er article de la page

    $sql = 'SELECT * FROM `etudiants` WHERE nom LIKE "%' . $q . '%" OR prenom LIKE "%' . $q . '%" LIMIT :premier, :parpage;';

    // On prépare la requête
    $query = $db->prepare($sql);

    $query->bindValue(':premier', $premier, PDO::PARAM_INT);
    $query->bindValue(':parpage', $parPage, PDO::PARAM_INT);

    // On exécute la requête
    $query->execute();

    // On stocke le résultat dans un tableau associatif
    $result = $query->fetchAll(PDO::FETCH_ASSOC);
}

require_once 'close.php';
?>
<!doctype html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Liste des étudiants</title>
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
                <?php
                if (!empty($_SESSION['message'])) {
                    echo '<div class="alert alert-success" role="alert">' . $_SESSION['message'] . '</div>';
                    $_SESSION['message'] = "";
                }
                ?>

                <h1>Liste des étudiants</h1>

                <form method="get">
                    <input type="search" class="form-control" name="q" id="" placeholder=" Rechercher par nom ou prénom">
                    <!-- <input type="submit" value="Valider"> -->
                </form>

                <?php if (count($result) > 0) {
                ?>

                    <table class="table">
                        <thead>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Actions</th>
                        </thead>

                        <tbody>
                            <?php
                            // On boucle sur la variable result
                            foreach ($result as $student) {
                            ?>
                                <tr>
                                    <td><?= $student['id_etudiant'] ?></td>
                                    <td><?= $student['nom'] ?></td>
                                    <td><?= $student['prenom'] ?></td>
                                    <td><a href="details.php?id=<?= $student['id_etudiant'] ?>" class="btn btn-info">Voir notes</a> <a href="edit.php?id=<?= $student['id_etudiant'] ?>" class="btn btn-warning ms-3 me-3">Modifier</a> <a href="delete.php?id=<?= $student['id_etudiant'] ?>" class="btn btn-danger">Supprimer l'étudiant</a></td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>

                    </table>
                    
                    <nav>
                        <ul class="pagination">
                            <!-- Lien vers la page précédente (désactivé si on se trouve sur la 1ère page) -->
                            <li class="page-item <?= ($currentPage == 1) ? "disabled" : "" ?>">
                                <a href="./?q=<?= $q = isset($_GET['q']) ? $q : "" ?>&page=<?= $currentPage - 1 ?>" class="page-link">Précédente</a>
                            </li>
                            <?php for ($page = 1; $page <= $pages; $page++) : ?>
                                <!-- Lien vers chacune des pages (activé si on se trouve sur la page correspondante) -->
                                <li class="page-item <?= ($currentPage == $page) ? "active" : "" ?>">
                                    <a href="./?q=<?= $q = isset($_GET['q']) ? $q : "" ?>&page=<?= $page ?>" class="page-link"><?= $page ?></a>
                                </li>
                            <?php endfor ?>
                            <!-- Lien vers la page suivante (désactivé si on se trouve sur la dernière page) -->
                            <li class="page-item <?= ($currentPage == $pages) ? "disabled" : "" ?>">
                                <a href="./?q=<?= $q = isset($_GET['q']) ? $q : "" ?>&page=<?= $currentPage + 1 ?>" class="page-link">Suivante</a>
                            </li>
                        </ul>
                    </nav>
                <?php
                } else {
                ?>
                    <p>Pas de résultats</p>
                <?php
                }
                ?>
            </section>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-A3rJD856KowSb7dwlZdYEkO39Gagi7vIsF0jrRAoQmDKKtQBHUuLZ9AsSv4jD4Xa" crossorigin="anonymous"></script>
</body>

</html>