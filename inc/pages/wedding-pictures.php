<h2>photos de mariage</h2>

<?php 
require_once("../src/model/gallery_model.php");
$cheminImages=$repImg."galleries/c-p/original";
try {
    $gallery = new Model_gallery($cheminImages, 'image/jpeg'); // On peut préciser 'image/png' par exemple
    $images = $gallery->getImages();
    print_r($images); // Affiche les informations des images trouvées
} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage();
}
?>