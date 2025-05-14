<?php
/**
 * ManiyaTech
 *
 * @author        Milan Maniya
 * @package       ManiyaTech_Crud
 */

namespace ManiyaTech\Crud\Controller\Adminhtml\Student\Management;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use ManiyaTech\Crud\Model\StudentmanagementFactory;
use Magento\Framework\Registry;

class Edit extends \Magento\Backend\App\Action
{
    /**
     * @var Registry
     */
    protected $_coreRegistry;

    /**
     * @var StudentmanagementFactory
     */
    protected $studentmanagementFactory;

    /**
     * Eidt constructor.
     *
     * @param Context $context
     * @param StudentmanagementFactory $studentmanagementFactory
     * @param Registry $registry
     */
    public function __construct(
        Context $context,
        StudentmanagementFactory $studentmanagementFactory,
        Registry $registry
    ) {
        $this->studentmanagementFactory = $studentmanagementFactory;
        $this->_coreRegistry = $registry;
        parent::__construct($context);
    }

    /**
     * Edit FAQ Question
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('ManiyaTech_Crud::main');

        if ($stud_id = (int) $this->getRequest()->getParam('stud_id')) {
            try {
                $studentmanagementFactory = $this->studentmanagementFactory->create()->load($stud_id);
                $this->_coreRegistry->register('student', $studentmanagementFactory);
                $resultPage->getConfig()->getTitle()->prepend(__($studentmanagementFactory->getName()));
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                $this->messageManager->addErrorMessage(__('This record no longer exists.'));
                return $this->_redirect('*/*/');
            }
        } else {
            $resultPage->getConfig()->getTitle()->prepend(__('New Student'));
        }

        return $resultPage;
    }
}
