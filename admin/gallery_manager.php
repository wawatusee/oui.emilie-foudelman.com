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
