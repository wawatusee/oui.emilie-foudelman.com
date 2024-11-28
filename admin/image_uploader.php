<?php class ImageUploader
{
    private $targetDir;      // Répertoire cible pour enregistrer l'image redimensionnée
    private $imageFile;      // Le fichier image
    private $targetFormat;   // Format de fichier pour l'image cible (ex: jpg, png, gif)
    private $imageInfo;      // Informations sur l'image (format, largeur, hauteur)
    private $resizeOptions;  // Options de redimensionnement (facultatif)

    public function __construct($targetDir, $imageFile, $targetFormat, $resizeOptions = [])
    {
        $this->targetDir = rtrim($targetDir, '/');
        $this->imageFile = $imageFile;
        $this->targetFormat = strtolower($targetFormat);
        $this->resizeOptions = $resizeOptions;
        $this->imageInfo = getimagesize($imageFile);

        if (!$this->imageInfo) {
            throw new Exception("Impossible de récupérer les informations de l'image.");
        }
    }

    // Méthode pour obtenir les informations de l'image
    private function getImageInfo()
    {
        return [
            'width' => $this->imageInfo[0],
            'height' => $this->imageInfo[1],
            'type' => $this->imageInfo['mime'],
            'extension' => image_type_to_extension($this->imageInfo[2], false) // Extension sans 'image/'
        ];
    }

    // Calcul du ratio de l'image
    private function calculateAspectRatio($originalWidth, $originalHeight)
    {
        return $originalWidth / $originalHeight;
    }

    // Déterminer les dimensions cibles en fonction des options fournies
    private function getTargetDimensions($originalWidth, $originalHeight)
    {
        $ratio = $this->calculateAspectRatio($originalWidth, $originalHeight);

        // Si des options de redimensionnement sont fournies
        if (isset($this->resizeOptions['largeur'])) {
            $newWidth = $this->resizeOptions['largeur'];
            $newHeight = round($newWidth / $ratio);
        } elseif (isset($this->resizeOptions['hauteur'])) {
            $newHeight = $this->resizeOptions['hauteur'];
            $newWidth = round($newHeight * $ratio);
        } else {
            // Si aucune option fournie, conserver les dimensions d'origine
            $newWidth = $originalWidth;
            $newHeight = $originalHeight;
        }

        return [$newWidth, $newHeight];
    }

    // Redimensionner et enregistrer l'image
    private function resize()
    {
        // Obtenir les informations sur l'image
        $imageInfo = $this->getImageInfo();
        $originalWidth = $imageInfo['width'];
        $originalHeight = $imageInfo['height'];

        // Calculer les dimensions cibles
        [$newWidth, $newHeight] = $this->getTargetDimensions($originalWidth, $originalHeight);

        // Charger l'image selon son type
        switch ($imageInfo['extension']) {
            case 'jpeg':
            case 'jpg':
                $image = imagecreatefromjpeg($this->imageFile);
                break;
            case 'png':
                $image = imagecreatefrompng($this->imageFile);
                break;
            case 'gif':
                $image = imagecreatefromgif($this->imageFile);
                break;
            default:
                throw new Exception("Format d'image non supporté.");
        }

        // Créer une nouvelle image vide avec les dimensions cibles
        $newImage = imagecreatetruecolor($newWidth, $newHeight);

        // Redimensionner l'image
        imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);

        // Créer le répertoire cible s'il n'existe pas
        if (!is_dir($this->targetDir)) {
            mkdir($this->targetDir, 0777, true);
        }

        // Sauvegarder l'image redimensionnée dans le format souhaité
        $newFileName = $this->targetDir . '/' . basename($this->imageFile, '.' . $imageInfo['extension']) . '.' . $this->targetFormat;
        switch ($this->targetFormat) {
            case 'jpg':
            case 'jpeg':
                imagejpeg($newImage, $newFileName, 90); // Qualité à 90% pour les JPG
                break;
            case 'png':
                imagepng($newImage, $newFileName); // PNG sans compression
                break;
            case 'gif':
                imagegif($newImage, $newFileName);
                break;
            default:
                throw new Exception("Format de sauvegarde non supporté.");
        }

        // Libérer la mémoire
        imagedestroy($image);
        imagedestroy($newImage);

        return $newFileName; // Retourner le chemin du fichier redimensionné
    }

    // Méthode publique pour enregistrer le fichier (avec ou sans redimensionnement)
    public function save()
    {
        return $this->resize(); // Appelle la méthode privée de redimensionnement
    }
}
