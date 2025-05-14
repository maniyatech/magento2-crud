<?php
/**
 * ManiyaTech
 *
 * @author        Milan Maniya
 * @package       ManiyaTech_Crud
 */

namespace ManiyaTech\Crud\Controller\Student;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Directory\Model\RegionFactory;

class Country extends Action
{
    /**
     * @var PageFactory
     */
    protected $_pageFactory;

    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var RegionFactory
     */
    protected $regionFactory;

    /**
     * Constructor
     *
     * @param Context $context
     * @param PageFactory $pageFactory
     * @param JsonFactory $resultJsonFactory
     * @param RegionFactory $regionFactory
     */
    public function __construct(
        Context $context,
        PageFactory $pageFactory,
        JsonFactory $resultJsonFactory,
        RegionFactory $regionFactory
    ) {
        parent::__construct($context);
        $this->_pageFactory = $pageFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->regionFactory = $regionFactory;
    }

    /**
     * Execute the action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        // Example of rendering a page with a custom title
        if ($this->getRequest()->getParam('country')) {
            // Handling AJAX request for state/region data
            return $this->getStateOptions();
        }
    }

    /**
     * Get state options for the selected country
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    private function getStateOptions()
    {
        // Get country code from request
        $countryId = $this->getRequest()->getParam('country');

        // Initialize result JSON
        $result = $this->resultJsonFactory->create();

        // Fetch regions based on the country code
        $regions = $this->regionFactory->create()
            ->getCollection()
            ->addFieldToFilter('country_id', $countryId);

        // Prepare HTML for the options
        $html = '';

        if ($regions->count() > 0) {
            $html .= '<option value="">Please select a region, state or province.</option>';
            foreach ($regions as $state) {
                $html .= '<option value="' . $state->getId() . '">' . $state->getName() . '</option>';
            }
        }

        // Return JSON response
        return $result->setData([
            'success' => true,
            'value'   => $html
        ]);
    }
}
