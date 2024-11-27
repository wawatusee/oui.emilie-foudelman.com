<?php class ImageUploader
{
	private $uploadDir;
	private $imageName;
	private $imageFormat;
	private $width; // Déclaration de la propriété width
	private $height; // Déclaration de la propriété height

	public function __construct($uploadDir, $imageName, $imageFormat)
	{
		$this->uploadDir = $uploadDir;
		$this->imageName = $this->sanitizeFileName($imageName);
		$this->imageFormat = $imageFormat;
	}

	// Méthode pour valider et redimensionner
	public function uploadOriginal($file)
	{
		$this->upload($file, 1280); // taille max de l’original
	}

	public function createThumbnail($filePath)
	{
		$this->resizeImage($filePath, 250); // taille des thumbs
	}

	public function upload($file)
	{
		if (!isset($file) || $file['error'] != 0) {
			throw new Exception("Invalid file upload");
		}

		$fileInfo = getimagesize($file['tmp_name']);
		if ($fileInfo === false) {
			throw new Exception("Invalid image file");
		}

		$imageType = $fileInfo[2];
		if (!in_array($imageType, [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF])) {
			throw new Exception("Unsupported image format");
		}

		// Créer le répertoire de réception s'il n'existe pas
		if (!is_dir($this->uploadDir)) {
			if (!mkdir($this->uploadDir, 0777, true)) {
				throw new Exception("Failed to create upload directory");
			}
		}

		// Définir le chemin du fichier cible
		$targetFile = $this->uploadDir . '/' . $this->imageName . '.' . $this->imageFormat;

		// Déplacer le fichier téléchargé vers le répertoire cible
		if (move_uploaded_file($file['tmp_name'], $targetFile)) {
			// Appeler resizeImage après avoir déplacé le fichier
			$this->resizeImage($targetFile, $imageType);
			return true;
		} else {
			throw new Exception("Failed to move uploaded file");
		}
	}


	private function resizeImage($filePath, $imageType) {
		// Charger l'image selon son type
		switch ($imageType) {
			case IMAGETYPE_JPEG:
				$image = imagecreatefromjpeg($filePath);
				break;
			case IMAGETYPE_PNG:
				$image = imagecreatefrompng($filePath);
				break;
			case IMAGETYPE_GIF:
				$image = imagecreatefromgif($filePath);
				break;
			default:
				throw new Exception("Unsupported image format");
		}
	
		// Obtenir les dimensions originales de l'image
		$origWidth = imagesx($image);
		$origHeight = imagesy($image);
		
		// Vérifier les dimensions
		if ($origWidth <= 0 || $origHeight <= 0) {
			throw new Exception("Invalid image dimensions: Width or Height is zero");
		}
	
		// Affiche les dimensions pour déboguer
		error_log("Original Width: $origWidth, Original Height: $origHeight");
	
		$aspectRatio = $origWidth / $origHeight;
	
		// Calculer les nouvelles dimensions en conservant le ratio d'aspect
		if ($this->width && !$this->height) {
			// Redimensionner en fonction de la largeur tout en conservant le ratio d'aspect
			$this->height = intval($this->width / $aspectRatio);
		} elseif ($this->height && !$this->width) {
			// Redimensionner en fonction de la hauteur tout en conservant le ratio d'aspect
			$this->width = intval($this->height * $aspectRatio);
		} else {
			// Si les deux sont définis, on calcule l'échelle la plus restrictive
			if ($this->width / $this->height > $aspectRatio) {
				$this->width = intval($this->height * $aspectRatio);
			} else {
				$this->height = intval($this->width / $aspectRatio);
			}
		}
	
		$newImage = imagecreatetruecolor($this->width, $this->height);
		imagecopyresampled($newImage, $image, 0, 0, 0, 0, $this->width, $this->height, $origWidth, $origHeight);
	
		// Sauvegarder l'image redimensionnée
		switch ($imageType) {
			case IMAGETYPE_JPEG:
				imagejpeg($newImage, $filePath);
				break;
			case IMAGETYPE_PNG:
				imagepng($newImage, $filePath);
				break;
			case IMAGETYPE_GIF:
				imagegif($newImage, $filePath);
				break;
		}
	
		// Libération de la mémoire
		imagedestroy($image);
		imagedestroy($newImage);
	}
	
	
	private function sanitizeFileName($fileName)
	{
		return preg_replace('/[^A-Za-z0-9_\-]/', '_', $fileName);
	}
}
