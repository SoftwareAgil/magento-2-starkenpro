<?php
/**
 * Delivery source type
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
class Delivery extends Generic
{
    /**
     * Source field code
     *
     * @var string
     */
    protected $code = 'delivery';


    /**
     * Returns array to be used in multiselect on back-end
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => \SoftwareAgil\StarkenPro\Model\Carrier\AbstractCarrier::DELIVERY_TYPE_AGENCY, 'label' => __('Agency')],
            ['value' => \SoftwareAgil\StarkenPro\Model\Carrier\AbstractCarrier::DELIVERY_TYPE_RESIDENCE, 'label' => __('Residence')]
        ];
    }
}
