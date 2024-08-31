<?php
/**
 * Address agency attribute source
 *
 * @category     SoftwareAgil
 * @package      SoftwareAgil_StarkenPro
 * @author       SoftwareAgil (info@softwareagil.com)
 * @copyright    Copyright (c) 2020 SoftwareAgil (www.softwareagil.com)
 */
namespace SoftwareAgil\StarkenPro\Model\ResourceModel\Address\Attribute\Source;

class Agency extends \Magento\Eav\Model\Entity\Attribute\Source\Table
{
    /**
     * @var \SoftwareAgil\StarkenPro\Model\ResourceModel\Agency\CollectionFactory
     */
    protected $_agencyFactory;

    /**
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory $attrOptionCollectionFactory
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory $attrOptionFactory
     * @param \SoftwareAgil\StarkenPro\Model\ResourceModel\Agency\CollectionFactory $agencyFactory
     */
    public function __construct(
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory $attrOptionCollectionFactory,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory $attrOptionFactory,
        \SoftwareAgil\StarkenPro\Model\ResourceModel\Agency\CollectionFactory $agencyFactory
    ) {
        $this->_agencyFactory = $agencyFactory;
        parent::__construct($attrOptionCollectionFactory, $attrOptionFactory);
    }

    /**
     * @inheritdoc
     */
    public function getAllOptions($withEmpty = true, $defaultValues = false)
    {
        if (!$this->_options) {
            $this->_options = $this->_createAgencyCollection()->load()->toOptionArray();
        }
        return $this->_options;
    }

    /**
     * @return \SoftwareAgil\StarkenPro\Model\ResourceModel\Agency\Collection
     */
    protected function _createAgencyCollection()
    {
        return $this->_agencyFactory->create();
    }

    /**
     * Get a text for option value
     *
     * @param string|integer $value
     * @return array|string|bool
     */
    public function getOptionText($value)
    {
        $isMultiple = false;
        if (is_string($value) && strpos($value, ',') !== false) {
            $isMultiple = true;
            $value = explode(',', $value);
        }

        $options = $this->getSpecificOptions($value, false);

        if ($isMultiple) {
            $values = [];
            foreach ($options as $item) {
                if (in_array($item['value'], $value)) {
                    $values[] = $item['label'];
                }
            }
            return $values;
        }

        foreach ($options as $item) {
            if ($item['value'] == $value) {
                return $item['label'];
            }
        }
        return false;
    }

    /**
     * Retrieve Option values array by ids
     *
     * @param string|array $ids
     * @param bool $withEmpty Add empty option to array
     * @return array
     */
    public function getSpecificOptions($ids, $withEmpty = true)
    {
        $options = [];
        $idsArr = (array)$ids;
        $allOptions = $this->getAllOptions(false);
        foreach($allOptions as $elm) {
            if (in_array($elm['value'], $idsArr)) {
                $options[] = $elm;
            }
        }

        return $options;
    }
}
