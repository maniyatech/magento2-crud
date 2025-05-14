<?php
/**
 * ManiyaTech
 *
 * @author        Milan Maniya
 * @package       ManiyaTech_Crud
 */

namespace ManiyaTech\Crud\Controller\Student;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\File\UploaderFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Message\ManagerInterface;
use ManiyaTech\Crud\Model\StudentmanagementFactory;

class Save extends Action
{
    private const MAX_FILE_SIZE = 2097152; // 2MB in bytes
    private const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'svg'];

    /**
     * @var UploaderFactory
     */
    protected $uploaderFactory;

    /**
     * @var DirectoryList
     */
    protected $directoryList;

    /**
     * @var StudentmanagementFactory
     */
    protected $studentFactory;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * Constructor
     *
     * @param Context $context
     * @param UploaderFactory $uploaderFactory
     * @param DirectoryList $directoryList
     * @param StudentmanagementFactory $studentFactory
     * @param ManagerInterface $messageManager
     */
    public function __construct(
        Context $context,
        UploaderFactory $uploaderFactory,
        DirectoryList $directoryList,
        StudentmanagementFactory $studentFactory,
        ManagerInterface $messageManager
    ) {
        $this->uploaderFactory = $uploaderFactory;
        $this->directoryList = $directoryList;
        $this->studentFactory = $studentFactory;
        $this->messageManager = $messageManager;
        parent::__construct($context);
    }

    /**
     * Save
     */
    public function execute()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $data = $this->getRequest()->getParams();
        $profileImage = $this->getRequest()->getFiles('profile');
        $uploadSuccess = false;

        if ($profileImage && $profileImage['tmp_name']) {
            $fileName = $profileImage['name'];
            $fileSize = $profileImage['size'];
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION)); //phpcs:ignore

            if ($fileSize > self::MAX_FILE_SIZE) {
                $this->messageManager->addErrorMessage(__('The file is too large. Maximum allowed size is 2MB.')); //phpcs:ignore
                return $resultRedirect->setUrl($this->_redirect->getRefererUrl());
            }

            if (!in_array($fileExtension, self::ALLOWED_EXTENSIONS)) {
                $this->messageManager->addErrorMessage(__('Invalid file type. Only JPG, JPEG, PNG, GIF and SVG are allowed.')); //phpcs:ignore
                return $resultRedirect->setUrl($this->_redirect->getRefererUrl());
            }

            try {
                $uploadPath = $this->directoryList->getPath(DirectoryList::MEDIA) . '/ManiyaTech/Profile';
                if (!is_dir($uploadPath)) { //phpcs:ignore
                    mkdir($uploadPath, 0777, true); //phpcs:ignore
                }

                $uploader = $this->uploaderFactory->create(['fileId' => 'profile']);
                $uploader->setAllowedExtensions(self::ALLOWED_EXTENSIONS);
                $uploader->setAllowRenameFiles(true);
                $uploader->setFilesDispersion(true);

                $result = $uploader->save($uploadPath);
                $profileFileName = $result['file'];
                $uploadSuccess = true;
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('File upload error: %1', $e->getMessage()));
                return $resultRedirect->setUrl($this->_redirect->getRefererUrl());
            }
        }

        if (!$uploadSuccess) {
            $this->messageManager->addErrorMessage(__('No valid file uploaded.'));
            return $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        }

        try {
            $hobby = isset($data['hobby']) ? implode(",", (array) $data['hobby']) : '';
            $regionId = '';
            $region = '';

            $formType = $data['form_type'] ?? '';
            $regionFromLuma = $data['region'] ?? '';
            $stateFromLuma = $data['state'] ?? '';
            $regionIdFromHyva = $data['region_id'] ?? '';

            if ($formType === 'luma') {
                if (!empty($regionFromLuma)) {
                    $regionId = $regionFromLuma;
                } else {
                    $region = $stateFromLuma;
                }
            } elseif ($formType === 'hyva') {
                if (!empty($regionIdFromHyva)) {
                    $regionId = $regionIdFromHyva;
                } else {
                    $region = $regionFromLuma;
                }
            }
            $student = $this->studentFactory->create();
            $student->addData([
                'name' => $data['name'] ?? '',
                'stud_standard_id' => $data['stud_standard_id'] ?? '',
                'email' => $data['email'] ?? '',
                'hobby' => $hobby,
                'region_id' => $regionId,
                'city' => $data['city'] ?? '',
                'status' => $data['status'] ?? 0,
                'gender' => $data['gender'] ?? '',
                'country_id' => $data['country_id'] ?? '',
                'address' => $data['address'] ?? '',
                'region' => $region,
                'profile' => $profileFileName ?? ''
            ])->save();

            $this->messageManager->addSuccessMessage(__('Student "%1" registered successfully.', $data['name']));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('An error occurred while saving the student: %1', $e->getMessage())); //phpcs:ignore
        }

        return $resultRedirect->setUrl($this->_redirect->getRefererUrl());
    }
}
