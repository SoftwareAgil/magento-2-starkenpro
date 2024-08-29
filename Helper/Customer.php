<?php
/**
 * Customer helper
 *
 * @category     SoftwareAgil
 * @package      SoftwareAgil_StarkenPro
 * @author       SoftwareAgil (info@softwareagil.com)
 * @copyright    Copyright (c) 2020 SoftwareAgil (www.softwareagil.com)
 */
namespace SoftwareAgil\StarkenPro\Helper;

/**
 * Customer EAV Attributes Data Helper
 *
 */
class Customer extends \SoftwareAgil\StarkenPro\Helper\CustomAttributeBase
{
    /**
     * Data helper
     *
     * @var Data $_dataHelper
     */
    protected $_dataHelper;

    /**
     * Input validator
     *
     * @var \SoftwareAgil\StarkenPro\Model\Adminhtml\System\Config\Source\Inputtype\Validator $_inputValidator
     */
    protected $_inputValidator;

    /**
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Framework\Filter\FilterManager $filterManager
     * @param Data $dataHelper
     * @param \SoftwareAgil\StarkenPro\Model\Adminhtml\System\Config\Source\Inputtype\Validator $inputValidator
     */
    public function __construct(
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\Filter\FilterManager $filterManager,
        Data $dataHelper,
        \SoftwareAgil\StarkenPro\Model\Adminhtml\System\Config\Source\Inputtype\Validator $inputValidator
    ) {
        parent::__construct($eavConfig, $localeDate, $filterManager);
        $this->_dataHelper = $dataHelper;
        $this->_inputValidator = $inputValidator;
    }

    /**
     * Default attribute entity type code
     *
     * @return string
     */
    protected function _getEntityTypeCode()
    {
        return 'customer';
    }

    /**
     * Return available customer attribute form as select options
     *
     * @return array
     */
    public function getAttributeFormOptions()
    {
        return [
            ['label' => __('Customer Registration'), 'value' => 'customer_account_create'],
            ['label' => __('Customer Account Edit'), 'value' => 'customer_account_edit'],
            ['label' => __('Admin Checkout'), 'value' => 'adminhtml_checkout']
        ];
    }

    /**
     * Filter post data
     *
     * @param array $data
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return array
     */
    public function filterPostData($data)
    {
        $data = parent::filterPostData($data);

        //validate frontend_input
        if (isset($data['frontend_input'])) {
            $this->_inputValidator->setHaystack(array_keys($this->_dataHelper->getAttributeInputTypes()));
            if (!$this->_inputValidator->isValid($data['frontend_input'])) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __($this->filterManager->stripTags(implode(' ', $this->_inputValidator->getMessages())))
                );
            }
        }
        return $data;
    }
}
