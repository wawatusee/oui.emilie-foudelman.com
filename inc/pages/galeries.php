<h2 class="invisible-titre">Galeries</h2>
<?php
// Inclure les modèles et vues
require_once('../src/model/model_galleries_choices.php');
require_once('../src/view/view_galleries_choices.php');

// Récupérer les données des galeries
$galleriesDatas = new ModelGalleryChoices('img/content/galleries/');
$galleryChoices = $galleriesDatas->getGalleryChoices(); // Récupérer le tableau des galeries

// Récupérer la variable 'page' à partir de l'URL (ou définir une valeur par défaut)
$page = isset($_GET['page']) ? htmlspecialchars($_GET['page']) : 1;

// Instancier ViewGalleryChoices avec les deux arguments : les choix et la page courante
$multiChoicesComponant = new ViewGalleryChoices($galleryChoices, $page);

// Afficher le rendu
echo $multiChoicesComponant->render();

// Définir le nom de la galerie
$galleryName = !empty($galleryChoices) ? $galleryChoices[0] : null; // Par défaut, prendre le premier élément du tableau

// Vérifier si $_GET["gallery"] est renseigné et valide
if (isset($_GET["gallery"]) && in_array($_GET["gallery"], $galleryChoices)) {
    $galleryName = htmlspecialchars($_GET["gallery"]); // Utiliser la valeur de $_GET si elle est valide
}
//Titre galerie
//echo '<h3>'.$galleryName.'</h3>'; // Affiche le nom de la galerie sélectionnée

require_once('../src/model/gallery_model.php');
require_once("../src/view/gallery_view.php");
$cheminImages = $repImg . 'galleries/' . $galleryName . '/original';


try {
    // Instancie le modèle pour obtenir les images
    $gallery = new Model_gallery($cheminImages, 'image/jpeg');
    $images = $gallery->getImages();
//Important, en deuxième paramètre de l'instance de View_gallery, le nom du dossier à traiter $galleryName
    // Crée la vue avec la classe View_gallery
    $view = new View_gallery($images,$galleryName );
    echo $view->render(); // Affiche la galerie
} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage();
}
?>
<!-- /container -->
<script src="js/imagesloaded.pkgd.min.js"></script>
<script src="js/masonry.pkgd.min.js"></script>
<script src="js/classie.js"></script>
<script src="js/main.js"></script>
<script>
    (function() {
        // create SVG circle overlay and append it to the preview element
        function createCircleOverlay(previewEl) {
            var dummy = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
            dummy.setAttributeNS(null, 'version', '1.1');
            dummy.setAttributeNS(null, 'width', '100%');
            dummy.setAttributeNS(null, 'height', '100%');
            dummy.setAttributeNS(null, 'class', 'overlay');
            var g = document.createElementNS('http://www.w3.org/2000/svg', 'g');
            var circle = document.createElementNS("http://www.w3.org/2000/svg", "circle");
            circle.setAttributeNS(null, 'cx', 0);
            circle.setAttributeNS(null, 'cy', 0);
            circle.setAttributeNS(null, 'r', Math.sqrt(Math.pow(previewEl.offsetWidth, 2) + Math.pow(previewEl.offsetHeight, 2)));
            dummy.appendChild(g);
            g.appendChild(circle);
            previewEl.appendChild(dummy);
        }

        new GridFx(document.querySelector('.grid'), {
            onInit: function(instance) {
                createCircleOverlay(instance.previewEl);
            },
            onResize: function(instance) {
                instance.previewEl.querySelector('svg circle').setAttributeNS(null, 'r', Math.sqrt(Math.pow(instance.previewEl.offsetWidth, 2) + Math.pow(instance.previewEl.offsetHeight, 2)));
            },
            onOpenItem: function(instance, item) {
                // item's image
                var gridImg = item.querySelector('img'),
                    gridImgOffset = gridImg.getBoundingClientRect(),
                    win = {
                        width: document.documentElement.clientWidth,
                        height: window.innerHeight
                    },
                    SVGCircleGroupEl = instance.previewEl.querySelector('svg > g'),
                    SVGCircleEl = SVGCircleGroupEl.querySelector('circle');

                SVGCircleEl.setAttributeNS(null, 'r', Math.sqrt(Math.pow(instance.previewEl.offsetWidth, 2) + Math.pow(instance.previewEl.offsetHeight, 2)));
                // set the transform for the SVG g node. This will animate the circle overlay. The origin of the circle depends on the position of the clicked item.
                if (gridImgOffset.left + gridImg.offsetWidth / 2 < win.width / 2) {
                    SVGCircleGroupEl.setAttributeNS(null, 'transform', 'translate(' + win.width + ', ' + (gridImgOffset.top + gridImg.offsetHeight / 2 < win.height / 2 ? win.height : 0) + ')');
                } else {
                    SVGCircleGroupEl.setAttributeNS(null, 'transform', 'translate(0, ' + (gridImgOffset.top + gridImg.offsetHeight / 2 < win.height / 2 ? win.height : 0) + ')');
                }
            }
        });
    })();
</script>