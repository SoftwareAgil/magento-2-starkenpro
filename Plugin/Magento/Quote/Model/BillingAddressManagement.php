<?php
/**
 * Billing Address Manager plugin
 *
 * @category     SoftwareAgil
 * @package      SoftwareAgil_StarkenPro
 * @author       SoftwareAgil (info@softwareagil.com)
 * @copyright    Copyright (c) 2020 SoftwareAgil (www.softwareagil.com)
 */
namespace SoftwareAgil\StarkenPro\Plugin\Magento\Quote\Model;

class BillingAddressManagement
{
    /**
     * @param \Magento\Quote\Model\BillingAddressManagement $subject
     * @param $cartId
     * \Magento\Quote\Api\Data\AddressInterface $address
     * @return array
     */
    public function beforeAssign(
        \Magento\Quote\Model\BillingAddressManagement $subject,
        $cartId,
        \Magento\Quote\Api\Data\AddressInterface $address
    ) {
        $extAttributes = $address->getExtensionAttributes();
        if (!empty($extAttributes)) {
            try {
                $address->setCommuneId($extAttributes->getCommuneId());
                $address->setCommune($extAttributes->getCommune());
                $address->setRut($extAttributes->getRut());
                $address->setAgencyId($extAttributes->getAgencyId());
            } catch (\Exception $e) {
            }
        }
        return [$cartId, $address];
    }
}
