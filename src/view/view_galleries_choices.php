<?php
class ViewGalleryChoices {
    private $galleryChoices;
    private $page;

    public function __construct(array $galleryChoices, string $page) {
        $this->galleryChoices = $galleryChoices;
        $this->page = $page;
    }

    public function render() {
        if (empty($this->galleryChoices)) {
            echo '<p>Aucune galerie disponible.</p>';
            return;
        }

        echo '<form action="" method="GET" id="galleryForm">';
        echo '<select name="gallery" id="gallery" onchange="this.form.submit()">';

        // Option d'instruction par défaut
        // echo '<option value="" disabled selected>Choisissez une galerie</option>';
        
        foreach ($this->galleryChoices as $choice) {
            echo '<option value="' . htmlspecialchars($choice) . '">' . htmlspecialchars($choice) . '</option>';
        }

        echo '</select>';
        
        // Champ caché pour la page
        echo '<input type="hidden" name="page" value="' . htmlspecialchars($this->page) . '">';
        echo '</form>';
    }
}
?>