<?php

class GalleryManager
{
    private $baseDir;
    private $defaultImageWidth = 1280; // Largeur par défaut des grandes images
    private $defaultThumbWidth = 400;  // Largeur par défaut des miniatures

    public function __construct($baseDir)
    {
        $this->baseDir = rtrim($baseDir, '/') . '/';
    }


    // Crée une galerie avec dossiers "original" et "thumbs"
    public function createGallery($name)
    {
        $galleryDir = $this->baseDir . $this->formatGalleryName($name);

        if (!is_dir($galleryDir)) {
            mkdir($galleryDir . '/original', 0777, true);
            mkdir($galleryDir . '/thumbs', 0777, true);
        } else {
            throw new Exception("La galerie existe déjà.");
        }
    }

    // Renomme une galerie
    public function renameGallery($oldName, $newName)
    {
        $oldDir = $this->baseDir . $this->formatGalleryName($oldName);
        $newDir = $this->baseDir . $this->formatGalleryName($newName);

        if (is_dir($oldDir) && !is_dir($newDir)) {
            rename($oldDir, $newDir);
        } else {
            throw new Exception("Impossible de renommer la galerie.");
        }
    }

    // Supprime une galerie et son contenu
    public function deleteGallery($name)
    {
        $galleryDir = $this->baseDir . $this->formatGalleryName($name);

        if (is_dir($galleryDir)) {
            $this->deleteDirectory($galleryDir);
        } else {
            throw new Exception("Galerie introuvable.");
        }
    }
    // Permet de modifier dynamiquement la taille des grandes images
    public function setDefaultImageWidth($width)
    {
        $this->defaultImageWidth = $width;
    }

    // Permet de modifier dynamiquement la taille des miniatures
    public function setDefaultThumbWidth($width)
    {
        $this->defaultThumbWidth = $width;
    }

    // Upload une image dans "original" et crée une miniature dans "thumbs"
    /* public function uploadImage($galleryName, $file)
    {
        $galleryPath  = $this->baseDir . $this->formatGalleryName($galleryName);
        $thumbsPath = $galleryPath . '/thumbs';
        $originalPath = $galleryPath . '/original';

        $imageUploader = new ImageUploader($originalPath, pathinfo($file['name'], PATHINFO_FILENAME), pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!$imageUploader->upload($file)) {
            throw new Exception("Failed to upload image");
        }

        // Resize the original image
        $originalFilePath = $originalPath . '/' . $imageUploader->getImageName();
        $this->resizeImage($originalFilePath, $this->defaultImageWidth); // Redimensionnement selon la largeur par défaut

        // Resize and move to thumbnails
        if (!is_dir($thumbsPath)) {
            mkdir($thumbsPath, 0777, true);
        }
        $thumbFilePath = $thumbsPath . '/' . $imageUploader->getImageName();
        $this->resizeImage($originalFilePath, $this->defaultThumbWidth, $thumbFilePath);
    }*/
    public function uploadImage($galleryName, $file)
    {
        // Initialisation de ImageUploader avec les paramètres appropriés
        $imageUploader = new ImageUploader(
            $this->baseDir . '/' . $galleryName,
            $_FILES['image_upload']['name'],
            pathinfo($_FILES['image_upload']['name'], PATHINFO_EXTENSION)
        );

        // Vérification du fichier et upload
        if ($imageUploader->upload($file)) {
            echo "Image uploadée avec succès dans la galerie '$galleryName'.";

            // Appel à la méthode de redimensionnement (c'est ici qu'on gère la taille)
            $targetFile = $this->baseDir . '/' . $galleryName . '/' . $imageUploader->getImageName() . '.' . $imageUploader->getImageFormat();

            // Redimensionner l'image en fonction de la logique métier (par exemple, largeur max 1280px)
            $this->resizeImageForGallery($galleryName, $targetFile, 1280, 1280); // Redimensionner à 1280px

            // Créer les miniatures
            $thumbsDir = $this->baseDir . '/' . $galleryName . '/thumbs';
            if (!is_dir($thumbsDir)) {
                mkdir($thumbsDir, 0777, true);  // Créer le dossier thumbs si nécessaire
            }

            // Créer la vignette de l'image
            $imageUploader->createThumbnail($targetFile);
            echo "Vignette créée avec succès.";
        } else {
            echo "Erreur lors de l'upload de l'image.";
        }
    }

    // Supprime une image dans "original" et "thumbs"
    public function deleteImage($galleryName, $imageName)
    {
        $galleryDir = $this->baseDir . $this->formatGalleryName($galleryName);

        $originalPath = $galleryDir . '/original/' . $imageName;
        $thumbPath = $galleryDir . '/thumbs/' . $imageName;

        if (file_exists($originalPath)) unlink($originalPath);
        if (file_exists($thumbPath)) unlink($thumbPath);
    }
    // Méthode privée dans GalleryManager pour gérer le redimensionnement
    private function resizeImageForGallery($galleryName, $filePath, $maxWidth, $maxHeight)
    {
        $imageUploader = new ImageUploader(
            $this->baseDir . '/' . $galleryName,
            basename($filePath, '.' . pathinfo($filePath, PATHINFO_EXTENSION)),
            pathinfo($filePath, PATHINFO_EXTENSION)
        );

        // Ici on appelle la méthode de redimensionnement avec les tailles max
        $imageUploader->resizeImage($filePath, $maxWidth, $maxHeight);
    }
    private function resizeImage($filePath, $targetWidth, $outputPath = null)
    {
        $fileInfo = getimagesize($filePath);
        if (!$fileInfo) {
            throw new Exception("Invalid image file");
        }

        $imageType = $fileInfo[2];
        $imageUploader = new ImageUploader(dirname($filePath), basename($filePath), image_type_to_extension($imageType, false));

        // Appelle resizeImage avec des dimensions calculées
        $imageUploader->resizeImage($filePath, $targetWidth, $outputPath);
    }

    // Formate le nom du dossier de la galerie
    private function formatGalleryName($name)
    {
        return strtoupper(str_replace(' ', '-', $name));
    }

    // Supprime un dossier et son contenu
    private function deleteDirectory($dir)
    {
        $files = array_diff(scandir($dir), array('.', '..'));
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? $this->deleteDirectory("$dir/$file") : unlink("$dir/$file");
        }
        rmdir($dir);
    }
}
