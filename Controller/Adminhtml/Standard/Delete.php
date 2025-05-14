<?php
/**
 * ManiyaTech
 *
 * @author        Milan Maniya
 * @package       ManiyaTech_Crud
 */

namespace ManiyaTech\Crud\Controller\Adminhtml\Standard;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultInterface;
use Magento\Backend\Model\View\Result\Redirect;
use ManiyaTech\Crud\Model\StudentstandardFactory;
use ManiyaTech\Crud\Model\ResourceModel\Studentmanagement\CollectionFactory;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Io\File;

class Delete extends Action
{
    /**
     * @var StudentstandardFactory
     */
    protected $studentStandardFactory;

    /**
     * @var CollectionFactory
     */
    protected $studentManagementCollectionFactory;

    /**
     * @var File
     */
    protected $fileIo;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * Constructor
     *
     * @param Context $context
     * @param StudentstandardFactory $studentStandardFactory
     * @param CollectionFactory $studentManagementCollectionFactory
     * @param File $fileIo
     * @param Filesystem $filesystem
     */
    public function __construct(
        Context $context,
        StudentstandardFactory $studentStandardFactory,
        CollectionFactory $studentManagementCollectionFactory,
        File $fileIo,
        Filesystem $filesystem
    ) {
        parent::__construct($context);
        $this->studentStandardFactory = $studentStandardFactory;
        $this->studentManagementCollectionFactory = $studentManagementCollectionFactory;
        $this->fileIo              = $fileIo;
        $this->filesystem          = $filesystem;
    }

    /**
     * Execute method to delete Student Standard
     *
     * @return ResultInterface
     */
    public function execute()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $standardId = $this->getRequest()->getParam('standard_id');

        if (!$standardId) {
            $this->messageManager->addErrorMessage(__('We can\'t find a Student Standard to delete.'));
            return $resultRedirect->setPath('*/*/');
        }

        try {
            $studentStandard = $this->studentStandardFactory->create()->load($standardId);
            $mediaDirectory = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
            $target = $mediaDirectory->getAbsolutePath('ManiyaTech/Profile/');

            if (!$studentStandard->getId()) {
                $this->messageManager->addErrorMessage(__('Student Standard no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }

            // Delete related student management entries
            $studentManagementCollection = $this->studentManagementCollectionFactory->create()
                ->addFieldToFilter('stud_standard_id', ['eq' => $standardId]);

            foreach ($studentManagementCollection as $student) {
                $oldImage = $student->getProfile();
                $deleteImage = $target . $oldImage;
                if ($mediaDirectory->isExist($deleteImage)) {
                    $this->fileIo->rm($deleteImage);
                }
                $student->delete();
            }
            $title = $studentStandard->getTitle();
            // Delete the standard record
            $studentStandard->delete();

            $this->messageManager->addSuccess(__('The <strong>%1</strong> Standard has been deleted.', $title));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Error deleting Standard: %1', $e->getMessage()));
            return $resultRedirect->setPath('*/*/edit', ['standard_id' => $standardId]);
        }

        return $resultRedirect->setPath('*/*/');
    }
}
