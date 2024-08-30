<?php
/**
 * @category     SoftwareAgil
 * @package      SoftwareAgil_StarkenPro
 * @author       SoftwareAgil (info@softwareagil.com)
 * @copyright    Copyright (c) 2020 SoftwareAgil (www.softwareagil.com)
 */
namespace SoftwareAgil\StarkenPro\Model\Adminhtml\System\Config\Source\Inputtype;

use Laminas\Validator\InArray;

/**
 * Validator for check input type value
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Validator extends InArray
{
    /**
     * @var \SoftwareAgil\StarkenPro\Helper\Eav
     */
    protected $_saEavData = null;

    protected $saMessageTemplates;

    /**
     * @param \SoftwareAgil\StarkenPro\Helper\Eav $eavData
     * @codeCoverageIgnore
     */
    public function __construct(\SoftwareAgil\StarkenPro\Helper\Eav $eavData)
    {
        $this->_saEavData = $eavData;
        //set data haystack
        $haystack = $this->_saEavData->getInputTypesValidatorData();

        //reset message template and set custom
        $this->saMessageTemplates = null;
        $this->_saInitMessageTemplates();

        //parent construct with options
        parent::__construct(['haystack' => $haystack, 'strict' => true]);
    }

    /**
     * Initialize message templates with translating
     *
     * @return $this
     */
    protected function _saInitMessageTemplates()
    {
        if (!$this->saMessageTemplates) {
            $this->saMessageTemplates = [
                self::NOT_IN_ARRAY => __('Input type "%value%" not found in the input types list.'),
            ];
        }
        return $this;
    }

    /**
     * Add input type to haystack
     *
     * @param string $type
     * @return $this
     */
    public function addInputType($type)
    {
        if (!in_array((string)$type, $this->haystack, true)) {
            $this->haystack[] = (string)$type;
        }
        return $this;
    }
}
