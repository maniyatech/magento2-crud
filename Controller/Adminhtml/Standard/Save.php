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
use ManiyaTech\Crud\Model\StudentstandardFactory;

class Save extends Action
{
    /**
     * @var StudentstandardFactory
     */
    protected $studentstandardFactory;

    /**
     * Constructor
     *
     * @param Context $context
     * @param StudentstandardFactory $studentstandardFactory
     */
    public function __construct(
        Context $context,
        StudentstandardFactory $studentstandardFactory
    ) {
        parent::__construct($context);
        $this->studentstandardFactory = $studentstandardFactory;
    }

    /**
     * Execute function
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $postData = $this->getRequest()->getPostValue();

        if (!$postData) {
            $this->messageManager->addErrorMessage(__('No data to save.'));
            return $resultRedirect->setPath('*/*/');
        }

        try {
            $model = $this->studentstandardFactory->create();
            if (!empty($postData['standard_id'])) {
                $model->load($postData['standard_id']);
            }
            $model->setTitle($postData['title']);
            $model->setStatus($postData['status']);
            $model->save();
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Something went wrong: %1', $e->getMessage()));
        }

        $resultPageFactory = $this->resultRedirectFactory->create();
        $buttonData = $this->getRequest()->getParam('back');
        if ($buttonData == 'edit' && $model->getId()) {
            $title = $model->getTitle();
            $this->messageManager->addSuccess(__('<strong>%1</strong> standard successfully updated.', $title));
            return $resultPageFactory->setPath('*/*/edit', ['standard_id' => $model->getId()]);
        }
        $this->messageManager->addSuccess(__('<strong>%1</strong> standard successfully saved.', $model->getTitle()));
        return $resultPageFactory->setPath('*/*/');
    }
}
