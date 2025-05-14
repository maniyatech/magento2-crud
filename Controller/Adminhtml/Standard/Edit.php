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
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Registry;
use ManiyaTech\Crud\Model\StudentstandardFactory;

class Edit extends Action
{
    /**
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * @var StudentstandardFactory
     */
    protected $studentstandardFactory;

    /**
     * Edit constructor.
     *
     * @param Context $context
     * @param StudentstandardFactory $studentstandardFactory
     * @param Registry $registry
     */
    public function __construct(
        Context $context,
        StudentstandardFactory $studentstandardFactory,
        Registry $registry
    ) {
        $this->studentstandardFactory = $studentstandardFactory;
        $this->coreRegistry = $registry;
        parent::__construct($context);
    }

    /**
     * Execute method for Edit action
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('ManiyaTech_Crud::main');

        $standardId = (int) $this->getRequest()->getParam('standard_id');
        $studentStandard = $this->studentstandardFactory->create();

        if ($standardId) {
            $studentStandard->load($standardId);

            if (!$studentStandard->getId()) {
                $this->messageManager->addErrorMessage(__('This record no longer exists.'));
                return $this->_redirect('*/*/');
            }

            $resultPage->getConfig()->getTitle()->prepend(__($studentStandard->getTitle()));
        } else {
            $resultPage->getConfig()->getTitle()->prepend(__('New Student Standard'));
        }

        $this->coreRegistry->register('student_standard', $studentStandard);

        return $resultPage;
    }
}
