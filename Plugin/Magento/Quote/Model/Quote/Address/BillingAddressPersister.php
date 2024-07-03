<?php
/**
 * Billing Address Persister plugin
 *
 * @category     SoftwareAgil
 * @package      SoftwareAgil_StarkenPro
 * @author       SoftwareAgil (info@softwareagil.com)
 * @copyright    Copyright (c) 2020 SoftwareAgil (www.softwareagil.com)
 */
namespace SoftwareAgil\StarkenPro\Plugin\Magento\Quote\Model\Quote\Address;

class BillingAddressPersister
{
    protected $logger;

    public function __construct(
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->logger = $logger;
    }

    public function beforeSave(
        \Magento\Quote\Model\Quote\Address\BillingAddressPersister $subject,
        $quote,
        \Magento\Quote\Api\Data\AddressInterface $address,
        $useForShipping = false
    ) {

        $extAttributes = $address->getExtensionAttributes();
        if (!empty($extAttributes)) {
            try {
                $address->setCommuneId($extAttributes->getCommuneId());
                $address->setCommune($extAttributes->getCommune());
                $address->setRut($extAttributes->getRut());
                $address->setAgencyId($extAttributes->getAgencyId());
            } catch (\Exception $e) {
                $this->logger->critical($e->getMessage());
            }
        }
    }
}
