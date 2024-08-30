<?php
/**
 * Shipping Address Manager plugin
 *
 * @category     SoftwareAgil
 * @package      SoftwareAgil_StarkenPro
 * @author       SoftwareAgil (info@softwareagil.com)
 * @copyright    Copyright (c) 2020 SoftwareAgil (www.softwareagil.com)
 */
namespace SoftwareAgil\StarkenPro\Plugin\Magento\Quote\Model;

use SoftwareAgil\StarkenPro\Model\Session;

class ShippingAddressManagement
{
    /**
     * @param Session $customerSession
     */
    public function __construct(
        protected Session $_saSession
    ) {
    }

    /**
     * @param \Magento\Quote\Model\ShippingAddressManagement $subject
     * @param $cartId
     * \Magento\Quote\Api\Data\AddressInterface $address
     * @return array
     */
    public function beforeAssign(
        \Magento\Quote\Model\ShippingAddressManagement $subject,
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
                $this->_saSession->setSaCommuneId($extAttributes->getCommuneId());
                $this->_saSession->setSaCommune($extAttributes->getCommune());
                $this->_saSession->setSaRut($extAttributes->getRut());
                $this->_saSession->setSaAgencyId($extAttributes->getAgencyId());
            } catch (\Exception $e) {
            }
        }
        return [$cartId, $address];
    }
}
