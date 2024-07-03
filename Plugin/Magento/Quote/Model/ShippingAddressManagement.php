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

class ShippingAddressManagement
{

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;


    /**
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession
    )
    {
        $this->_customerSession = $customerSession;
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
                $this->_customerSession->setSaCommuneId($extAttributes->getCommuneId());
                $this->_customerSession->setSaCommune($extAttributes->getCommune());
                $this->_customerSession->setSaRut($extAttributes->getRut());
                $this->_customerSession->setSaAgencyId($extAttributes->getAgencyId());
            } catch (\Exception $e) {
            }
        }
        return [$cartId, $address];
    }
}
