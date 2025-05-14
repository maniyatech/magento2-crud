<?php
/**
 * ManiyaTech
 *
 * @author        Milan Maniya
 * @package       ManiyaTech_Crud
 */

namespace ManiyaTech\Crud\Controller\Adminhtml\Student\Management;

use Magento\Backend\App\Action;
use Magento\Framework\Filesystem;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\App\Filesystem\DirectoryList;
use ManiyaTech\Crud\Model\ResourceModel\Studentmanagement\CollectionFactory;

class MassDelete extends Action
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
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * MassDelete constructor.
     *
     * @param Action\Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param Filesystem $filesystem
     */
    public function __construct(
        Action\Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        Filesystem $filesystem
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->filesystem          = $filesystem;
        parent::__construct($context);
    }

    /**
     * Mass Delete FAQ Question
     */
    public function execute()
    {
        try {
            $logCollection = $this->filter->getCollection($this->collectionFactory->create());
            $itemsDeleted = 0;
            foreach ($logCollection as $item) {
                $profile = $item->getProfile();
                if ($profile) {
                    $this->deleteImage($profile);
                }
                $item->delete();
                $itemsDeleted++;
            }
            $this->messageManager->addSuccessMessage(__('A total of %1 Student(s) were deleted.', $itemsDeleted));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('*/student_management/index');
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
