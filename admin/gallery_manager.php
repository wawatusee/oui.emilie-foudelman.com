<?php

class GalleryManager
{
    private $baseDir;


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
    // MÉTHODES GESTION IMAGES
    public function uploadImage($galleryName, $file)
    {
        $galleryDir = $this->baseDir . $this->formatGalleryName($galleryName);
    
        // Vérifie si le dossier "original" existe
        if (!is_dir($galleryDir . '/original')) {
            throw new Exception("La galerie '$galleryName' n'existe pas ou est mal configurée.");
        }
    
        // Vérifie la validité du fichier
        if (!isset($file['tmp_name']) || !file_exists($file['tmp_name'])) {
            throw new Exception("Le fichier temporaire est introuvable ou a été supprimé.");
        }
    
        // Debug: Afficher les informations sur le fichier
        echo "Fichier reçu : " . $file['tmp_name'] . " (" . $file['type'] . ")\n";
    
        // Obtenir les dimensions et l'orientation de l'image
        $imageInfo = $this->getImageInfo($file['tmp_name']);
        if (!$imageInfo) {
            throw new Exception("Le fichier image n'est pas valide.");
        }
    
        // Debug: Afficher les informations d'image
        echo "Image dimensions : " . $imageInfo['width'] . "x" . $imageInfo['height'] . "\n";
    
        $orientation = $this->getImageOrientation($imageInfo);
    
        // Obtenir les paramètres des traitements (original, thumb, etc.)
        $processes = $this->getProcessingParameters($galleryDir, $orientation);
    
        // Parcourir chaque processus défini (original, thumb) et traiter l'image
        foreach ($processes as $process) {
            try {
                // Debug: Afficher les paramètres du processus
                echo "Traitement : " . json_encode($process) . "\n";
    
                $uploader = new ImageUploader(
                    $process['targetDir'],
                    $file,
                    $process['format'],
                    $process['resize']
                );
                $uploader->upload(); // Lance l'upload/redimensionnement
            } catch (Exception $e) {
                echo "Erreur lors du traitement : " . $e->getMessage() . "\n";
            }
        }
    
        echo "Image uploadée et traitée avec succès dans la galerie '$galleryName'.";
    }
    

    // Méthode utilitaire pour obtenir les dimensions de l'image
    private function getImageInfo($filePath)
    {
        $imageInfo = getimagesize($filePath);
        if (!$imageInfo) return false;

        return [
            'width' => $imageInfo[0],
            'height' => $imageInfo[1],
            'mime' => $imageInfo['mime']
        ];
    }

    // Méthode utilitaire pour déterminer l'orientation de l'image
    private function getImageOrientation($imageInfo)
    {
        return $imageInfo['width'] > $imageInfo['height'] ? 'landscape' : 'portrait';
    }

    // Méthode utilitaire pour définir les paramètres de traitement
    private function getProcessingParameters($galleryDir, $orientation)
    {
        $processes = [];

        // Paramètres pour la version "original"
        if ($orientation === 'portrait') {
            $processes[] = [
                'targetDir' => $galleryDir . '/original',
                'format' => 'jpg',
                'resize' => ['height' => 1280]
            ];
        } else {
            $processes[] = [
                'targetDir' => $galleryDir . '/original',
                'format' => 'jpg',
                'resize' => ['width' => 1280]
            ];
        }

        // Paramètres pour la version "thumb"
        $processes[] = [
            'targetDir' => $galleryDir . '/thumbs',
            'format' => 'jpg',
            'resize' => ['width' => 400]
        ];

        return $processes;
    }



    // Supprimer une image (inchangé)
    public function deleteImage($galleryName, $imageName)
    {
        $galleryDir = $this->baseDir . $this->formatGalleryName($galleryName);

        $originalPath = $galleryDir . '/original/' . $imageName;
        $thumbPath = $galleryDir . '/thumbs/' . $imageName;

        if (file_exists($originalPath)) unlink($originalPath);
        if (file_exists($thumbPath)) unlink($thumbPath);
    }
}
