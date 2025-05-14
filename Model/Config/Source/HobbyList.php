<?php
/**
 * ManiyaTech
 *
 * @author        Milan Maniya
 * @package       ManiyaTech_Crud
 */

namespace ManiyaTech\Crud\Model\Config\Source;

class HobbyList implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Return options for Hobbies field
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'cricket', 'label' => __('Playing Cricket')],
            ['value' => 'movie', 'label' => __('Watching Movie')],
            ['value' => 'reading', 'label' => __('Reading book')],
            ['value' => 'swimming', 'label' => __('Swimming')]
        ];
    }
}
