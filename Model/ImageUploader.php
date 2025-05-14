<?php
/**
 * ManiyaTech
 *
 * @author        Milan Maniya
 * @package       ManiyaTech_Crud
 */

namespace ManiyaTech\Crud\Model;

use Exception;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\UrlInterface;
use Magento\MediaStorage\Helper\File\Storage\Database;
use Magento\Framework\Filesystem;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Psr\Log\LoggerInterface;

class ImageUploader
{
    public const BASE_TMP_PATH = 'ManiyaTech/Profile/tmp/';
    public const BASE_PATH = 'ManiyaTech/Profile/';
    public const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'svg'];

    /**
     * @var string
     */
    public string $baseTmpPath;

    /**
     * @var string
     */
    public string $basePath;

    /**
     * @var string[]
     */
    public array $allowedExtensions;

    /**
     * @var Database
     */
    private Database $coreFileStorageDatabase;

    /**
     * @var WriteInterface
     */
    private WriteInterface $mediaDirectory;

    /**
     * @var UploaderFactory
     */
    private UploaderFactory $uploaderFactory;

    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * Constructor
     *
     * @param Database              $coreFileStorageDatabase
     * @param Filesystem            $filesystem
     * @param UploaderFactory       $uploaderFactory
     * @param StoreManagerInterface $storeManager
     * @param LoggerInterface       $logger
     */
    public function __construct(
        Database $coreFileStorageDatabase,
        Filesystem $filesystem,
        UploaderFactory $uploaderFactory,
        StoreManagerInterface $storeManager,
        LoggerInterface $logger
    ) {
        $this->coreFileStorageDatabase = $coreFileStorageDatabase;
        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->uploaderFactory = $uploaderFactory;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
        $this->baseTmpPath = self::BASE_TMP_PATH;
        $this->basePath = self::BASE_PATH;
        $this->allowedExtensions = self::ALLOWED_EXTENSIONS;
    }

    /**
     * Move file from temporary directory to permanent location.
     *
     * @param  string $imageName
     * @return string
     * @throws LocalizedException
     */
    public function moveFileFromTmp(string $imageName): string
    {
        $imageName = ltrim($imageName, '/');
        $baseTmpImagePath = $this->getFilePath($this->baseTmpPath, $imageName);
        $baseImagePath = $this->getFilePath($this->basePath, $imageName);

        // Ensure tmp file exists
        if (!$this->mediaDirectory->isExist($baseTmpImagePath)) {
            throw new LocalizedException(__('Temporary file "%1" does not exist.', $baseTmpImagePath));
        }

        try {
            // Save in DB storage first
            $this->coreFileStorageDatabase->copyFile($baseTmpImagePath, $baseImagePath);

            // Move file physically
            $this->mediaDirectory->renameFile($baseTmpImagePath, $baseImagePath);

        } catch (Exception $e) {
            $this->logger->critical($e);
            throw new LocalizedException(__('Something went wrong while moving the image.'));
        }

        return $imageName;
    }

    /**
     * Save uploaded file to temporary directory and return metadata.
     *
     * @param  string $fileId
     * @return array
     * @throws LocalizedException
     */
    public function saveFileToTmpDir(string $fileId): array
    {
        $uploader = $this->uploaderFactory->create(['fileId' => $fileId]);
        $uploader->setAllowedExtensions($this->allowedExtensions);
        $uploader->setAllowRenameFiles(true);
        $uploader->setFilesDispersion(true);

        $result = $uploader->save($this->mediaDirectory->getAbsolutePath($this->baseTmpPath));

        if (!$result) {
            throw new LocalizedException(__('File cannot be saved to the destination folder.'));
        }

        $result['tmp_name'] = str_replace('\\', '/', $result['tmp_name']);
        $result['path'] = str_replace('\\', '/', $result['path']);
        $result['url'] = $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA)
            . $this->getFilePath($this->baseTmpPath, $result['file']);
        $result['name'] = $result['file'];

        if (isset($result['file'])) {
            try {
                $relativePath = $this->getFilePath($this->baseTmpPath, $result['file']);
                $this->coreFileStorageDatabase->saveFile($relativePath);
            } catch (Exception $e) {
                $this->logger->critical($e);
                throw new LocalizedException(__('Something went wrong while saving the file(s).'));
            }
        }

        return $result;
    }

    /**
     * Save file from temp path to media path if they are different.
     *
     * @param  string $imageName
     * @param  string $imagePath
     * @return string
     * @throws LocalizedException
     */
    public function saveMediaImage(string $imageName, string $imagePath): string
    {
        $baseImagePath = $this->getFilePath($this->basePath, $imageName);
        $mediaPath = substr($imagePath, 0, strpos($imagePath, 'media'));
        $baseTmpImagePath = str_replace($mediaPath . 'media/', '', $imagePath);

        if ($baseImagePath === $baseTmpImagePath) {
            return $imageName;
        }

        try {
            $this->mediaDirectory->copyFile($baseTmpImagePath, $baseImagePath);
        } catch (Exception $e) {
            throw new LocalizedException(__('Something went wrong while saving the file(s).'));
        }

        return $imageName;
    }

    /**
     * Construct full file path from base path and image name.
     *
     * @param  string $path
     * @param  string $imageName
     * @return string
     */
    public function getFilePath(string $path, string $imageName): string
    {
        return rtrim($path, '/') . '/' . ltrim($imageName, '/');
    }

    /**
     * Handle image upload and removal.
     *
     * @param  string      $field
     * @param  array       $data
     * @param  string|null $existingImage
     * @param  string      $basepath
     * @return string
     */
    public function processImage(string $field, array $data, ?string $existingImage, string $basepath): string
    {
        $imageData = $data[$field][0] ?? null;

        // Image removed via admin
        if (isset($imageData['deleted']) && $imageData['deleted'] == 1) {
            $this->deleteImage($existingImage, $basepath);
            return '';
        }

        // New image uploaded
        if (isset($imageData['name']) && isset($imageData['url'])) {
            if ($existingImage && $existingImage !== $imageData['name']) {
                $this->deleteImage($existingImage, $basepath);
            }
            return $this->saveMediaImage($imageData['name'], $imageData['url']);
        }

        // Keep existing
        return $existingImage ?? '';
    }

    /**
     * Delete image from media folder.
     *
     * @param  string $imageName
     * @param  string $basepath
     * @return void
     */
    public function deleteImage(string $imageName, string $basepath): void
    {
        if (!$imageName) {
            return;
        }

        $mediaPath = $basepath . ltrim($imageName, '/');
        if ($this->mediaDirectory->isExist($mediaPath)) {
            $this->mediaDirectory->delete($mediaPath);
        }
    }
}
