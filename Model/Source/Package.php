<?php
/**
 * Package source type
 *
 * @category     SoftwareAgil
 * @package      SoftwareAgil_StarkenPro
 * @author       SoftwareAgil (info@softwareagil.com)
 * @copyright    Copyright (c) 2020 SoftwareAgil (www.softwareagil.com)
 */
namespace SoftwareAgil\StarkenPro\Model\Source;

/**
 * Method source
 */
class Package extends Generic
{
    /**
     * Source field code
     *
     * @var string
     */
    protected $code = 'package';


    /**
     * Returns array to be used in multiselect on back-end
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => \SoftwareAgil\StarkenPro\Model\Carrier\AbstractCarrier::PACKAGE_TYPE_DOCUMENT, 'label' => __('Document')],
            ['value' => \SoftwareAgil\StarkenPro\Model\Carrier\AbstractCarrier::PACKAGE_TYPE_PACKAGE, 'label' => __('Package')]
        ];
    }
}
