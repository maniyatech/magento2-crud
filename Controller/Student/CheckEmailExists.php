<?php
/**
 * ManiyaTech
 *
 * @author        Milan Maniya
 * @package       ManiyaTech_Crud
 */

namespace ManiyaTech\Crud\Controller\Student;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use ManiyaTech\Crud\Model\ResourceModel\Studentmanagement\CollectionFactory;

class CheckEmailExists extends \Magento\Framework\App\Action\Action
{
    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var CollectionFactory
     */
    protected $studentCollectionFactory;

    /**
     * Constructor
     *
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param CollectionFactory $studentCollectionFactory
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        CollectionFactory $studentCollectionFactory
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->studentCollectionFactory = $studentCollectionFactory;
        parent::__construct($context);
    }

    /**
     * Check if the email is unique.
     */
    public function execute()
    {
        $email = $this->getRequest()->getParam('email'); // Get the email from the request

        // Check if the email exists in the customer table
        $customerCollection = $this->studentCollectionFactory->create()->addFieldToFilter('email', $email);

        $response = ['exists' => false];

        if ($customerCollection->getSize() > 0) {
            $response['exists'] = true; // Email exists
        }

        // Return the response as JSON
        return $this->resultJsonFactory->create()->setData($response);
    }
}
