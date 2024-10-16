<?php
/**
 * Freight Order Info block
 *
 * @category     SoftwareAgil
 * @package      SoftwareAgil_StarkenPro
 * @author       SoftwareAgil (info@softwareagil.com)
 * @copyright    Copyright (c) 2020 SoftwareAgil (www.softwareagil.com)
 */
namespace SoftwareAgil\StarkenPro\Block\Adminhtml\Directory;

use Magento\Framework\View\Element\Template;

class Commune extends Template
{
    public function getBackendUrl($path)
    {
        return $this->_urlBuilder->getUrl($path);
    }
}
