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
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\App\Action\HttpPostActionInterface;

class CheckEmailExistsHyva extends \Magento\Framework\App\Action\Action implements HttpPostActionInterface
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
     * @var Json
     */
    protected $_json;

    /**
     * Constructor
     *
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param CollectionFactory $studentCollectionFactory
     * @param Json $json
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        CollectionFactory $studentCollectionFactory,
        Json $json
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->studentCollectionFactory = $studentCollectionFactory;
        $this->_json = $json;
        parent::__construct($context);
    }

    /**
     * Check if the email is unique.
     */
    public function execute()
    {
        $params = $this->_json->unserialize($this->getRequest()->getContent());
        $email = $categoryId = isset($params['email']) ? $params['email'] : 0;

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
