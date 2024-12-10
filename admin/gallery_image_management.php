<?php
require_once("../config/config.php");
?>
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérifie si la variable "gallery-name" existe dans la requête POST
    if (isset($_POST['galleryName']) && !empty($_POST['galleryName'])) {
        // Récupère et sécurise la valeur
        $galleryName = htmlspecialchars($_POST['galleryName']);
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
    <link rel="stylesheet" href="css/admin.css">
    <title>Gestion des Images de galerie</title>
</head>

<body>
    <header>
        <h1>Gestion des Images de la galerie: <?= $galleryName ?></h1>
        <p></p>
    </header>
    <main>
        <section class="form-contener">
            <!-- Sélection des fichiers à uploader -->
            <input type="file" id="fileInput" multiple />
            <button onclick="uploadImages()">Upload</button>
            <!--Fin de Sélection des fichiers à uploader -->
        </section>
        <section class="form-contener">
            <!-- Rafraîchir les miniatures -->
            <button id="refreshThumbsBtn">Rafraîchir les miniatures</button>
            <!--Fin de Rafraîchir les miniatures -->
        </section>
    </main>
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
    <script>
        document.getElementById('refreshThumbsBtn').addEventListener('click', () => {
            const galleryName = '<?= htmlspecialchars($galleryName, ENT_QUOTES) ?>';
            fetch('refresh_gallery_thumbs.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        galleryName
                    }),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                    } else {
                        alert("Erreur : " + data.error);
                    }
                })
                .catch(error => {
                    alert("Erreur lors de l'appel AJAX : " + error.message);
                });
        });
    </script>
</body>

</html>