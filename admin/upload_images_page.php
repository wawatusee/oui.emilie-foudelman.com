<?php
require_once("../config/config.php");

?>
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérifie si la variable "gallery-name" existe dans la requête POST
    if (isset($_POST['galleryName']) && !empty($_POST['galleryName'])) {
        // Récupère et sécurise la valeur
        $galleryName = htmlspecialchars($_POST['galleryName']);

        // Affiche ou utilise le nom de la galerie
        echo "Nom de la galerie reçu : " . $galleryName;
    } else {
        echo "Aucune galerie sélectionnée.";
    }
} else {
    echo "Accès non autorisé.";
}
$repgalleries = $repImg . 'galleries/' . $galleryName . '/original';
?>
<!DOCTYPE html>
<html>

<head>
    <title>Upload Images</title>
</head>

<body>
    <header>
        <h1>Upload d'images</h1>
        <p></p>
    </header>
    <!-- Sélection des fichiers -->
    <input type="file" id="fileInput" multiple />
    <button onclick="uploadImages()">Upload</button>
    <!--Rafraichit les miniatures-->
    <form action="refresh_gallery_thumbs.php" method="POST">
        <input type="hidden" name="galleryName" value="<?$galleryName?>">
        <button type="submit">Rafraîchir les miniatures</button>
    </form>
    <script>
        function uploadImages() {
            const fileInput = document.getElementById('fileInput');
            if (fileInput.files.length === 0) {
                alert("Please select image files");
                return;
            }

            const files = fileInput.files;
            const formData = new FormData();

            // Ajout des fichiers au formulaire
            for (let i = 0; i < files.length; i++) {
                formData.append('images[]', files[i]);
            }

            // Définition des paramètres pour l'upload
            //formData.append('uploadDir', '../public/img/content/galleries/marcel/original');
            formData.append('uploadDir', '<?= $repgalleries ?>');
            formData.append('width', 400); // Exemple de largeur
            formData.append('height', 600); // Exemple de hauteur
            formData.append('imageFormat', 'jpg');

            // Vérification de la largeur dans la console
            console.log("Chemin upload: ", formData.get('uploadDir'));
            console.log("Width sent:", formData.get('width'));
            fetch('upload.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok ' + response.statusText);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        alert("Images uploaded successfully");
                    } else {
                        alert("Failed to upload images: " + data.error);
                    }
                })
                .catch(error => {
                    alert("Error uploading images: " + error);
                });
        }
    </script>
</body>

</html>