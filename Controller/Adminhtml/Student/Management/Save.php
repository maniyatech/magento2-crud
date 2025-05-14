<?php
/**
 * ManiyaTech
 *
 * @author        Milan Maniya
 * @package       ManiyaTech_Crud
 */

namespace ManiyaTech\Crud\Controller\Adminhtml\Student\Management;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use ManiyaTech\Crud\Model\StudentmanagementFactory;
use ManiyaTech\Crud\Model\ImageUploader;

/**
 * Class Save
 * Handles saving student management data in admin.
 */
class Save extends Action
{
    private const BASE_PATH = 'ManiyaTech/Profile/';

    /**
     * @var StudentmanagementFactory
     */
    protected StudentmanagementFactory $studentmanagementFactory;

    /**
     * @var ImageUploader
     */
    protected ImageUploader $imageUploader;

    /**
     * @var string
     */
    public string $basePath;

    /**
     * @param Context $context
     * @param StudentmanagementFactory $studentmanagementFactory
     * @param ImageUploader $imageUploader
     */
    public function __construct(
        Context $context,
        StudentmanagementFactory $studentmanagementFactory,
        ImageUploader $imageUploader
    ) {
        parent::__construct($context);
        $this->studentmanagementFactory = $studentmanagementFactory;
        $this->imageUploader = $imageUploader;
        $this->basePath = self::BASE_PATH;
    }

    /**
     * Execute method for saving student data.
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        $id = $this->getRequest()->getParam('stud_id');

        if (!$data) {
            return $resultRedirect->setPath('*/*/');
        }

        try {
            $model = $this->studentmanagementFactory->create();

            if ($id) {
                $model->load($id);
                if (!$model->getId()) {
                    throw new LocalizedException(__('Invalid student ID.'));
                }
            } else {
                unset($data['stud_id']);
            }

            // Convert hobbies to comma-separated string if it's an array
            if (isset($data['hobby']) && is_array($data['hobby'])) {
                $data['hobby'] = implode(',', $data['hobby']);
            }

            // Process profile image
            $data['profile'] = $this->imageUploader->processImage(
                'profile',
                $data,
                $model->getProfile(),
                $this->basePath
            );

            $model->setData($data);
            $model->save();

            $message = $id
                ? __('The student <b>%1</b> has been updated.', $model->getName())
                : __('The student <b>%1</b> has been added.', $model->getName());

            $this->messageManager->addSuccess($message);

            // Redirect based on 'back' button selection
            return match ($this->getRequest()->getParam('back')) {
                'edit' => $resultRedirect->setPath('*/*/edit', ['stud_id' => $model->getId()]),
                default => $resultRedirect->setPath('*/*/')
            };

        } catch (LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addError(__('An error occurred while saving the student.'));
        }

        return $resultRedirect->setPath('*/*/');
    }
}
