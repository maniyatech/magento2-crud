<?php
/**
 * ManiyaTech
 *
 * @author        Milan Maniya
 * @package       ManiyaTech_Crud
 */

namespace ManiyaTech\Crud\Controller\Adminhtml\Student\Management;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use ManiyaTech\Crud\Model\StudentmanagementFactory;

class ValidateEmail extends \Magento\Backend\App\Action
{
    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var StudentmanagementFactory
     */
    protected $studentmanagementFactory;

    /**
     * Constructor
     *
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param StudentmanagementFactory $studentmanagementFactory
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        StudentmanagementFactory $studentmanagementFactory
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->studentmanagementFactory = $studentmanagementFactory;
    }

    /**
     * Check if the email is unique.
     */
    public function execute()
    {
        $email = $this->getRequest()->getParam('email');
        $studId = $this->getRequest()->getParam('stud_id'); // Get the student ID, if it's available for edit
        $result = ['isUnique' => true]; // Assume email is unique initially

        // If a student ID is provided (edit scenario), exclude the current email from the uniqueness check
        $student = $this->studentmanagementFactory->create();
        $existingStudent = $student->getCollection()->addFieldToFilter('email', $email);
        if ($studId) {
            $existingStudent->addFieldToFilter('stud_id', ['neq' => $studId]);
        }
        
        if ($existingStudent->count()) {
            $result['isUnique'] = false;
        }

        // Return the result as a JSON response
        return $this->resultJsonFactory->create()->setData($result);
    }
}
