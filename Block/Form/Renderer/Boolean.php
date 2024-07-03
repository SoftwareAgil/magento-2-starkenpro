<?php
/**
 * Customer address form render block
 *
 * @api
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @since 100.0.2
 */
namespace SoftwareAgil\StarkenPro\Block\Form\Renderer;

/**
 * EAV Entity Attribute Form Renderer Block for Boolean
 *
 * @api
 * @author      Magento Core Team <core@magentocommerce.com>
 * @since 100.0.2
 */
class Boolean extends \SoftwareAgil\StarkenPro\Block\Form\Renderer\Select
{
    /**
     * Return array of select options
     *
     * @return array
     */
    public function getOptions()
    {
        return [
            ['value' => '', 'label' => ''],
            ['value' => '0', 'label' => __('No')],
            ['value' => '1', 'label' => __('Yes')]
        ];
    }
}
