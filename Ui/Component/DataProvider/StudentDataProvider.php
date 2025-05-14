<?php
/**
 * ManiyaTech
 *
 * @author        Milan Maniya
 * @package       ManiyaTech_Crud
 */

namespace ManiyaTech\Crud\Ui\Component\DataProvider;

use Magento\Framework\Api\Filter;
use Magento\Ui\DataProvider\AbstractDataProvider;
use ManiyaTech\Crud\Model\ResourceModel\Studentmanagement\CollectionFactory;

class StudentDataProvider extends AbstractDataProvider
{
    /**
     * @var CollectionFactory
     */
    protected $employeeCollectionFactory;

    /**
     * StudentDataProvider constructor.
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $employeeCollectionFactory
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $employeeCollectionFactory,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $employeeCollectionFactory->create();
        $this->collection->setOrder('stud_id', 'DESC');
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * Sorting issue fix
     *
     * @param  string $field
     * @param  string $direction
     * @return $this
     */
    public function addOrder($field, $direction)
    {
        $associationTableColumns = [
            'name',
            'email',
            'gender',
            'status',
            'stud_standard_id',
            'created_at'
        ];

        if (in_array($field, $associationTableColumns)) {
            $this->getCollection()->getSelect()->order("main_table.$field $direction");
        } else {
            parent::addOrder($field, $direction);
        }

        return $this;
    }

    /**
     * Filter issue fix
     *
     * @param  Filter $filter
     * @return void
     */
    public function addFilter(Filter $filter)
    {
        if ($filter->getField() === 'hobby') {
            $conditions = [];
            foreach ($filter->getValue() as $value) {
                $conditions[] = ['like' => '%' . $value . '%'];
            }
            $this->getCollection()->addFieldToFilter('main_table.hobby', $conditions);
        } else {
            parent::addFilter($filter);
        }
    }
}
