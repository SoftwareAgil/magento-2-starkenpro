<?php
/**
 * Customer Address Add Custom Attributes To Customer Address plugin
 *
 * @category     SoftwareAgil
 * @package      SoftwareAgil_StarkenPro
 * @author       SoftwareAgil (info@softwareagil.com)
 * @copyright    Copyright (c) 2020 SoftwareAgil (www.softwareagil.com)
 */
declare(strict_types=1);

namespace SoftwareAgil\StarkenPro\Model\Plugin;

use Magento\Framework\Api\AttributeInterface;
use Magento\Framework\Api\AttributeValue;
use SoftwareAgil\StarkenPro\Model\Session;

/**
 * Plugin for converting customer address custom attributes
 */
class AddCustomAttributesToCustomerAddress
{
    /**
     * @var \SoftwareAgil\StarkenPro\Helper\CustomAttribute
     */
    private $customerData;

    /**
     * @var Session
     */
    protected $_saSession;

    /**
     * @param \Magento\CustomerCustomAttributes\Helper\Data $customerData
     * @param Session $customerSession
     */
    public function __construct(
        \SoftwareAgil\StarkenPro\Helper\CustomAttribute $customerData,
        Session $customerSession
    ) {
        $this->customerData = $customerData;
        $this->_saSession = $customerSession;
    }

    /**
     * @param \Magento\Customer\Model\Address $subject
     * @param \Magento\Customer\Api\Data\AddressInterface $customerAddress
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeUpdateData(
        \Magento\Customer\Model\Address $subject,
        \Magento\Customer\Api\Data\AddressInterface $customerAddress
    ) : array {
        $attributes = $this->customerData->getCustomerAddressUserDefinedAttributeCodes();
        $values = $customerAddress->getCustomAttributes();

        $valuesInSession = [];
        $valuesInSession["commune_id"] = $this->_saSession->getSaCommuneId();
        $valuesInSession["commune"] = $this->_saSession->getSaCommune();
        $valuesInSession["rut"] = $this->_saSession->getSaRut();
        $valuesInSession["agency_id"] = $this->_saSession->getSaAgencyId();

        foreach ($attributes as $attribute) {
            if (!isset($values[$attribute])) {
                continue;
            }
            if (!empty($values[$attribute]) && !($values[$attribute] instanceof AttributeInterface)) {
                $valueTemp = $values[$attribute];
                if ($valuesInSession[$attribute]) {
                    $valueTemp = $valuesInSession[$attribute];
                }
                $customerAddress->setCustomAttribute($attribute, $valueTemp);
            } elseif ($values[$attribute] instanceof AttributeInterface) {
                $valueTemp = $values[$attribute]->getValue();
                if ($valuesInSession[$attribute]) {
                    $valueTemp = $valuesInSession[$attribute];
                }
                $customerAddress->setCustomAttribute($attribute, $valueTemp);
            }
        }

        return [$customerAddress];
    }
}
