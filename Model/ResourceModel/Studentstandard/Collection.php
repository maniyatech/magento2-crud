<?php
/**
 * ManiyaTech
 *
 * @author        Milan Maniya
 * @package       ManiyaTech_Crud
 */

namespace ManiyaTech\Crud\Model\ResourceModel\Studentstandard;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * standard_id variable
     *
     * @var string
     */
    protected $_idFieldName = 'standard_id';
    
    /**
     * Initialize construct
     */
    public function _construct()
    {
        $this->_init(
            \ManiyaTech\Crud\Model\Studentstandard::class,
            \ManiyaTech\Crud\Model\ResourceModel\Studentstandard::class
        );
    }
}
