<?php
/**
 * ManiyaTech
 *
 * @author        Milan Maniya
 * @package       ManiyaTech_Crud
 */

namespace ManiyaTech\Crud\Model\ResourceModel\Studentmanagement;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * stud_id variable
     *
     * @var string
     */
    protected $_idFieldName = 'stud_id';
    
    /**
     * Initialize construct
     */
    public function _construct()
    {
        $this->_init(
            \ManiyaTech\Crud\Model\Studentmanagement::class,
            \ManiyaTech\Crud\Model\ResourceModel\Studentmanagement::class
        );
    }
}
