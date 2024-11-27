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
	// Getter pour obtenir le nom de l'image sans l'extension
	public function getImageName()
	{
		return $this->imageName;
	}
	// Getter pour obtenir l'extension de l'image
	public function getImageFormat()
	{
		return $this->imageFormat;
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
		if (!isset($file) || $file['error'] != UPLOAD_ERR_OK) {
			throw new Exception("Invalid file upload: " . $this->getFileUploadErrorMessage($file['error']));
		}


		// Validation et initialisation des informations de l'image
		$fileInfo = getimagesize($file['tmp_name']);
		if ($fileInfo === false) {
			throw new Exception("Invalid image file");
		}

		$imageType = $fileInfo[2];
		if (!in_array($imageType, [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF])) {
			throw new Exception("Unsupported image format");
		}

		// Définir les dimensions de l'image via la méthode setImageDimensions
		$this->setImageDimensions($fileInfo);

		// Créer le répertoire de destination
		if (!is_dir($this->uploadDir)) {
			if (!mkdir($this->uploadDir, 0777, true)) {
				throw new Exception("Failed to create upload directory");
			}
		}

		$targetFile = $this->uploadDir . '/' . $this->imageName . '.' . $this->imageFormat;

		if (move_uploaded_file($file['tmp_name'], $targetFile)) {
			// Redimensionner l'image
			$this->resizeImage($targetFile, $imageType);
			return true;
		} else {
			throw new Exception("Failed to move uploaded file");
		}
	}


	public function copyToThumbs($originalFilePath, $thumbsDir)
	{
		// Assurer que le répertoire de thumbs existe
		if (!is_dir($thumbsDir)) {
			mkdir($thumbsDir, 0777, true);
		}

		// Définir le chemin de la vignette
		$thumbFilePath = $thumbsDir . '/' . basename($originalFilePath);

		// Copie le fichier original vers le répertoire des vignettes
		if (copy($originalFilePath, $thumbFilePath)) {
			return true;
		} else {
			throw new Exception("Erreur lors de la copie de l'image vers le dossier des vignettes.");
		}
	}



	private function resizeImage($filePath, $imageType)
	{
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
	// Setter pour définir les dimensions de l'image à partir des infos du fichier
	private function setImageDimensions($fileInfo)
	{
		$this->width = $fileInfo[0]; // Largeur
		$this->height = $fileInfo[1]; // Hauteur

		// Vérifier les dimensions
		if ($this->width <= 0 || $this->height <= 0) {
			throw new Exception("Invalid image dimensions: Width or Height is zero or negative");
		}
	}


	private function sanitizeFileName($fileName)
	{
		return preg_replace('/[^A-Za-z0-9_\-]/', '_', $fileName);
	}
	private function getFileUploadErrorMessage($errorCode)
	{
		$errors = [
			UPLOAD_ERR_OK => 'There is no error, the file uploaded successfully.',
			UPLOAD_ERR_INI_SIZE => 'The uploaded file exceeds the upload_max_filesize directive in php.ini.',
			UPLOAD_ERR_FORM_SIZE => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.',
			UPLOAD_ERR_PARTIAL => 'The uploaded file was only partially uploaded.',
			UPLOAD_ERR_NO_FILE => 'No file was uploaded.',
			UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder.',
			UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
			UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload.'
		];

		return $errors[$errorCode] ?? 'Unknown upload error.';
	}
}
