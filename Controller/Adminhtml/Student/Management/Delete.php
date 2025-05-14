<?php
/**
 * ManiyaTech
 *
 * @author        Milan Maniya
 * @package       ManiyaTech_Crud
 */

namespace ManiyaTech\Crud\Controller\Adminhtml\Student\Management;

use Magento\Backend\App\Action;
use ManiyaTech\Crud\Model\StudentmanagementFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;

class Delete extends Action
{
    /**
     * @var StudentmanagementFactory
     */
    protected $studentmanagementFactory;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * Delete constructor.
     *
     * @param Action\Context $context
     * @param StudentmanagementFactory $studentmanagementFactory
     * @param Filesystem $filesystem
     */
    public function __construct(
        Action\Context $context,
        StudentmanagementFactory $studentmanagementFactory,
        Filesystem $filesystem
    ) {
        $this->studentmanagementFactory = $studentmanagementFactory;
        $this->filesystem          = $filesystem;
        parent::__construct($context);
    }

    /**
     * Delete action
     *
     * @return ResultInterface
     */
    public function execute()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('stud_id');
        if ($id) {
            try {
                $studentData = $this->studentmanagementFactory->create()->load($id);
                $name = $studentData->getName();
                $profile = $studentData->getProfile();
                if ($profile) {
                    $this->deleteImage($profile);
                }
                $studentData->delete();
                $this->messageManager->addSuccess(__('You deleted the <b>%1</b> student.', $name));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                return $resultRedirect->setPath('*/student_management/edit', ['stud_id' => $id]);
            }
        }
        $this->messageManager->addError(__('We can\'t find a Student to delete.'));
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Delete image from media folder
     *
     * @param string|null $imageName
     */
    protected function deleteImage(string $imageName): void
    {
        if (!$imageName) {
            return;
        }

        $mediaPath = 'ManiyaTech/Profile/' . ltrim($imageName, '/');
        $mediaDirectory = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);

        if ($mediaDirectory->isExist($mediaPath)) {
            $mediaDirectory->delete($mediaPath);
        }
    }
}
