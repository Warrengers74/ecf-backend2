<?php
// On démarre une session
session_start();

// Est-ce que l'id existe et n'est pas vide dans l'URL
if (isset($_GET['id']) && !empty($_GET['id'])) {
    require_once 'connect.php';

    // On nettoie l'id envoyé
    $id = strip_tags($_GET['id']);

    $sql = 'SELECT * FROM `etudiants` et INNER JOIN `examens` ex ON et.id_etudiant = ex.id_etudiant WHERE et.id_etudiant = :id';

    // On prépare la requête
    $query = $db->prepare($sql);

    // On "accroche" les paramètres (id)
    $query->bindValue(':id', $id, PDO::PARAM_INT);

    // On exécute la requête
    $query->execute();

    // On récupère résultat
    $result = $query->fetch();

    // On vérifie si le résultat existe
    if (!$result) {
        $_SESSION['erreur'] = "Cet id n'existe pas";
        header('Location: index.php');
        die();
    }

    $sql = 'DELETE FROM `examens` WHERE id_examen = 45 AND id_etudiant = :id';

    // On prépare la requête
    $query = $db->prepare($sql);

    // On "accroche" les paramètres (id)
    $query->bindValue(':id', $id, PDO::PARAM_INT);

    // On exécute la requête
    $query->execute();
    $_SESSION['message'] = "Note supprimé";
    header('Location: index.php');

} else {
    $_SESSION['erreur'] = "URL invalide";
    header('Location: index.php');
}