<?php
require_once('../src/model/gallery_model.php');
require_once('image_uploader.php'); // Inclure la classe ImageUploader pour réutilisation

// Chemin des images pour la galerie sélectionnée
require_once('../config/config.php'); // Charge les constantes et chemins globaux
$galleryName = $_POST['gallery_name'] ?? null;

if (!$galleryName) {
    echo "Erreur : aucun nom de galerie fourni.";
    exit;
}

$originalPath = $repImg . 'galleries/' . $galleryName . '/original';
$thumbsPath = $repImg . 'galleries/' . $galleryName . '/thumbs';

try {
    // Instancier le modèle de galerie pour obtenir les images
    $gallery = new Model_gallery($originalPath, 'image/jpeg'); // On peut adapter le type MIME si nécessaire
    $images = $gallery->getImages();

    // Créer le répertoire 'thumbs' s'il n'existe pas
    if (!is_dir($thumbsPath)) {
        mkdir($thumbsPath, 0777, true);
    }

    // Parcourir les images pour générer les miniatures
    foreach ($images as $image) {
        $originalFile = $originalPath . '/' . $image['name'];
        $thumbFile = $thumbsPath . '/' . $image['name'];

        // Vérifier si la miniature existe déjà
        if (file_exists($thumbFile)) {
            continue; // Passer à l'image suivante
        }

        // Redimensionner l'image
        $uploader = new ImageUploader($thumbsPath); // Réutilisation de la classe ImageUploader
        $uploader->resizeToWidth($originalFile, $thumbFile, 400);
    }

    echo "Miniatures générées avec succès pour la galerie '{$galleryName}'.";
} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage();
}
