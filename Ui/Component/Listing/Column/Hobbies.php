<?php
/**
 * ManiyaTech
 *
 * @author        Milan Maniya
 * @package       ManiyaTech_Crud
 */

namespace ManiyaTech\Crud\Ui\Component\Listing\Column;
 
class Hobbies extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * Datasource function
     *
     * @param  array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        $fieldName = $this->getData('name');
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $item[$fieldName] = $item['hobby'];
            }
        }
        return $dataSource;
    }
}
