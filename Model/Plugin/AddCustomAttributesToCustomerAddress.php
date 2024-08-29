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
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @param \Magento\CustomerCustomAttributes\Helper\Data $customerData
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        \SoftwareAgil\StarkenPro\Helper\CustomAttribute $customerData,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->customerData = $customerData;
        $this->_customerSession = $customerSession;
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
        $valuesInSession["commune_id"] = $this->_customerSession->getSaCommuneId();
        $valuesInSession["commune"] = $this->_customerSession->getSaCommune();
        $valuesInSession["rut"] = $this->_customerSession->getSaRut();
        $valuesInSession["agency_id"] = $this->_customerSession->getSaAgencyId();

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
