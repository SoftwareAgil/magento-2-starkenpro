<?php
/**
 * Admin Clear Log Button block
 *
 * @category     SoftwareAgil
 * @package      SoftwareAgil_StarkenPro
 * @author       SoftwareAgil (info@softwareagil.com)
 * @copyright    Copyright (c) 2020 SoftwareAgil (www.softwareagil.com)
 */

namespace SoftwareAgil\StarkenPro\Block\Adminhtml\Log;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class ClearButton
 * @package SoftwareAgil\StarkenPro\Block\Adminhtml\Log
 */
class ClearButton implements ButtonProviderInterface
{
    /**
     * @var UrlInterface
     */
    protected $_urlBuilder;

    /**
     * ClearButton constructor.
     *
     * @param UrlInterface $urlBuilder
     */
    public function __construct(UrlInterface $urlBuilder)
    {
        $this->_urlBuilder = $urlBuilder;
    }

    /**
     * Retrieve button-specified settings
     *
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label'      => __('Clear old API Calls Log'),
            'class'      => 'primary',
            'on_click'   => 'deleteConfirm(\'' . __(
                'Are you sure you want to clear API Call logs with more than a month old?'
            ) . '\', \'' . $this->_urlBuilder->getUrl('*/*/clear') . '\')',
            'sort_order' => 10,
        ];
    }
}
