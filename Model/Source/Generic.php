<?php
/**
 * Generic source type
 *
 * @category     SoftwareAgil
 * @package      SoftwareAgil_StarkenPro
 * @author       SoftwareAgil (info@softwareagil.com)
 * @copyright    Copyright (c) 2020 SoftwareAgil (www.softwareagil.com)
 */
namespace SoftwareAgil\StarkenPro\Model\Source;

use Magento\Shipping\Model\Carrier\Source\GenericInterface;
use SoftwareAgil\StarkenPro\Model\Carrier;

/**
 * Generic source
 */
class Generic implements GenericInterface
{
    /**
     * @var \SoftwareAgil\StarkenPro\Model\Carrier
     */
    protected $shippingStarkenPro;

    /**
     * Source field code
     *
     * @var string
     */
    protected $code = '';

    /**
     * @param \SoftwareAgil\StarkenPro\Model\Carrier $shippingStarkenPro
     */
    public function __construct(Carrier $shippingStarkenPro)
    {
        $this->shippingStarkenPro = $shippingStarkenPro;
    }

    /**
     * Returns array to be used in multiselect on back-end
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
        $codes = $this->shippingStarkenPro->getCode($this->code);
        if ($codes) {
            foreach ($codes as $code => $title) {
                $options[] = ['value' => $code, 'label' => __($title)];
            }
        }
        return $options;
    }
}
