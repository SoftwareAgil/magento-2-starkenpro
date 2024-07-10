<?php
/**
 * Abstract carrier
 *
 * @category     SoftwareAgil
 * @package      SoftwareAgil_StarkenPro
 * @author       SoftwareAgil (info@softwareagil.com)
 * @copyright    Copyright (c) 2020 SoftwareAgil (www.softwareagil.com)
 */
namespace SoftwareAgil\StarkenPro\Model\Carrier;

use Magento\Shipping\Model\Carrier\AbstractCarrierOnline;

/**
 * Class AbstractCarrier
 *
 * @api
 * @since 100.0.2
 */
abstract class AbstractCarrier extends \Magento\Shipping\Model\Carrier\AbstractCarrier implements \Magento\Shipping\Model\Carrier\CarrierInterface
{
    public $_customizableContainerTypes = [];
    /**
     * Container types that could be customized
     *
     * @var string[]
     */
    const PACKAGE_TYPE_DOCUMENT = 'SOBRE';
    const PACKAGE_TYPE_PACKAGE = 'BULTO';
    const DELIVERY_TYPE_AGENCY = 'AGENCIA';
    const DELIVERY_TYPE_AGENCY2 = 'AGENCY';
    const DELIVERY_TYPE_RESIDENCE = 'DOMICILIO';
    const PAYMENT_TYPE_ON_ORIGIN = 2;
    const PAYMENT_TYPE_ON_ARRIVAL = 3;

    /**
     * StarkenPro method
     */
    const METHOD_NORMAL = 'NORMAL';
    const METHOD_EXPRESS = 'EXPRESO';
}
