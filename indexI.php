<?php
require_once 'Database.php';
require_once 'Inscriptions.php';

// Initialiser la base de données
// Vérifier la connexion à la base de données
if (!$db->connexion) {
    die("La connexion à la base de données a échoué.");
}

?>
