<?php
/**
 * Address commune attribute source
 *
 * @category     SoftwareAgil
 * @package      SoftwareAgil_StarkenPro
 * @author       SoftwareAgil (info@softwareagil.com)
 * @copyright    Copyright (c) 2020 SoftwareAgil (www.softwareagil.com)
 */
namespace SoftwareAgil\StarkenPro\Model\ResourceModel\Address\Attribute\Source;

class Commune extends \Magento\Eav\Model\Entity\Attribute\Source\Table
{
    /**
     * @var \SoftwareAgil\StarkenPro\Model\ResourceModel\Commune\CollectionFactory
     */
    protected $_communesFactory;

    /**
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory $attrOptionCollectionFactory
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory $attrOptionFactory
     * @param \SoftwareAgil\StarkenPro\Model\ResourceModel\Commune\CollectionFactory $communesFactory
     */
    public function __construct(
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory $attrOptionCollectionFactory,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory $attrOptionFactory,
        \SoftwareAgil\StarkenPro\Model\ResourceModel\Commune\CollectionFactory $communesFactory
    ) {
        $this->_communesFactory = $communesFactory;
        parent::__construct($attrOptionCollectionFactory, $attrOptionFactory);
    }

    /**
     * @inheritdoc
     */
    public function getAllOptions($withEmpty = true, $defaultValues = false)
    {
        if (!$this->_options) {
            $this->_options = $this->_createCommunesCollection()->load()->toOptionArray();
        }
        return $this->_options;
    }

    /**
     * @return \SoftwareAgil\StarkenPro\Model\ResourceModel\Commune\Collection
     */
    protected function _createCommunesCollection()
    {
        return $this->_communesFactory->create();
    }
}
