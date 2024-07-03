<?php
/**
 * Address ToOrderAddress plugin
 *
 * @category     SoftwareAgil
 * @package      SoftwareAgil_StarkenPro
 * @author       SoftwareAgil (info@softwareagil.com)
 * @copyright    Copyright (c) 2020 SoftwareAgil (www.softwareagil.com)
 */
namespace SoftwareAgil\StarkenPro\Plugin\Magento\Quote\Model\Quote\Address;

class ToOrderAddress
{
    /**
     * @param \Magento\Quote\Model\Quote\Address\ToOrderAddress $subject
     * @param \Magento\Sales\Api\Data\OrderAddressInterface $orderAddress
     * @param \Magento\Quote\Model\Quote\Address $object
     * @return \Magento\Sales\Api\Data\OrderAddressInterface
     */
    public function afterConvert(
        \Magento\Quote\Model\Quote\Address\ToOrderAddress $subject,
        \Magento\Sales\Api\Data\OrderAddressInterface $orderAddress,
        \Magento\Quote\Model\Quote\Address $object
    ) {
        $orderAddress->setCommuneId($object->getCommuneId());
        $orderAddress->setCommune($object->getCommune());
        $orderAddress->setRut($object->getRut());
        $orderAddress->setAgencyId($object->getAgencyId());
        return $orderAddress;
    }
}
