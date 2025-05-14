<?php
/**
 * ManiyaTech
 *
 * @author        Milan Maniya
 * @package       ManiyaTech_Crud
 */

namespace ManiyaTech\Crud\Block\Adminhtml\Standard;

use Magento\Backend\Block\Widget\Form\Container;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Registry;

class Edit extends Container
{
    /**
     * @var Registry|null
     */
    protected $_coreRegistry = null;

    /**
     * Edit constructor.
     *
     * @param Context  $context
     * @param Registry $registry
     * @param array    $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Intialize constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId   = 'id';
        $this->_controller = 'adminhtml_standard';
        $this->_blockGroup = 'ManiyaTech_Crud';

        parent::_construct();
        $this->buttonList->update('save', 'label', __('Save'));

        // Save and Continue Edit Button
        $this->buttonList->add(
            'save_and_continue_edit',
            [
                'class'          => 'save',
                'label'          => __('Save and Continue Edit'),
                'data_attribute' => [
                    'mage-init' => [
                        'button' => [
                            'event'  => 'saveAndContinueEdit',
                            'target' => '#edit_form',
                        ],
                    ],
                ],
            ],
            10
        );

        if ($this->getRequest()->getParam('standard_id')) {
            $this->buttonList->remove('reset');
            $this->buttonList->add(
                'delete',
                [
                    'label'   => __('Delete'),
                    'class'   => 'delete',
                    'onclick' => sprintf(
                        'deleteConfirm("%s", "%s")',
                        __('Are you sure you want to delete this item?'),
                        $this->getDeleteUrl()
                    ),
                ],
                20
            );
        }
    }

    /**
     * Get URL for delete action
     *
     * @return string
     */
    public function getDeleteUrl()
    {
        return $this->getUrl('*/*/delete', ['_current' => true]);
    }

    /**
     * Prepared Layout
     *
     * @return Container
     */
    protected function _prepareLayout()
    {
        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('post_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'post_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'post_content');
                }
            };
        ";
        return parent::_prepareLayout();
    }
}
