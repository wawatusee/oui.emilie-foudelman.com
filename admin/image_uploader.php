<?php class ImageUploader
{
	private $targetDir;
	private $targetFormat;
	private $resizeOptions;
	private $originalWidth;
	private $originalHeight;
	private $originalRatio;
	private $imagePath;

	public function __construct($targetDir, $imageFile, $targetFormat, $resizeOptions = [])
	{
		$this->targetDir = rtrim($targetDir, '/');
		$this->targetFormat = $targetFormat;
		$this->resizeOptions = $resizeOptions;

		// Utiliser tmp_name pour obtenir les informations de l'image
		if (!isset($imageFile['tmp_name']) || !file_exists($imageFile['tmp_name'])) {
			throw new Exception("Le fichier image n'est pas valide.");
		}

		$this->imagePath = $imageFile['tmp_name']; // Le chemin temporaire de l'image uploadée
		$imageInfo = getimagesize($this->imagePath);

		if (!$imageInfo) {
			throw new Exception("Impossible d'obtenir les informations de l'image.");
		}

		$this->originalWidth = $imageInfo[0];
		$this->originalHeight = $imageInfo[1];
		$this->originalRatio = $this->originalWidth / $this->originalHeight;
	}

	public function upload()
	{
		try {
			$this->validateFile();

			if (!empty($this->resizeOptions)) {
				$this->resize();
			}

			$targetPath = $this->targetDir . '/' . $this->getTargetFileName();
			if (!move_uploaded_file($this->imageFile['tmp_name'], $targetPath)) {
				throw new Exception("Échec du déplacement du fichier temporaire à : $targetPath");
			}

			// Debug: Vérification finale
			echo "Fichier déplacé avec succès à : $targetPath\n";
		} catch (Exception $e) {
			throw new Exception("Erreur lors de l'upload de l'image : " . $e->getMessage());
		}
	}
	private function getTargetFileName()
	{
		// Obtenez le nom du fichier d'origine (sans extension)
		$originalName = pathinfo($this->imageFile['name'], PATHINFO_FILENAME);

		// Nettoyez le nom pour éviter les caractères spéciaux
		$sanitizedOriginalName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $originalName);

		// Ajoutez l'extension cible
		$targetFileName = $sanitizedOriginalName . '.' . $this->targetFormat;

		return $targetFileName;
	}

	private function validateFile()
	{
		// Vérifie si le fichier existe
		if (!file_exists($this->imageFile['tmp_name'])) {
			throw new Exception("Fichier introuvable : " . $this->imageFile['tmp_name']);
		}
	
		// Vérifie si c'est une image valide
		$imageInfo = getimagesize($this->imageFile['tmp_name']);
		if ($imageInfo === false) {
			throw new Exception("Le fichier n'est pas une image valide.");
		}
	
		// Vérifie le type MIME
		$validMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
		if (!in_array($imageInfo['mime'], $validMimeTypes)) {
			throw new Exception("Type MIME non supporté : " . $imageInfo['mime']);
		}
	
		// Optionnel : Vérifie la taille maximale
		$maxSize = 5 * 1024 * 1024; // 5 MB
		if ($this->imageFile['size'] > $maxSize) {
			throw new Exception("Le fichier dépasse la taille maximale autorisée (5 MB).");
		}
	
		// Si toutes les vérifications passent
		return true;
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
