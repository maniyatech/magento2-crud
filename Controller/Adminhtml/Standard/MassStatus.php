<?php
/**
 * ManiyaTech
 *
 * @author        Milan Maniya
 * @package       ManiyaTech_Crud
 */

namespace ManiyaTech\Crud\Controller\Adminhtml\Standard;

use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use ManiyaTech\Crud\Model\ResourceModel\Studentstandard\CollectionFactory;
use ManiyaTech\Crud\Model\StudentstandardFactory;

class MassStatus extends \Magento\Backend\App\Action
{
    /**
     * @var Filter
     */
    protected $_filter;

    /**
     * @var CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var StudentstandardFactory
     */
    protected $studentstandardFactory;

    /**
     * MassStatus Constructor
     *
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param StudentstandardFactory $studentstandardFactory
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        StudentstandardFactory $studentstandardFactory
    ) {
        $this->_filter            = $filter;
        $this->_collectionFactory = $collectionFactory;
        $this->studentstandardFactory = $studentstandardFactory;
        parent::__construct($context);
    }

    /**
     * Mass Status Of Deals
     *
     * @return void
     */
    public function execute()
    {
        try {
            $collection = $this->_filter->getCollection($this->_collectionFactory->create());
            $updated = 0;
            foreach ($collection as $item) {
                $model = $this->studentstandardFactory->create()->load($item['standard_id']);
                $model->setData('status', $this->getRequest()->getParam('status'));
                $model->save();
                $updated++;
            }
            $this->messageManager->addSuccess(__('A total of <b>%1 Student Standard Status(s)</b> were updated successfully.', $updated)); // phpcs:ignore
        } catch (Exception $e) {
            $this->messageManager->addError('Something went wrong while deleting the Student Standard '.$e->getMessage()); // phpcs:ignore
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('*/*/');
    }
}
