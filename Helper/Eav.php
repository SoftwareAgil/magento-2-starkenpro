<?php
/**
 * @category     SoftwareAgil
 * @package      SoftwareAgil_StarkenPro
 * @author       SoftwareAgil (info@softwareagil.com)
 * @copyright    Copyright (c) 2020 SoftwareAgil (www.softwareagil.com)
 */
namespace SoftwareAgil\StarkenPro\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Eav data helper
 */
class Eav
{
    /**
     * XML path to input types validator data in config
     *
     * @var string
     */
    const XML_PATH_SA_VALIDATOR_DATA_INPUT_TYPES = 'general/validator_data/input_types';

    /**
     * @var array
     */
    protected $_saAttributesLockedFields = [];

    /**
     * @var array
     */
    protected $_saEntityTypeFrontendClasses = [];

    /**
     * @var \Magento\Eav\Model\Entity\Attribute\Config
     */
    protected $_saAttributeConfig;

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $_saEavConfig;

    /**
     * @param \Magento\Eav\Model\Entity\Attribute\Config $attributeConfig
     * @param \Magento\Eav\Model\Config $eavConfig
     * @codeCoverageIgnore
     */
    public function __construct(
        \Magento\Eav\Model\Entity\Attribute\Config $attributeConfig,
        \Magento\Eav\Model\Config $eavConfig,
        protected ScopeConfigInterface $scopeConfig
    ) {
        $this->_saAttributeConfig = $attributeConfig;
        $this->_saEavConfig = $eavConfig;
    }

    /**
     * Return default frontend classes value label array
     *
     * @return array
     */
    protected function _getSaDefaultFrontendClasses()
    {
        return [
            ['value' => '', 'label' => __('None')],
            ['value' => 'validate-number', 'label' => __('Decimal Number')],
            ['value' => 'validate-digits', 'label' => __('Integer Number')],
            ['value' => 'validate-email', 'label' => __('Email')],
            ['value' => 'validate-url', 'label' => __('URL')],
            ['value' => 'validate-alpha', 'label' => __('Letters')],
            ['value' => 'validate-alphanum', 'label' => __('Letters (a-z, A-Z) or Numbers (0-9)')]
        ];
    }

    /**
     * Return merged default and entity type frontend classes value label array
     *
     * @param string $entityTypeCode
     * @return array
     */
    public function getFrontendClasses($entityTypeCode)
    {
        $_saDefaultClasses = $this->_getSaDefaultFrontendClasses();

        if (isset($this->_saEntityTypeFrontendClasses[$entityTypeCode])) {
            return array_merge($_saDefaultClasses, $this->_saEntityTypeFrontendClasses[$entityTypeCode]);
        }

        return $_saDefaultClasses;
    }

    /**
     * Retrieve attributes locked fields to edit
     *
     * @param string $entityTypeCode
     * @return array
     */
    public function getAttributeLockedFields($entityTypeCode)
    {
        if (!$entityTypeCode) {
            return [];
        }
        if (isset($this->_saAttributesLockedFields[$entityTypeCode])) {
            return $this->_saAttributesLockedFields[$entityTypeCode];
        }
        $attributesLockedFields = $this->_saAttributeConfig->getEntityAttributesLockedFields($entityTypeCode);
        if (count($attributesLockedFields)) {
            $this->_saAttributesLockedFields[$entityTypeCode] = $attributesLockedFields;
            return $this->_saAttributesLockedFields[$entityTypeCode];
        }
        return [];
    }

    /**
     * Get input types validator data
     *
     * @return array
     */
    public function getInputTypesValidatorData()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SA_VALIDATOR_DATA_INPUT_TYPES,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Retrieve attribute metadata.
     *
     * @param string $entityTypeCode
     * @param string $attributeCode
     * @return array <pre>[
     *      'entity_type_id' => $entityTypeId,
     *      'attribute_id' => $attributeId,
     *      'attribute_table' => $attributeTable
     *      'backend_type' => $backendType
     * ]</pre>
     */
    public function getAttributeMetadata($entityTypeCode, $attributeCode)
    {
        $attribute = $this->_saEavConfig->getAttribute($entityTypeCode, $attributeCode);
        return [
            'entity_type_id' => $attribute->getEntityTypeId(),
            'attribute_id' => $attribute->getAttributeId(),
            'attribute_table' => $attribute->getBackend()->getTable(),
            'backend_type' => $attribute->getBackendType()
        ];
    }
}
