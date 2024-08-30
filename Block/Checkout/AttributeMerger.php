<?php
/**
 * Cart Layout Processor
 *
 * @category     SoftwareAgil
 * @package      SoftwareAgil_StarkenPro
 * @author       SoftwareAgil (info@softwareagil.com)
 * @copyright    Copyright (c) 2020 SoftwareAgil (www.softwareagil.com)
 */
namespace SoftwareAgil\StarkenPro\Block\Checkout;

use Magento\Customer\Api\CustomerRepositoryInterface as CustomerRepository;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Helper\Address as AddressHelper;
use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Directory\Model\AllowedCountries;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use SoftwareAgil\StarkenPro\Model\Session;
use Magento\Customer\Model\Session as CustomerSession;

/**
 * Fields block attribute merger.
 *
 * @SuppressWarnings(PHPMD.CookieAndSessionMisuse)
 */
class AttributeMerger
{
    /**
     * Map form element
     *
     * @var array
     */
    protected $saFormElementMap = [
        'checkbox'    => 'Magento_Ui/js/form/element/select',
        'select'      => 'Magento_Ui/js/form/element/select',
        'textarea'    => 'Magento_Ui/js/form/element/textarea',
        'multiline'   => 'Magento_Ui/js/form/components/group',
        'multiselect' => 'Magento_Ui/js/form/element/multiselect',
        'image' => 'Magento_Ui/js/form/element/media',
        'file' => 'Magento_Ui/js/form/element/media',
    ];

    /**
     * Map template
     *
     * @var array
     */
    protected $saTemplateMap = [
        'image' => 'media',
        'file' => 'media',
    ];

    /**
     * Map input_validation and validation rule from js
     *
     * @var array
     */
    protected $saInputValidationMap = [
        'alpha' => 'validate-alpha',
        'numeric' => 'validate-number',
        'alphanumeric' => 'validate-alphanum',
        'alphanum-with-spaces' => 'validate-alphanum-with-spaces',
        'url' => 'validate-url',
        'email' => 'email2',
        'length' => 'validate-length',
    ];

    /**
     * @var CustomerInterface
     */
    protected $customer;

    /**
     * List of codes of countries that must be shown on the top of country list
     *
     * @var array
     */
    protected $saTopCountryCodes;

    public function __construct(
        protected AddressHelper $saAddressHelper,
        protected CustomerSession $saCustomerSession,
        protected Session $saSession,
        protected CustomerRepository $saCustomerRepository,
        protected DirectoryHelper $saDirectoryHelper,
        protected ?AllowedCountries $saAllowedCountryReader = null
    ) {
        $this->saTopCountryCodes = $saDirectoryHelper->getTopCountryCodes();
        $this->saAllowedCountryReader =
            $saAllowedCountryReader ?: ObjectManager::getInstance()->get(AllowedCountries::class);
    }

    /**
     * Merge additional address fields for given provider or form
     *
     * @param array $elms
     * @param string $provName name of the storage container used by UI component
     * @param string $dataScopePrefix
     * @param array $fields
     * @return array
     */
    public function merge($elms, $provName, $dataScopePrefix, array $fields = [])
    {
        foreach ($elms as $attrCode => $attrConfig) {
            $additionalConfig = isset($fields[$attrCode]) ? $fields[$attrCode] : [];
            if (!$this->isSaFieldVisible($attrCode, $attrConfig, $additionalConfig)) {
                continue;
            }
            $fields[$attrCode] = $this->getSaFieldConfig(
                $attrCode,
                $attrConfig,
                $additionalConfig,
                $provName,
                $dataScopePrefix
            );
        }
        return $fields;
    }

    /**
     * Retrieve UI field configuration for given attribute
     *
     * @param string $attrCode
     * @param array $attrConfig
     * @param array $additionalConfig field configuration provided via layout XML
     * @param string $provName name of the storage container used by UI component
     * @param string $dataScopePrefix
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function getSaFieldConfig(
        $attrCode,
        array $attrConfig,
        array $additionalConfig,
        $provName,
        $dataScopePrefix
    ) {
        // street attribute is unique in terms of configuration, so it has its own configuration builder
        if (isset($attrConfig['validation']['input_validation'])) {
            $validationRule = $attrConfig['validation']['input_validation'];
            $attrConfig['validation'][$this->saInputValidationMap[$validationRule]] = true;
            unset($attrConfig['validation']['input_validation']);
        }

        if ($attrConfig['formElement'] == 'multiline') {
            return $this->getMultilineFieldConfig($attrCode, $attrConfig, $provName, $dataScopePrefix);
        }

        $uiComponent = isset($this->saFormElementMap[$attrConfig['formElement']])
            ? $this->saFormElementMap[$attrConfig['formElement']]
            : 'Magento_Ui/js/form/element/abstract';
        $elementTemplate = isset($this->saTemplateMap[$attrConfig['formElement']])
            ? 'ui/form/element/' . $this->saTemplateMap[$attrConfig['formElement']]
            : 'ui/form/element/' . $attrConfig['formElement'];

        $element = [
            'component' => isset($additionalConfig['component']) ? $additionalConfig['component'] : $uiComponent,
            'config' => $this->mergeConfigurationNode(
                'config',
                $additionalConfig,
                [
                    'config' => [
                        // customScope is used to group elements within a single
                        // form (e.g. they can be validated separately)
                        'customScope' => $dataScopePrefix,
                        'template' => 'ui/form/field',
                        'elementTmpl' => $elementTemplate,
                    ],
                ]
            ),
            'dataScope' => $dataScopePrefix . '.' . $attrCode,
            'label' => $attrConfig['label'],
            'provider' => $provName,
            'sortOrder' => isset($additionalConfig['sortOrder'])
                ? $additionalConfig['sortOrder']
                : $attrConfig['sortOrder'],
            'validation' => $this->mergeConfigurationNode('validation', $additionalConfig, $attrConfig),
            'options' => $this->getSaFieldOptions($attrCode, $attrConfig),
            'filterBy' => isset($additionalConfig['filterBy']) ? $additionalConfig['filterBy'] : null,
            'customEntry' => isset($additionalConfig['customEntry']) ? $additionalConfig['customEntry'] : null,
            'visible' => isset($additionalConfig['visible']) ? $additionalConfig['visible'] : true,
        ];

        if ($attrCode === 'region_id' || $attrCode === 'country_id') {
            unset($element['options']);
            $element['deps'] = [$provName];
            $element['imports'] = [
                'initialOptions' => 'index = ' . $provName . ':dictionaries.' . $attrCode,
                'setOptions' => 'index = ' . $provName . ':dictionaries.' . $attrCode
            ];
        }

        if (isset($attrConfig['value']) && $attrConfig['value'] != null) {
            $element['value'] = $attrConfig['value'];
        } elseif (isset($attrConfig['default']) && $attrConfig['default'] != null) {
            $element['value'] = $attrConfig['default'];
        } else {
            $defaultValue = $this->getSaDefaultValue($attrCode);
            if (null !== $defaultValue) {
                $element['value'] = $defaultValue;
            }
        }
        return $element;
    }

    /**
     * Merge two configuration nodes recursively
     *
     * @param string $nodeName
     * @param array $mainSource
     * @param array $additionalSource
     * @return array
     */
    protected function mergeConfigurationNode($nodeName, array $mainSource, array $additionalSource)
    {
        $mainData = isset($mainSource[$nodeName]) ? $mainSource[$nodeName] : [];
        $additionalData = isset($additionalSource[$nodeName]) ? $additionalSource[$nodeName] : [];
        return array_replace_recursive($additionalData, $mainData);
    }

    /**
     * Check if address attribute is visible on frontend
     *
     * @param string $attrCode
     * @param array $attrConfig
     * @param array $additionalConfig field configuration provided via layout XML
     * @return bool
     */
    protected function isSaFieldVisible($attrCode, array $attrConfig, array $additionalConfig = [])
    {
        // TODO move this logic to separate model so it can be customized
        if ($attrConfig['visible'] == false
            || (isset($additionalConfig['visible']) && $additionalConfig['visible'] == false)
        ) {
            return false;
        }
        if ($attrCode == 'vat_id' && !$this->saAddressHelper->isVatAttributeVisible()) {
            return false;
        }
        return true;
    }

    /**
     * Retrieve field configuration for street address attribute
     *
     * @param string $attrCode
     * @param array $attrConfig
     * @param string $provName name of the storage container used by UI component
     * @param string $dataScopePrefix
     * @return array
     */
    protected function getMultilineFieldConfig($attrCode, array $attrConfig, $provName, $dataScopePrefix)
    {
        $lines = [];
        unset($attrConfig['validation']['required-entry']);
        for ($lineIndex = 0; $lineIndex < (int)$attrConfig['size']; $lineIndex++) {
            $isFirstLine = $lineIndex === 0;
            $line = [
                'label' => __("%1: Line %2", $attrConfig['label'], $lineIndex + 1),
                'component' => 'Magento_Ui/js/form/element/abstract',
                'config' => [
                    // customScope is used to group elements within a single form e.g. they can be validated separately
                    'customScope' => $dataScopePrefix,
                    'template' => 'ui/form/field',
                    'elementTmpl' => 'ui/form/element/input'
                ],
                'dataScope' => $lineIndex,
                'provider' => $provName,
                'validation' => $isFirstLine
                    // phpcs:ignore Magento2.Performance.ForeachArrayMerge
                    ? array_merge(
                        ['required-entry' => (bool)$attrConfig['required']],
                        $attrConfig['validation']
                    )
                    : $attrConfig['validation'],
                'additionalClasses' => $isFirstLine ? 'field' : 'additional'

            ];
            if ($isFirstLine && isset($attrConfig['default']) && $attrConfig['default'] != null) {
                $line['value'] = $attrConfig['default'];
            }
            $lines[] = $line;
        }
        return [
            'component' => 'Magento_Ui/js/form/components/group',
            'label' => $attrConfig['label'],
            'required' => (bool)$attrConfig['required'],
            'dataScope' => $dataScopePrefix . '.' . $attrCode,
            'provider' => $provName,
            'sortOrder' => $attrConfig['sortOrder'],
            'type' => 'group',
            'config' => [
                'template' => 'ui/group/group',
                'additionalClasses' => $attrCode
            ],
            'children' => $lines,
        ];
    }

    /**
     * Returns default attribute value.
     *
     * @param string $attrCode
     * @throws NoSuchEntityException
     * @throws LocalizedException
     * @return null|string
     */
    protected function getSaDefaultValue($attrCode): ?string
    {
        if ($attrCode === 'country_id') {
            $defaultCountryId = $this->saDirectoryHelper->getDefaultCountry();
            if (!in_array($defaultCountryId, $this->saAllowedCountryReader->getAllowedCountries())) {
                $defaultCountryId = null;
            }
            return $defaultCountryId;
        }

        $customer = $this->getSaCustomer();
        if ($customer === null) {
            return null;
        }

        $attrValue = null;
        switch ($attrCode) {
            case 'prefix':
                $attrValue = $customer->getPrefix();
                break;
            case 'firstname':
                $attrValue = $customer->getFirstname();
                break;
            case 'middlename':
                $attrValue = $customer->getMiddlename();
                break;
            case 'lastname':
                $attrValue = $customer->getLastname();
                break;
            case 'suffix':
                $attrValue = $customer->getSuffix();
                break;
        }

        return $attrValue;
    }

    /**
     * Returns logged customer.
     *
     * @throws NoSuchEntityException
     * @throws LocalizedException
     * @return CustomerInterface|null
     */
    protected function getSaCustomer(): ?CustomerInterface
    {
        if (!$this->customer) {
            if ($this->saCustomerSession->isLoggedIn()) {
                $this->customer = $this->saCustomerRepository->getById($this->saCustomerSession->getCustomerId());
            } else {
                return null;
            }
        }
        return $this->customer;
    }

    /**
     * Retrieve field options from attribute configuration
     *
     * @param mixed $attrCode
     * @param array $attrConfig
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function getSaFieldOptions($attrCode, array $attrConfig)
    {
        return $attrConfig['options'] ?? [];
    }
}
