<?php
/**
 * ManiyaTech
 *
 * @author        Milan Maniya
 * @package       ManiyaTech_Crud
 */

namespace ManiyaTech\Crud\Block\Adminhtml\Standard\Edit;

use Magento\Backend\Block\Widget\Tabs as WidgetTabs;

class Tabs extends WidgetTabs
{
    /**
     * Construct method
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('standard_edit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Student Standard Information'));
    }

    /**
     * Before html
     *
     * @return WidgetTabs
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _beforeToHtml()
    {
        $this->addTab(
            'standard_edit_tabs',
            [
                'label'   => __('General'),
                'title'   => __('General'),
                'content' => $this->getLayout()->createBlock(\ManiyaTech\Crud\Block\Adminhtml\Standard\Edit\Tab\Info::class)->toHtml(),//phpcs:ignore
                'active'  => true,
            ]
        );
        return parent::_beforeToHtml();
    }
}
