<?php
/**
 * ManiyaTech
 *
 * @author        Milan Maniya
 * @package       ManiyaTech_Crud
 */

namespace ManiyaTech\Crud\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use ManiyaTech\Crud\Model\ResourceModel\Studentstandard\CollectionFactory;

/**
 * Provides active student standards as options for dropdown/select elements
 */
class StudentStandardOptions implements OptionSourceInterface
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
     * Returns array of options for dropdown/select
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        $options = [];

        $collection = $this->collectionFactory->create()
            ->addFieldToFilter('status', ['eq' => 1])
            ->setOrder('title', 'ASC');

        foreach ($collection as $item) {
            $options[] = [
                'value' => (int) $item->getId(),
                'label' => $item->getTitle(),
            ];
        }

        if (empty($options)) {
            $options[] = [
                'value' => '',
                'label' => __('Please add a standard first.'),
            ];
        }

        return $options;
    }
}
