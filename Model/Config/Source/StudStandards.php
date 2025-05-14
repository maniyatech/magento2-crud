<?php
/**
 * ManiyaTech
 *
 * @author        Milan Maniya
 * @package       ManiyaTech_Crud
 */

namespace ManiyaTech\Crud\Model\Config\Source;

use ManiyaTech\Crud\Model\ResourceModel\Studentstandard\CollectionFactory;

class StudStandards implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var CollectionFactory
     */
    private CollectionFactory $collectionFactory;

    /**
     * Constructor
     *
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(CollectionFactory $collectionFactory)
    {
        $this->collectionFactory = $collectionFactory;
    }
    
    /**
     * Returns array of options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];

        $collection = $this->collectionFactory->create()->addFieldToFilter('status', ['eq' => 1]);
        foreach ($collection as $item) {
            $options[] = [
                'value' => $item->getId(),
                'label' => $item->getTitle(),
            ];
        }

        return $options;
    }
}
