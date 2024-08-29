<?php
/**
 * Tracking Info block
 *
 * @category     SoftwareAgil
 * @package      SoftwareAgil_StarkenPro
 * @author       SoftwareAgil (info@softwareagil.com)
 * @copyright    Copyright (c) 2020 SoftwareAgil (www.softwareagil.com)
 */
namespace SoftwareAgil\StarkenPro\Block\Adminhtml\System\Config\Form\Field;

class DebugLogLink extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        return sprintf(
            '<a href ="%s">%s</a> ' . "(" . __('if debug is enabled') . ")",
            rtrim($this->_urlBuilder->getUrl('starkenpro/starken/log'), '/'),
            __('See Log')
        );
    }
}
