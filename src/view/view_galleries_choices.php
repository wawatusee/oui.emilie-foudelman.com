<?php
class ViewGalleryChoices {
    private $galleryChoices;
    private $page;

    public function __construct($galleryChoices, $page) {
        $this->galleryChoices = $galleryChoices;
        $this->page = $page;
    }

    public function render() {
        if (!empty($this->galleryChoices)) {
            echo '<form action="" method="GET" id="galleryForm">';
            //echo '<label for="gallery">Choisir une galerie :</label>';
            echo '<select name="gallery" id="gallery" onchange="submitGalleryForm()">';
    
            // Option d'instruction par défaut
            echo '<option value="" disabled selected>Choisissez une galerie</option>';
    
            foreach ($this->galleryChoices as $choice) {
                echo '<option value="' . htmlspecialchars($choice) . '">' . htmlspecialchars($choice) . '</option>';
            }
    
            echo '</select>';
            
            // Champ caché pour la page
            echo '<input type="hidden" name="page" value="' . htmlspecialchars($this->page) . '">';
            echo '</form>';
        } else {
            echo '<p>Aucune galerie disponible.</p>';
        }
    
        // Ajouter le script JavaScript pour soumettre le formulaire automatiquement
        echo <<<EOD
        <script>
            function submitGalleryForm() {
                document.getElementById('galleryForm').submit();
            }
        </script>
        EOD;
    }
    
}
?>
