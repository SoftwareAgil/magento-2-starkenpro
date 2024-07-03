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
 * EAV Entity Attribute Form Renderer Block for select
 *
 * @api
 * @author      Magento Core Team <core@magentocommerce.com>
 * @since 100.0.2
 */
class Select extends \SoftwareAgil\StarkenPro\Block\Form\Renderer\AbstractRenderer
{
    /**
     * Return array of select options
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->getAttributeObject()->getSource()->getAllOptions();
    }
}
