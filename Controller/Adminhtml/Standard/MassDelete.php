<?php
/**
 * ManiyaTech
 *
 * @author        Milan Maniya
 * @package       ManiyaTech_Crud
 */

namespace ManiyaTech\Crud\Controller\Adminhtml\Standard;

use Magento\Backend\App\Action;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Io\File;
use ManiyaTech\Crud\Model\ResourceModel\Studentstandard\CollectionFactory;
use ManiyaTech\Crud\Model\ResourceModel\Studentmanagement\CollectionFactory as StudentmanagementFactory;

class MassDelete extends \Magento\Backend\App\Action
{
    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var StudentmanagementFactory
     */
    protected $studentmanagementFactory;

    /**
     * @var File
     */
    protected $fileIo;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * MassDelete constructor.
     *
     * @param Action\Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param StudentmanagementFactory $studentmanagementFactory
     * @param File $fileIo
     * @param Filesystem $filesystem
     */
    public function __construct(
        Action\Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        StudentmanagementFactory $studentmanagementFactory,
        File $fileIo,
        Filesystem $filesystem
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->studentmanagementFactory = $studentmanagementFactory;
        $this->fileIo              = $fileIo;
        $this->filesystem          = $filesystem;
        parent::__construct($context);
    }

    /**
     * Mass Delete Student Standard
     */
    public function execute()
    {
        try {
            $logCollection = $this->filter->getCollection($this->collectionFactory->create());
            $itemsDeleted = 0;
            $mediaDirectory = $this->filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);//phpcs:ignore
            $target = $mediaDirectory->getAbsolutePath('ManiyaTech/Profile/');
            foreach ($logCollection as $item) {
                $standardId = $item->getStandardId();
                // Delete related student management entries
                $studentManagementCollection = $this->studentmanagementFactory->create()
                    ->addFieldToFilter('stud_standard_id', ['eq' => $standardId]);

                foreach ($studentManagementCollection as $student) {
                    $oldImage = $student->getProfile();
                    $deleteImage = $target . $oldImage;
                    if ($mediaDirectory->isExist($deleteImage)) {
                        $this->fileIo->rm($deleteImage);
                    }
                    $student->delete();
                }
                $item->delete();
                $itemsDeleted++;
            }
            $this->messageManager->addSuccessMessage(__('A total of <strong>%1</strong> Student Standard(s) were deleted.', $itemsDeleted)); //phpcs:ignore
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('*/*/');
    }
}
