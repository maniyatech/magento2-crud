<?php
/**
 * ManiyaTech
 *
 * @author        Milan Maniya
 * @package       ManiyaTech_Crud
 */

namespace ManiyaTech\Crud\Block;

use Magento\Framework\View\Element\Template;
use ManiyaTech\Crud\Model\ResourceModel\Studentstandard\CollectionFactory;
use Magento\Directory\Block\Data as DirectoryData;
use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Directory\Model\ResourceModel\Country\CollectionFactory as CountryCollectionFactory;

class Main extends \Magento\Framework\View\Element\Template
{
    /**
     * @var CollectionFactory
     */
    protected $studentstandardFactory;

    /**
     * @var DirectoryData
     */
    protected $directoryBlock;
    
    /**
     * @var CountryCollectionFactory
     */
    protected $_countryCollectionFactory;

    /**
     * @var DirectoryHelper
     */
    protected $directoryHelper;

    /**
     * Main constructor.
     *
     * @param Template\Context $context
     * @param CollectionFactory $studentstandardFactory
     * @param DirectoryData $directoryBlock
     * @param DirectoryHelper $directoryHelper
     * @param CountryCollectionFactory $countryCollectionFactory
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        CollectionFactory $studentstandardFactory,
        DirectoryData $directoryBlock,
        DirectoryHelper $directoryHelper,
        CountryCollectionFactory $countryCollectionFactory,
        array $data = []
    ) {
        $this->studentstandardFactory = $studentstandardFactory;
        $this->directoryBlock = $directoryBlock;
        $this->directoryHelper = $directoryHelper;
        $this->_countryCollectionFactory = $countryCollectionFactory;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve active student standards.
     *
     * @return \Magento\Directory\Model\ResourceModel\Country\CollectionFactory
     */
    public function getStudentstandards()
    {
        return $this->studentstandardFactory->create()->addFieldToFilter('status', '1');
    }

    /**
     * Generate HTML select element for countries.
     *
     * @return string
     */
    public function getCountries()
    {
        return $this->directoryBlock->getCountryHtmlSelect();
    }

    /**
     * Generate HTML select element for regions.
     *
     * @return string
     */
    public function getRegion()
    {
        return $this->directoryBlock->getRegionHtmlSelect();
    }

    /**
     * Get URL for country action.
     *
     * @return string
     */
    public function getCountryAction()
    {
        return $this->getUrl('*/student/country');
    }

    /**
     * Retrieve country collection for the current store.
     *
     * @return \Magento\Directory\Model\ResourceModel\Country\Collection
     */
    public function getCountryCollection()
    {
        return $this->_countryCollectionFactory->create()->loadByStore();
    }

    /**
     * Retrieve selected or default country ID.
     *
     * @return string
     */
    public function getCountryId()
    {
        $countryId = $this->getData('country_id');
        if ($countryId === null) {
            $countryId = $this->directoryHelper->getDefaultCountry();
        }
        return $countryId;
    }
}
