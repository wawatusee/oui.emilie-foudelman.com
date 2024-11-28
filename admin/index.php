<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
// index.php dans le dossier admin

require_once 'gallery_manager.php';
require_once 'image_uploader.php';

// Définir le répertoire de base pour les galeries
$baseDir = realpath(__DIR__ . '/../public/img/content/galleries/');
$galleryManager = new GalleryManager($baseDir);

// Gestion des requêtes
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['create_gallery'])) {
        $galleryName = $_POST['gallery_name'];
        try {
            $galleryManager->createGallery($galleryName);
            echo "Galerie '$galleryName' créée avec succès.";
        } catch (Exception $e) {
            echo "Erreur : " . $e->getMessage();
        }
    }

    if (isset($_POST['rename_gallery'])) {
        $oldName = $_POST['old_name'];
        $newName = $_POST['new_name'];
        try {
            $galleryManager->renameGallery($oldName, $newName);
            echo "Galerie '$oldName' renommée en '$newName'.";
        } catch (Exception $e) {
            echo "Erreur : " . $e->getMessage();
        }
    }

    if (isset($_POST['delete_gallery'])) {
        $galleryName = $_POST['gallery_name'];
        try {
            $galleryManager->deleteGallery($galleryName);
            echo "Galerie '$galleryName' supprimée avec succès.";
        } catch (Exception $e) {
            echo "Erreur : " . $e->getMessage();
        }
    }

    if (isset($_FILES['image_upload'])) {
        $galleryName = $_POST['gallery_name'];
        $file = $_FILES['image_upload'];

        try {
            // Utilisation de GalleryManager pour uploader l'image
            $galleryManager->uploadImage($galleryName, $file);

        } catch (Exception $e) {
            echo "Erreur lors de l'upload de l'image : " . $e->getMessage();
        }
    }
}

// Affichage des galeries existantes
$galleries = array_diff(scandir($baseDir), array('.', '..'));

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestionnaire de Galeries</title>
</head>
<body>
    <h1>Gestionnaire de Galeries</h1>

    <h2>Créer une galerie</h2>
    <form method="POST">
        <input type="text" name="gallery_name" required placeholder="Nom de la galerie">
        <button type="submit" name="create_gallery">Créer</button>
    </form>

    <h2>Renommer une galerie</h2>
    <form method="POST">
        <input type="text" name="old_name" required placeholder="Nom actuel de la galerie">
        <input type="text" name="new_name" required placeholder="Nouveau nom de la galerie">
        <button type="submit" name="rename_gallery">Renommer</button>
    </form>

    <h2>Supprimer une galerie</h2>
    <form method="POST">
        <input type="text" name="gallery_name" required placeholder="Nom de la galerie à supprimer">
        <button type="submit" name="delete_gallery">Supprimer</button>
    </form>

    <h2>Uploader une image dans une galerie</h2>
    <form method="POST" enctype="multipart/form-data">
        <select name="gallery_name" required>
            <?php foreach ($galleries as $gallery): ?>
                <option value="<?= htmlspecialchars($gallery) ?>"><?= htmlspecialchars($gallery) ?></option>
            <?php endforeach; ?>
        </select>
        <input type="file" name="image_upload" required>
        <button type="submit">Uploader</button>
    </form>

    <h2>Galeries existantes</h2>
    <ul>
        <?php foreach ($galleries as $gallery): ?>
            <li><?= htmlspecialchars($gallery) ?></li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
