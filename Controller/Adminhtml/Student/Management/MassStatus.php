<?php
/**
 * ManiyaTech
 *
 * @author        Milan Maniya
 * @package       ManiyaTech_Crud
 */

namespace ManiyaTech\Crud\Controller\Adminhtml\Student\Management;

use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use ManiyaTech\Crud\Model\ResourceModel\Studentmanagement\CollectionFactory;
use ManiyaTech\Crud\Model\StudentmanagementFactory;

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
     * @var StudentmanagementFactory
     */
    protected $studentmanagementFactory;

    /**
     * MassStatus Constructor
     *
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param StudentmanagementFactory $studentmanagementFactory
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        StudentmanagementFactory $studentmanagementFactory
    ) {
        $this->_filter            = $filter;
        $this->_collectionFactory = $collectionFactory;
        $this->studentmanagementFactory = $studentmanagementFactory;
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
                $model = $this->studentmanagementFactory->create()->load($item['stud_id']);
                $model->setData('status', $this->getRequest()->getParam('status'));
                $model->save();
                $updated++;
            }
            $this->messageManager->addSuccess(__('A total of <b>%1 Student Status(s)</b> were updated successfully.', $updated)); // phpcs:ignore
        } catch (Exception $e) {
            $this->messageManager->addError('Something went wrong while deleting the Student '.$e->getMessage()); // phpcs:ignore
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('*/*/');
    }
}
