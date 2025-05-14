<?php
/**
 * ManiyaTech
 *
 * @author        Milan Maniya
 * @package       ManiyaTech_Crud
 */

namespace ManiyaTech\Crud\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Gender implements OptionSourceInterface
{
    /**
     * Return options for Gender radio button field
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'male', 'label' => __('Male')],
            ['value' => 'female', 'label' => __('Female')]
        ];
    }
}
