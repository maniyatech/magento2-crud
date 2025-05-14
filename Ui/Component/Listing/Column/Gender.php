<?php
/**
 * ManiyaTech
 *
 * @author        Milan Maniya
 * @package       ManiyaTech_Crud
 */

namespace ManiyaTech\Crud\Ui\Component\Listing\Column;

class Gender implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * Gender
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
        $options[] = ['label' => 'Male', 'value' => 'male'];
        $options[] = ['label' => 'Female', 'value' => 'female'];
        return $options;
    }
}
