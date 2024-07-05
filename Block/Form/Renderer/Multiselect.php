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
 * EAV Entity Attribute Form Renderer Block for multiply select
 *
 * @api
 * @author      Magento Core Team <core@magentocommerce.com>
 * @since 100.0.2
 */
class Multiselect extends \SoftwareAgil\StarkenPro\Block\Form\Renderer\Select
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

    /**
     * Return array of values
     *
     * @return array
     */
    public function getValues()
    {
        $value = $this->getValue();
        if (!is_array($value)) {
            $value = explode(',', $value);
        }
        return $value;
    }

    /**
     * Check is value selected
     *
     * @param string $value
     * @return boolean
     */
    public function isValueSelected($value)
    {
        return in_array($value, $this->getValues());
    }
}
